<?php

namespace Modules\Ichat\Entities;


class Status
{
    const SENT = 1;
    const DELIVERED = 2;
    const READ = 3;
    const FAILED = 4;
    const OBTAINED = 5;


    private $statuses = [];

    public function __construct()
    {
        $this->statuses = [
            self::SENT => trans('ichat::messages.status.sent'),
            self::DELIVERED => trans('ichat::messages.status.delivered'),
            self::READ => trans('ichat::messages.status.read'),
            self::FAILED => trans('ichat::messages.status.failed'),
            self::OBTAINED => trans('ichat::messages.status.obtained')
        ];
    }

    public function lists()
    {
        return $this->statuses;
    }


    public function get($statusId)
    {
        if (isset($this->statuses[$statusId])) {
            return $this->statuses[$statusId];
        }

        return $this->statuses[self::FAILED];
    }

    public function index()
    {
        //Instance response
        $response = [];
        //AMp status
        foreach ($this->statuses as $key => $status) {
            array_push($response, ['id' => $key, 'title' => $status]);
        }
        //Repsonse
        return collect($response);
    }
}