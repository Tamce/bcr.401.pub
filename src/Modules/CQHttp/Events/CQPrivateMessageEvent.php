<?php
namespace App\Modules\CQHttp\Events;

class CQPrivateMessageEvent extends CQMessageEvent
{
    public function getMessageType()
    {
        return 'user';
    }

    public function getMessageSourceId()
    {
        return $this->getSenderId();
    }

}