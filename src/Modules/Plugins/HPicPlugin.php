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
        '涩图有多少' => 'query',
        '涩图 (.*)' => 'query',
        '色图' => 'getPic',
        '瑟图' => 'getPic',
        '涩图' => 'getPic',
    ];
    protected $listen = '*';

    public function query(CQEvent $e, $id = null)
    {
        $list = json_decode(file_get_contents(storage('/hpic.json')));
        if (is_null($id)) {
            $e->reply('目前涩图存量: '.count($list));
        } else {
            if ($id >= count($list) or $id < 0) {
                $e->reply('合法 id 范围为 0 - '.count($list) - 1);
            }
            $url = $list[$id];
            $e->reply("id: $id\nurl: $url\n".CQCode::image($url));
        }
    }

    public function getPic(CQEvent $e)
    {
        if (file_exists(storage('/hpic.json'))) {
            $list = json_decode(file_get_contents(storage('/hpic.json')));
            if (!empty($list)) {
                $id = array_rand($list);
                $e->reply("id: $id\n".CQCode::image($list[$id]));
                return;
            } else {
                $e->reply('图片列表为空，请稍后再试');
            }
        } else {
            $e->reply('图片列表为空，请稍后再试');
        }
    }
}
