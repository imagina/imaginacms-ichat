<?php

namespace Modules\Ichat\Repositories\Eloquent;

use Modules\Ichat\Repositories\ConversationRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class EloquentConversationRepository extends EloquentBaseRepository implements ConversationRepository
{
  public function create($data)
  {
    //Validate if conversation already exist
    $conversation = $this->model->whereHas('users', function ($q) use ($data) {
      $q->select('conversation_id')->whereIn('user_id', $data['users'])->groupBy('conversation_id')
        ->having(\DB::raw('count(*)'), '=', count($data['users']));
    })->with('users')->first();

    //Create conversation
    if (!$conversation) {
      $conversation = $this->model->create($data);
      //Sync Users relation
      if ($conversation) {
        $conversation->users()->sync(Arr::get($data, 'users', []));//Sync users
        $conversation = $this->getItem($conversation->id, (object)["include" => ["users"]]); //Get model with user relation
      }
    }

    //Response
    return $conversation;
  }

  public function getItemsBy($params)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();

    // RELATIONSHIPS
    if (in_array('*', $params->include)) {//If Request all relationships
      $query->with([]);
    } else {//Especific relationships
      $includeDefault = ['users','conversationUsers'];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }

    // FILTERS
    if ($params->filter) {
      $filter = $params->filter;

      //Filter by date
      if (isset($filter->date)) {
        $date = $filter->date;//Short filter date
        $date->field = $date->field ?? 'created_at';
        if (isset($date->from))//From a date
          $query->whereDate($date->field, '>=', $date->from);
        if (isset($date->to))//to a date
          $query->whereDate($date->field, '<=', $date->to);
      }

      //Order by
      if (isset($filter->order)) {
        $orderByField = $filter->order->field ?? 'created_at';//Default field
        $orderWay = $filter->order->way ?? 'desc';//Default way
        $query->orderBy($orderByField, $orderWay);//Add order to query
      }
    }

    //Validate permission index all
    if (!isset($params->permissions["ichat.conversations.index-all"]) ||
      !$params->permissions["ichat.conversations.index-all"]) {
      $query->wherehas('users', function ($query) {
        $query->where('user_id', Auth::id());
      });
    }

    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);
    /*== REQUEST ==*/
    if (isset($params->page) && $params->page) {
      return $query->paginate($params->take);
    } else {
      $params->take ? $query->take($params->take) : false;//Take
      return $query->get();
    }
  }

  public function getItem($criteria, $params = false)
  {
    //Initialize query
    $query = $this->model->query();

    /*== RELATIONSHIPS ==*/
    if (in_array('*', $params->include)) {//If Request all relationships
      $query->with([]);
    } else {//Especific relationships
      $includeDefault = ['users','conversationUsers'];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }

    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;

      if (isset($filter->field))//Filter by specific field
        $field = $filter->field;
    }

    //Limit to current user
    if (!isset($params->permissions["ichat.conversations.index-all"]) ||
      !$params->permissions["ichat.conversations.index-all"]) {
      $query->wherehas('users', function ($query) {
        $query->where('user_id', Auth::id());
      });
    }

    /*== FIELDS ==*/
    if (isset($params->fields) && count($params->fields))
      $query->select($params->fields);

    /*== REQUEST ==*/
    return $query->where($field ?? 'id', $criteria)->first();
  }

  public function updateBy($criteria, $data, $params = false)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      //Update by field
      if (isset($filter->field))
        $field = $filter->field;
    }
    /*== REQUEST ==*/
    $model = $query->where($field ?? 'id', $criteria)->first();
    return $model ? $model->update((array)$data) : false;
  }

  public function deleteBy($criteria, $params = false)
  {
    /*== initialize query ==*/
    $query = $this->model->query();
    /*== FILTER ==*/
    if (isset($params->filter)) {
      $filter = $params->filter;
      if (isset($filter->field))//Where field
        $field = $filter->field;
    }
    /*== REQUEST ==*/
    $model = $query->where($field ?? 'id', $criteria)->first();
    $model ? $model->delete() : false;
  }

}
