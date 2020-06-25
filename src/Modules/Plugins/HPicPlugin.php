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
    ];
    protected $listen = '*';

    public function getPic(CQEvent $e, Client $cli)
    {
        try {
            $res = $cli->get('https://yande.re/post?tags=princess_connect', [
                'timeout' => 5,
                'proxy' => 'http://127.0.0.1:1087'
            ]);
        } catch (Exception $err) {
            $e->reply('Error: '.$err->getMessage());
            return;
        }
        if ($res->getStatusCode() != 200) {
            $e->reply('获取失败, 状态码 '.$res->getStatusCode());
            return;
        }
        $dom = new Dom;
        $html = $res->getBody()->getContents();
        $dom->load($html);
        $result = $dom->find('a.thumb img')->toArray();
        $result = $result[array_rand($result)];
        if (preg_match('/src="([^"]*)"/', $result, $matches)) {
            $e->reply(CQCode::image($matches[1]));
        } else {
            $e->reply('获取失败');
        }
    }
}