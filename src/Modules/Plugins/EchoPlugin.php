<?php
namespace App\Modules\Plugins;

use App\Modules\CQHttp\CQEvent;

class EchoPlugin extends OnMessagePlugin
{
    protected $commands = [
        'echo (.*)' => 'echoBack',
    ];
    protected $listen = '*';

    public function echoBack(CQEvent $e, $text)
    {
        $e->reply($text);
    }
}