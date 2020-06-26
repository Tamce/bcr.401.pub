<?php
namespace App\Controllers;

use App\Models\Image;
use App\Modules\CQHttp\CQEvent;
use App\Modules\CQHttp\CQGroupMessageEvent;
use App\Modules\CQHttp\CQHttp;
use App\Modules\CQHttp\CQPrivateMessageEvent;
use App\Modules\Plugins\EchoPlugin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Intervention\Image\ImageManagerStatic;

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

    public function downloadImage(Request $request)
    {
        if (empty($request->query('id'))) {
            return Response::create('<center><h1>404 Image Not Found</h1></center>', 404);
        }
        $img = Image::find($request->query('id'));
        if (empty($img) or empty($img->local_path)) {
            return Response::create('<center><h1>404 Image Not Found</h1></center>', 404);
        }
        return ImageManagerStatic::make(storage('/image/'.$img->local_path))->response();
    }
}