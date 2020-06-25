<?php
namespace App\Modules\Plugins;

use App\Modules\CQHttp\CQCode;
use App\Modules\CQHttp\Events\CQEvent;
use Exception;
use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

class HPicPlugin extends OnMessagePlugin
{
    protected $commands = [
        '色图' => 'getPic',
        '瑟图' => 'getPic',
        '涩图' => 'getPic',
    ];
    protected $listen = '*';

    public function getPic(CQEvent $e)
    {
        if (file_exists(storage('/hpic.json'))) {
            $list = json_decode(file_get_contents(storage('/hpic.json')));
            if (!empty($list)) {
                $e->reply(CQCode::image($list[array_rand($list)]));
                return;
            } else {
                $e->reply('图片列表为空，请稍后再试');
            }
        } else {
            $e->reply('图片列表为空，请稍后再试');
        }
    }
}