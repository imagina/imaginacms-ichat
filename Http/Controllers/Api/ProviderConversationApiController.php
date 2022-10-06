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

class ProviderConversationApiController extends BaseApiController
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
  public function create(Request $request)
  {
    \DB::beginTransaction();
    try {
      $response = ["data" => []];
      $data = $request->input('attributes') ?? [];//Get data
      \Log::info("[Provider-Message]::Create" . json_encode($data));
      //Validate Request
      $this->validateRequestApi(new CreateProviderMessageRequest($data));
      //Validate Provider
      $provider = Provider::where('system_name', $data['provider'])->first();
      if (!$provider) throw new Exception('Provider not found', 500);
      //Get the user provider
      $user = $this->getUserProvider($data);
      $data["users"] = [$user->id];
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
          "medias_single" => $fileMessage ? ["attachment" => $fileMessage->id] : []
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
    $uploadedFile = getUploadedFileFromUrl($data["file"]);
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

  /** Return chat file */
  public function getFile($fileId)
  {
    $file = File::where("filename", $fileId)->first();
    return \Storage::disk("privatemedia")->response($file->path->getRelativeUrl());
  }
}
