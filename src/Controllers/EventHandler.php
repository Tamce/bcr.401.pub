<?php
namespace App\Controllers;

use App\Modules\CQHttp\CQEvent;
use App\Modules\CQHttp\CQGroupMessageEvent;
use App\Modules\CQHttp\CQHttp;
use App\Modules\CQHttp\CQPrivateMessageEvent;
use Illuminate\Http\Request;

class EventHandler
{
    public function handle(Request $request, CQHttp $cq)
    {
        $botId = $request->header('X-Self-ID', '0');
        $sig = $request->header('X-Signature');

        $cq->listen('message.group', function (CQGroupMessageEvent $e) {
            if ($e->getMessage() == '%ping') {
                $e->reply('pong!');
            }
        });
        $cq->listen('message.private', function (CQPrivateMessageEvent $e) {
            if ($e->getMessage() == '%ping') {
                $e->reply('pong@');
            }
        });
        $cq->listen('message.*', function ($name, $e) {
            $e[0]->reply('!');
        });
        return $cq->dispatchEventFromArray($request->input());
    }
}