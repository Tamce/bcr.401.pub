<?php
namespace App\Modules\Plugins;

use App\Models\Image;
use App\Modules\CQHttp\Events\CQEvent;
use Illuminate\Support\Facades\DB;

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