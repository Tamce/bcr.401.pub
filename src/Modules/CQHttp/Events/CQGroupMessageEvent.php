<?php
namespace App\Modules\CQHttp\Events;

class CQGroupMessageEvent extends CQPrivateMessageEvent
{
    public function getGroupId()
    {
        return $this->rawData('group_id');
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
