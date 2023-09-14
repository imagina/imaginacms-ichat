<?php

namespace Modules\Ichat\Entities;


class Status
{
    const SENT = 1;
    const DELIVERED = 2;
    const READ = 3;
    const OBTAINED = 4;
    const TIMEEXCEEDED = 5;
    const MEDIADOWNLOADERROR = 6;
    const INCORRECTTEMPLATEPARAM = 7;
    const TEMPLATENOTFOUND = 8;
    const TEMPLATEFORMATTINGERROR = 9;
    const TEMPLATEPAUSED = 10;
    const TEMPLATEDISABLED = 11;
    const TIMEOUT = 12;
    const UNKNOWNERROR = 13;
    const MESSAGENOTSENT = 14;
    const INCOMPATIBLEMSGTYPE = 15;
    const MEDIAUPLOADERROR = 16;
    const SERVERUNAVAILABLE = 17;
    const UNIDENTIFIEDERROR = 18;


    private $statuses = [];

    public function __construct()
    {
        $this->statuses = [
            self::SENT => trans('ichat::messages.status.sent'),
            self::DELIVERED => trans('ichat::messages.status.delivered'),
            self::READ => trans('ichat::messages.status.read'),
            self::OBTAINED => trans('ichat::messages.status.obtained'),
            self::TIMEEXCEEDED => trans('ichat::messages.status.timeExceeded'),
            self::MEDIADOWNLOADERROR => trans('ichat::messages.status.mediaDownloadError'),
            self::INCORRECTTEMPLATEPARAM => trans('ichat::messages.status.incorrectTemplateParam'),
            self::TEMPLATENOTFOUND => trans('ichat::messages.status.templateNotFound'),
            self::TEMPLATEFORMATTINGERROR => trans('ichat::messages.status.templateFormattingError'),
            self::TEMPLATEPAUSED => trans('ichat::messages.status.templatePaused'),
            self::TEMPLATEDISABLED => trans('ichat::messages.status.templateDisabled'),
            self::TIMEOUT => trans('ichat::messages.status.timeout'),
            self::UNKNOWNERROR => trans('ichat::messages.status.unknownError'),
            self::MESSAGENOTSENT => trans('ichat::messages.status.messageNotSent'),
            self::INCOMPATIBLEMSGTYPE => trans('ichat::messages.status.incompatibleMsgType'),
            self::SERVERUNAVAILABLE => trans('ichat::messages.status.serverUnavailable'),
            self::UNIDENTIFIEDERROR => trans('ichat::messages.status.unidentifiedError')
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