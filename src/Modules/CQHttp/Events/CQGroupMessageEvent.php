<?php
namespace App\Modules\CQHttp\Events;

use Illuminate\Contracts\Validation\ImplicitRule;

class CQGroupMessageEvent extends CQMessageEvent
{
    public function getMessageType()
    {
        return 'group';
    }

    public function getMessageSourceId()
    {
        return $this->getGroupId();
    }


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
