<?php

namespace Modules\Ichat\Services;

use Modules\Notification\Entities\Provider;

class ProviderService
{
    /**
     * @var string|Provider[object]
     */
    private $provider;

    public function getTemplateWhatsapp($template)
    {
        try {
            // Request to the Facebook API to retrieve all templates
            $fileResponse = \Cache::remember('ichatTemplatesWhatsapp', 86400, function () {
                $provider = Provider::where("system_name", "whatsapp")->first();
                $client = new \GuzzleHttp\Client();

                // Request to Facebook API
                $response = $client->request('GET',
                    "https://graph.facebook.com/v17.0/{$provider->fields->businessAccountId}/message_templates",
                    ['headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => "Bearer {$provider->fields->accessToken}",
                    ]]
                );
                $response = json_decode($response->getBody()->getContents());
                return $response;
            });

            // Find the template based on name and language
            $findTemplate = collect($fileResponse->data)
                ->where('name', $template["name"])
                ->where('language', $template["language"])
                ->first();

            $response = ["body" => ''];
            //Loop components of template
            foreach ($findTemplate->components as $component) {
                // Modify format of component body

                // If exist files in header component
                if (isset($component->format) && $component->format !== "TEXT") {
                    $type = strtolower($component->type);
                    $param = collect($template["components"])->where('type', $type)->first()["parameters"][0];

                    // Save the link to the file
                    $response["file"] = $param[$param["type"]]["link"];
                } else {
                    // Validate if it is one of the following types
                    if (in_array($component->type, ["HEADER", "BODY", "FOOTER"])) {
                        // Add dynamic values in the body
                        $response["body"] .= $this->findAndAddDynamicComponentsTemplate($component->type, $component->text, $template["components"]);
                    } else {
                        $type = strtolower($component->type);
                        $response["body"] .= "$type: ";
                        // Concatenate the 'text' columns of the template's buttons
                        $response["body"] .= implode(' | ', array_column($component->$type, "text")) . " ";
                    }
                }
            }

            return $response;
        } catch (\Exception $e) {
            \Log::error("[Ichat]::GetTemplate | Error: " . $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine() . $e->getTraceAsString());
        }
    }

    // Add dynamic Components in  Template
    private function findAndAddDynamicComponentsTemplate($type, $bodyText, $dynamicValues)
    {
        $type = strtolower($type);

        // Verify if exist dynamic values
        if (empty($dynamicValues)) {
            return $bodyText;
        }

        // Get the dynamic parameters of that component
        $params = collect($dynamicValues)->where('type', $type)->first();

        if (empty($params)) {
            return $bodyText;
        }

        $params = $params["parameters"];

        // Replace all values enclosed within {{ n }}
        $formattedText = preg_replace_callback('/\{\{(\d+)\}\}/', function ($matches) use ($params) {
            // Gets the number n from the match and subtracts 1 to obtain a valid index
            $numero = intval($matches[1]) - 1;

            // Checks if an element exists in the $params array with the index $number
            if (isset($params[$numero]) && isset($params[$numero]["text"])) {
                // Returns the value of "text" as a replacement
                return $params[$numero]["text"];
            } else {
                // Returns the original match ({{n}})
                return $matches[0];
            }
        }, $bodyText);

        // If the type is 'header', add bold formatting
        $formattedText = ($type === 'header') ? "**$formattedText**\n" : "$formattedText\n";

        return $formattedText;
    }


    public function getInteractiveWhatsapp($interactive)
    {
        try {
            $response = ["body" => ''];

            foreach ($interactive as $propKey => $propValue) {
                // Check if the $propKey is not 'type' or 'action'
                if ($propKey !== 'type' && $propKey !== 'action') {
                    if ($propKey === 'header') {
                        // If the $propKey is 'header', get the type and corresponding text
                        $type = $propValue["type"];
                        $text = $propValue[$type];

                        // If the type is 'text', add bold formatting to the response body
                        if ($type === 'text') $response["body"] .= "**$text**\n";
                        else $response["file"] = $propValue[$type]["link"];
                    } else {
                        // For other keys, simply add the text to the response body
                        $text = $propValue["text"];
                        $response["body"] .= "$text\n";
                    }
                } else if ($propKey === 'action') {
                    // Handle only 'buttons' and 'button' cases

                    // Check if buttons exist
                    if (isset($propValue["buttons"])) {
                        $response["body"] .= "Buttons: \n";

                        // Add button titles to the response body
                        foreach ($propValue["buttons"] as $button) {
                            $text = $button["reply"]["title"];
                            $response["body"] .= "$text\n";
                        }
                    } else if (isset($propValue["button"])) {
                        // Checks if the key 'button' exists, indicating an interactive list-type message

                        $text = $propValue["button"];
                        $response["body"] .= "$text:\n";

                        // Add row descriptions to the response body
                        foreach ($propValue["sections"][0]["rows"] as $row) {
                            $text = $row["description"];
                            $response["body"] .= "$text:\n";
                        }
                    }
                }
            }

            return $response;

        } catch (\Exception $e) {
            \Log::error("[Ichat]::GetInteractive | Error: " . $e->getMessage() . "\n" . $e->getFile() . "\n" . $e->getLine() . $e->getTraceAsString());
        }
    }
}
