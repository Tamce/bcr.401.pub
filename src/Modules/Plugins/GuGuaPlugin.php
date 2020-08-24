<?php

namespace App\Modules\Plugins;

use App\Modules\CQHttp\Events\CQMessageEvent;
use App\Modules\CQHttp\CQHttp;

class GuGuaPlugin extends OnMessagePlugin
{
    protected $commands = [
        '^.*七夕.*' => 'toad',
        '^.*\[CQ:at,qq=2789594061\].*什么功能.*' => 'gugua',
        '^.*\[CQ:at,qq=2789594061\].*能干啥.*' => 'gugua',
        '^.*\[CQ:at,qq=2789594061\].*test.*' => 'test',
    ];
    protected $listen = '*';

    public function test(CQMessageEvent $e)
    {
        $e->reply('test');
    }

    public function toad(CQMessageEvent $e)
    {
        return $e->reply('大家好，我是你们朋友点的七夕蛤蟆');
    }

    public function gugua(CQMessageEvent $e)
    {
        $cq = CQHttp::instance();
        sleep(2);
        $cq->sendMessage($e->getMessageType(), $e->getMessageSourceId(), '我要开始叫了');
        sleep(2);
        $cq->sendMessage($e->getMessageType(), $e->getMessageSourceId(), '孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡');
        sleep(5);
        $cq->sendMessage($e->getMessageType(), $e->getMessageSourceId(), '孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡孤寡');
    }
}
