<?php

namespace App\Modules\Plugins;

use App\Modules\CQHttp\Events\CQMessageEvent;

class RollPlugin extends OnMessagePlugin
{
    protected $commands = [
        'roll (.*)' => 'rollNum',
        'roll' => 'roll',
    ];
    protected $listen = '*';

    public function rollNum(CQMessageEvent $e, $text)
    {
        if (!is_numeric($text)) {
            return $e->reply('点数不是一个合法的数字！');
        }
        $e->reply($e->getSenderId() . '摇到的点数是：' . rand(0, $text));
    }

    public function roll(CQMessageEvent $e)
    {
        $this->rollNum($e, 100);
    }
}
