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

    public function getSenderInfoArray($key = null)
    {
        if (empty($key)) {
            return $this->rawData('sender');
        }
        return @$this->rawData('sender')[$key];
    }

    public function getSenderNickname()
    {
        return $this->getSenderInfoArray('nickname');
    }

    public function getSenderName()
    {
        return $this->getSenderInfoArray('card') ?? $this->getSenderInfoArray('nickname');
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

    public function isGroupMessage()
    {
        return $this->getMessageType() == 'group';
    }

    public function isPrivateMessage()
    {
        return $this->getMessageType() == 'user';
    }
}
