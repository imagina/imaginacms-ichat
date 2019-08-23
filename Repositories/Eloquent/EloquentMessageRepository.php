<?php

namespace Modules\Ichat\Repositories\Eloquent;

use Modules\Ichat\Repositories\MessageRepository;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Illuminate\Support\Facades\Auth;

class EloquentMessageRepository extends EloquentBaseRepository implements MessageRepository
{
  public function getItemsBy($params)
  {
    // INITIALIZE QUERY
    $query = $this->model->query();
    /*== RELATIONSHIPS ==*/
    if (in_array('*', $params->include)) {//If Request all relationships
      $query->with([]);
    } else {//Especific relationships
      $includeDefault = [];//Default relationships
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

      // Filter by conversation
      if (isset($filter->conversation)) {
        $query->where('conversation_id', $filter->conversation);
      }

      // Filter by user
      if (isset($filter->user)) {
        $query->where('user_id', $filter->user);
      }

      // Filter by isSeen
      if (isset($filter->isSeen)) {
        $query->where('is_seen', $filter->isSeen);
      }

      // Filter bfor Get Thread Conversation
      if(isset($filter->type) && isset($filter->user)){
        if($filter->type == 'thread'){
          $query->where(['sender_id'=> auth()->id(), 'receiver_id'=> $filter->user])
            ->orWhere(function($query) use($filter){
              $query->where(['sender_id' => $filter->user, 'receiver_id' => auth()->id()]);
            });
        }
      }
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
    // INITIALIZE QUERY
    $query = $this->model->query();
    /*== RELATIONSHIPS ==*/
    if (in_array('*', $params->include)) {//If Request all relationships
      $query->with([]);
    } else {//Especific relationships
      $includeDefault = [];//Default relationships
      if (isset($params->include))//merge relations with default relationships
        $includeDefault = array_merge($includeDefault, $params->include);
      $query->with($includeDefault);//Add Relationships to query
    }
    /*== FIELDS ==*/
    if (is_array($params->fields) && count($params->fields))
      $query->select($params->fields);
    /*== FILTER ==*/
    if (isset($params->filter)) {

    }
    /*== REQUEST ==*/
    return $query->first();
  }
  public function create($data)
  {
    $data['sender_id'] = Auth::user()->id;
    $message = $this->model->create($data);
    return $message;
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
