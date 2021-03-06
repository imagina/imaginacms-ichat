<?php

namespace Modules\Ichat\Http\Controllers\Api;

// Requests & Response
use Modules\Ichat\Http\Requests\CreateConversationRequest;
use Modules\Ichat\Http\Requests\UpdateConversationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

// Base Api
use Modules\Ihelpers\Http\Controllers\Api\BaseApiController;

// Transformers
use Modules\Ichat\Transformers\ConversationTransformer;

// Entities
use Modules\Ichat\Entities\Conversation;

// Repositories
use Modules\Ichat\Repositories\ConversationRepository;

use Illuminate\Support\Facades\Auth;

class ConversationApiController extends BaseApiController
{
  private $conversation;
  public function __construct(ConversationRepository $conversation)
  {
    $this->conversation = $conversation;
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
      $data = $this->conversation->getItemsBy($params);
      //Response
      $response = ["data" => ConversationTransformer::collection($data)];
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
      $data = $this->conversation->getItem($criteria, $params);
      //Break if no found item
      if (!$data) throw new Exception('Item not found', 204);
      //Response
      $response = ["data" => new ConversationTransformer($data)];
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
   * CREATE A ITEM
   *
   * @param Request $request
   * @return mixed
   */
  public function create(Request $request)
  {
    \DB::beginTransaction();
    try {
      $data = $request->input('attributes') ?? [];//Get data

      // init instace of Request
      $newRequest = new Request();
      // build filters
      $newRequest->merge(["filter" => json_encode(["between" => $data['users']])]);
      // Get params of request
      $params = $this->getParamsRequest($newRequest);
      // Use method getItemsBy of eloquent conversation repo for get equals conversations
      $ifexistConversation = $this->conversation->getItemsBy($params);

      // If exist as minimum 1 conversation equals, that is return
      if (count($ifexistConversation) > 0){
        $conversation = new ConversationTransformer($ifexistConversation[0]);
      } else {
        //Validate Request
        $this->validateRequestApi(new CreateConversationRequest($data));
        //Create item
        $conversation = new ConversationTransformer($this->conversation->create($data));
      }

      //Response
      $response = ["data" => $conversation];
      \DB::commit(); //Commit to Data Base
    } catch (\Exception $e) {
      \DB::rollback();//Rollback to Data Base
      $status = $this->getStatusError($e->getCode());
      $response = ["errors" => $e->getMessage()];
    }
    //Return response
    return response()->json($response ?? ["data" => "Request successful"], $status ?? 200);
  }

  /**
   * Update the specified resource in storage.
   * @param  Request $request
   * @return Response
   */
  public function update($criteria, Request $request)
  {
    \DB::beginTransaction();
    try {
      $params = $this->getParamsRequest($request);
      $data = $request->input('attributes');
      //Validate Request
      $this->validateRequestApi(new UpdateConversationRequest($data));
      //Update data
      $this->conversation->updateBy($criteria, $data, $params);
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
      $this->conversation->deleteBy($criteria, $params);
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

}
