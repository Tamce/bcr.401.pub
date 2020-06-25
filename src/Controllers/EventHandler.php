<?php
namespace App\Controllers;

use App\Modules\CQHttp\CQEvent;
use App\Modules\CQHttp\CQGroupMessageEvent;
use App\Modules\CQHttp\CQHttp;
use App\Modules\CQHttp\CQPrivateMessageEvent;
use App\Modules\Plugins\EchoPlugin;
use Illuminate\Http\Request;

class EventHandler
{
    public function handle(Request $request, CQHttp $cq)
    {
        $botId = $request->header('X-Self-ID', '0');
        $sig = $request->header('X-Signature');

        $plugins = explode(',', $_ENV['PLUGINS']);
        foreach ($plugins as $plugin) {
            $class = 'App\\Modules\\Plugins\\'.trim($plugin);
            (new $class)->register($cq);
        }
        return json($cq->dispatchEventFromArray($request->input()));
    }
}