<?php

namespace App\Modules\Plugins;

use App\Modules\CQHttp\Events\CQMessageEvent;

class RollPlugin extends OnMessagePlugin
{
    protected $commands = [
        'roll (.*)' => 'rollNum',
        'roll' => 'roll',
        'random (.*)' => 'random',
    ];
    protected $listen = '*';

    public function random(CQMessageEvent $e, $text)
    {
        $list = collect(explode(' ', $text));
        if ($list->count() == 1) {
            return $e->reply('就一个你还 rand？？？');
        }
        return $e->reply($e->getSenderId() . '摇到了：' . $list->random());
    }

    public function rollNum(CQMessageEvent $e, $text)
    {
        if (!is_integer($text)) {
            return $e->reply('点数不是一个合法的整数！');
        }
        return $e->reply($e->getSenderId() . '摇到的点数是：' . rand(0, $text));
    }

    public function roll(CQMessageEvent $e)
    {
        $this->rollNum($e, 100);
    }
}
