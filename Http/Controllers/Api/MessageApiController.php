<?php

namespace Modules\Ichat\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;
use Modules\Ichat\Repositories\MessageRepository;
use Modules\Ichat\Entities\Provider;
use Modules\Ichat\Transformers\MessageTransformer;
use Modules\Ichat\Http\Requests\CreateMessageRequest;
use Modules\Ichat\Http\Requests\UpdateMessageRequest;
use Modules\Media\Entities\File;
use Illuminate\Support\Facades\Auth;


class MessageApiController extends BaseApiController
{
  private $message;

  public function __construct(MessageRepository $message)
  {
    $this->message = $message;
  }

  /**
   * CREATE A ITEM
   *
   * @param Request $request
   * @return mixed
   */
  public function create(Request $request)
  {

    try {
      $data = $request->input('attributes') ?? [];//Get data

      //Validate Request
      $this->validateRequestApi(new CreateMessageRequest($data));
      //Create item
      $message = $this->message->create($data);
      //Emit message to provider
      $this->emitMessageForProvider($message);
      //Response
      $response = ["data" => collect(new MessageTransformer($message))->put("frontId", $data["front_id"] ?? null)];

    } catch (\Exception $e) {
     \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * GET ITEMS
   *
   * @return mixed
   */
  public function index(Request $request)
  {
    try {
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);
      //Request to Repository
      $data = $this->message->getItemsBy($params);
      //Response
      $response = ["data" => MessageTransformer::collection($data)];
      //If request pagination add meta-page
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($data)] : false;
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * GET A ITEM
   *
   * @param $criteria
   * @return mixed
   */
  public function show($criteria, Request $request)
  {
    try {
      //Get Parameters from URL.
      $params = $this->getParamsRequest($request);
      //Request to Repository
      $data = $this->message->getItem($criteria, $params);
      //Break if no found item
      if (!$data) throw new Exception('Item not found', 204);
      //Response
      $response = ["data" => new MessageTransformer($data)];
      //If request pagination add meta-page
      $params->page ? $response["meta"] = ["page" => $this->pageTransformer($data)] : false;
    } catch (\Exception $e) {
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * Update the specified resource in storage.
   * @param Request $request
   * @return Response
   */
  public function update($criteria, Request $request)
  {
    \DB::beginTransaction();
    try {
      $params = $this->getParamsRequest($request);
      $data = $request->input('attributes');
      //Validate Request
      $this->validateRequestApi(new UpdateMessageRequest($data));
      //Update data
      $this->message->updateBy($criteria, $data, $params);
      //Response
      $response = ['data' => 'Item Updated'];
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    return response()->json($response, $status ?? 200);
  }

  /**
   * Remove the specified resource from storage.
   * @return Response
   */
  public function delete($criteria, Request $request)
  {
    \DB::beginTransaction();
    try {
      //Get params
      $params = $this->getParamsRequest($request);
      //Delete data
      $this->message->deleteBy($criteria, $params);
      //Response
      $response = ['data' => ''];
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    return response()->json($response, $status ?? 200);
  }

  /** Emit message for providers*/
  public function emitMessageForProvider($message)
  {
    try {
      //Search if conversation is of the provider
      $provider = Provider::where("name", $message->conversation->entity_type ?? "null")->first();

      //Emit message
      if ($provider) {
        //Instance the message type
        $messageType = "text";
        //Get message attachment
        if ($message->attached) {
          $file = $message->files()->where('zone', 'attachment')->first();
          //Send url to get file
          $messagaAttachment = \URL::route("api.ichat.external.file.get", ["fileId" => $file->filename]);
          //Instance the message type
          $messageType = $file->isImage() ? "image" : ($file->extension == "mp3" ? "audio" : "document");
        }

        //Send request
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST',
          $provider->end_point,
          //'https://nflow.imaginacolombia.com/webhook-test/14bc8ef6-c757-4c62-8301-b4b6f355ce60',
          [
            'body' => json_encode([
              "attributes" => [
                "provider" => $provider->name,
                "type" => $messageType,
                "message" => $message->body,
                "conversationId" => $message->conversation->entity_id,
                "file" => $messagaAttachment ?? null
              ]
            ]),
            'headers' => [
              'Content-Type' => 'application/json',
              'Authorization' => $provider->token,
            ]
          ]
        );
        //Log
        \Log::info("[send-message-provider]:: Emit message to provider - Type: {$messageType} - status code: " . $response->getStatusCode());
      }
    } catch (\Exception $e) {
      \Log::info("[send-message-provider]::Error " . $e->getMessage());
    }
  }
}
