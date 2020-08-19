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
        return $e->reply($e->getSenderName() . '摇到了：' . trim($list->random()));
    }

    public function rollNum(CQMessageEvent $e, $text)
    {
        $dices = explode(' ', trim($text));
        $result = [];
        foreach ($dices as $n) {
            if (!is_numeric($n)) {
                return $e->reply('点数不是一个合法的整数！');
            }
            $result[] = rand(1, $n);
        }
        return $e->reply($e->getSenderName() . '摇到的点数是：' . implode(', ', $result));
    }

    public function roll(CQMessageEvent $e)
    {
        $this->rollNum($e, 100);
    }
}
