<?php
namespace App\Modules\CQHttp;

class CQGroupMessageEvent extends CQEvent
{
    public function getSubType($type = null)
    {
        if (is_null($type))
            return $this->rawData('sub_type');
        else
            return $this->rawData('sub_type') == $type;
    }

    public function getGroupId()
    {
        return $this->rawData('group_id');
    }

    public function getSenderId()
    {
        return $this->rawData('user_id');
    }

    public function getMessage()
    {
        return $this->rawData('message');
    }

    public function getRawMessage()
    {
        return $this->rawData('raw_message');
    }

    public function atSender($bool = null)
    {
        if (is_null($bool)) {
            return $this->response['at_sender'] ?? true;
        }

        $this->response['at_sender'] = $bool;
        return $this;
    }
    
}