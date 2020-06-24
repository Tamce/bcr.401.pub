<?php
namespace App\Modules\Plugins;

use App\Modules\CQHttp\CQEvent;

class EchoPlugin extends OnMessagePlugin
{
    protected $commands = [
        'echo (.*)' => 'echoBack',
        'ping' => 'ping',
    ];
    protected $listen = '*';

    public function echoBack(CQEvent $e, $text)
    {
        $e->reply($text);
    }

    public function ping(CQEvent $e)
    {
        $e->reply('pong');
    }
}