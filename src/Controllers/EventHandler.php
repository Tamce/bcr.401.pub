<?php
namespace App\Controllers;

use Illuminate\Http\Request;

class EventHandler
{
    public function handle(Request $request)
    {
        $botId = $request->header('X-Self-ID', '0');
        $sig = $request->header('X-Signature');

        $type = $request->input('post_type');
        if (in_array($type, ['message', 'group'])) {
            if ($request->input('message') == '%ping') {
                return json([
                    'reply' => 'pong',
                ]);
            }
        }
        return '';
    }
}