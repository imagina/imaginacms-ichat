<?php

namespace Modules\Ichat\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\CountValidator\Exception;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Ichat\Repositories\ConversationRepository;
use Modules\Ichat\Repositories\MessageRepository;
use Modules\Notification\Entities\Provider;
use Modules\Media\Entities\File;
use Modules\User\Repositories\UserRepository;
use Modules\User\Entities\Sentinel\User;
use Modules\Ichat\Transformers\MessageTransformer;
use Modules\Ichat\Http\Requests\CreateProviderMessageRequest;
use Modules\Media\Services\FileService;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProviderApiController extends BaseApiController
{
  private $userEntity;
  private $userRepository;
  private $conversation;
  private $messageRepository;

  public function __construct(
    ConversationRepository $conversation,
    User                   $userEntity,
    UserRepository         $userRepository,
    MessageRepository      $messageRepository
  )
  {
    $this->userEntity = $userEntity;
    $this->userRepository = $userRepository;
    $this->conversation = $conversation;
    $this->messageRepository = $messageRepository;
  }

  /**
   * CREATE A ITEM
   *
   * @param Request $request
   * @return mixed
   */
  public function manage(Request $request)
  {
    \DB::beginTransaction();
    try {
      $response = ["data" => []];
      $data = $request->input('attributes') ?? [];//Get data
      \Log::info("[Provider-Message]::Creating - " . json_encode($data));
      //Validate Request
      $this->validateRequestApi(new CreateProviderMessageRequest($data));
      //Validate Provider
      $provider = Provider::where('system_name', $data['provider'])->first();
      if (!$provider) throw new Exception('Provider not found', 500);
      //Get the user provider
      $user = $this->getUserProvider($data);
      if ($user) {
        $data["users"] = [$user->id];//Set conversation user
        if (!\Auth::id()) \Auth::loginUsingId($user->id);//Authenticated user
      }
      //Get the conversation
      $conversation = $this->getConversation($data);
      $response["data"]['conversation'] = $conversation;
      //Validate if exist a file
      $fileMessage = $this->getMessageFile($data);
      //Insert the message
      if (isset($data["message"]) || $fileMessage) {
        $message = $this->messageRepository->create([
          "conversation_id" => $conversation->id,
          "user_id" => $user->id,
          "body" => $data["message"] ?? "",
          "attached" => $fileMessage ? $fileMessage->id : null,
          "medias_single" => $fileMessage ? ["attachment" => $fileMessage->id] : [],
          "created_at" => $data["created_at"] ?? Carbon::now()
        ]);
        $response["data"]['message'] = $message;
      }
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /** Return the user provider */
  private function getUserProvider($data)
  {
    //Instance de user email
    $email = "{$data["conversation_id"]}@{$data["provider"]}.com";
    //Validate if exist
    $user = $this->userEntity->where("email", $email)->first();
    //Create user if not exist
    if (!$user) {
      //Instance the user data
      $userData = [
        "email" => $email,
        "first_name" => $data["first_name"] ?? $data["conversation_id"],
        "last_name" => $data["last_name"] ?? $data["provider"],
        "password" => $this->generatePassword()
      ];
      //Create user
      $user = $this->userRepository->createWithRoles($userData, [], true);
    }
    //Return response
    return $user;
  }

  /** Insert file if exist and return the file entity */
  private function getMessageFile($data)
  {
    //Validate if exist
    if (!isset($data["file"]) || !$data["file"]) return null;
    //Instance file service
    $fileService = app("Modules\Media\Services\FileService");
    //Get base64 file
    $uploadedFile = getUploadedFileFromUrl($data["file"], ($data["file_context"] ?? []));
    //Create file
    return $fileService->store($uploadedFile, 0, 'privatemedia');
  }

  /** Return the conversation*/
  private function getConversation($data)
  {
    //Search by a conversation
    $conversation = $this->conversation->where("entity_type", $data['provider'])
      ->where("entity_id", $data["conversation_id"])->first();
    //Create the conversation if not exist
    if (!$conversation) {
      //Instance the conversation data
      $conversationData = [
        "private" => 0,
        "entity_type" => $data["provider"],
        "entity_id" => $data["conversation_id"],
        "users" => $data["users"]
      ];
      //Create the conversation data
      $conversation = $this->conversation->create($conversationData);
    }
    //Return the conversation
    return $conversation;
  }

  /** Validate Webhook of provider */
  public function validateWebhook($provider, Request $request)
  {
    //Validate webhook
    switch ($provider) {
      case "whatsapp":
        $responseToken = $request->query("hub_challenge") ?? 0;
        return response()->json((int)$responseToken, 200);
        break;
    }

    //Default abort 404
    return abort(404);
  }

  /** Handle provider Webhook */
  public function handleWebhook($providerName, Request $request)
  {
    try {
      //Get provider data
      $provider = Provider::where('system_name', $providerName)->first();
      //Validate the provider
      if (!$provider || !$provider->status || !isset($provider->fields))
        throw new Exception("Provider '{$providerName}' not found", 400);
      //Parse the message by provider
      $data = $this->{"get" . Str::title($providerName) . "Message"}($request->all(), $provider);
      //Manage message
      if ($data) {
        $response = $this->validateResponseApi($this->manage(new Request([
          "attributes" => array_merge(["provider" => $providerName], $data)
        ])));
      }
      //Log info
      \Log::info("[handleWebhook]::{$provider} Success");
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
      \Log::info("[handleWebhook]::{$provider}. Error -> " . $e->getMessage());
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /** Return the Whatsapp messages data  */
  private function getWhatsappMessage($data, $provider)
  {
    try {
      //Get attributes from the message
      $contact = $data["entry"][0]["changes"][0]["value"]["contacts"][0] ?? null;
      $message = $data["entry"][0]["changes"][0]["value"]["messages"][0] ?? null;
      //Validate message
      if (!$message) return null;
      //Get date entry message
      /*$messageDate = $message["timestamp"] ?? null;
      if ($messageDate) {
        $dateTmp = new \DateTime();
        $dateTmp->setTimestamp($messageDate);
        $messageDate = $dateTmp->format("Y-m-d H:m:s");
      }*/
      //Instance the response
      $response = [
        "conversation_id" => $message["from"],
        "first_name" => $contact["profile"]["name"] ?? null,
        "message" => $message["text"]["body"] ?? $message["button"]["text"] ?? null
        //"created_at" => $messageDate
      ];
      //Get file
      if (in_array($message["type"], ["video", "document", "image", "audio", "stiker"])) {
        //Request File url
        $client = new \GuzzleHttp\Client();
        $fileResponse = $client->request('GET',
          "https://graph.facebook.com/v15.0/{$message[$message["type"]]["id"]}",
          ['headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$provider->fields->accessToken}",
          ]]
        );
        $fileResponse = json_decode($fileResponse->getBody()->getContents());
        //Set file to response
        $response["file"] = $fileResponse->url;
        //Set file request context
        $response["file_context"] = [
          'header' => "User-Agent: php/7 \r\n" .
            "Authorization: Bearer {$provider->fields->accessToken}"
        ];
        //Replace the message text by the file caption
        $response["message"] = $message[$message["type"]]["caption"] ?? null;
      }
      //Response
      return $response;
    } catch (\Exception $e) {
      throw new Exception('whatsappBusiness::Issue parsing the message: ' . $e->getMessage(), 500);
    }
  }
}
