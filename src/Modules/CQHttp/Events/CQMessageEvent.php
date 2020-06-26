<?php
namespace App\Modules\CQHttp\Events;

abstract class CQMessageEvent extends CQEvent
{
    abstract public function getMessageType();
    abstract public function getMessageSourceId();

    public function getSenderId()
    {
        return $this->rawData('user_id');
    }

    public function getSubType($type = null)
    {
        if (is_null($type))
            return $this->rawData('sub_type');
        else
            return $this->rawData('sub_type') == $type;
    }

    public function getMessage()
    {
        return $this->rawData('message');
    }

    public function getRawMessage()
    {
        return $this->rawData('raw_message');
    }
}