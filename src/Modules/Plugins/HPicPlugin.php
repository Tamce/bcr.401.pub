<?php
namespace App\Modules\Plugins;

use App\Models\Image;
use App\Modules\CQHttp\CQCode;
use App\Modules\CQHttp\Events\CQEvent;
use App\Modules\CQHttp\Events\CQPrivateMessageEvent;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use PHPHtmlParser\Dom;

class HPicPlugin extends OnMessagePlugin
{
    protected $commands = [
        '涩图存量$' => 'count',
        '涩图分类$' => 'category',
        '涩图 (\w+)$' => 'query',
        '涩图$' => 'queryDefault',
        '查涩图 (\d+)' => 'queryById',
        '上传涩图 (.*)$' => 'upload',
    ];
    protected $listen = '*';

    public function count(CQEvent $e)
    {
        $data = Image::select(DB::raw('count(*) as cnt'), 'downloaded')->groupBy('downloaded')->get();
        $e->reply('涩图存量共 '. $data->sum('cnt') ." 份，其中\n");
        foreach ($data as $downloadGrouped) {
            if ($downloadGrouped->downloaded) {
                $e->reply("已下载 {$downloadGrouped->cnt} 份\n");
            } else {
                $e->reply("未下载 {$downloadGrouped->cnt} 份\n");
            }
        }
    }

    public function category(CQEvent $e)
    {
        $data = Image::select(DB::raw('count(*) as cnt'), 'category')->groupBy('category')->get();
        $e->reply('涩图分类存量如下：');
        foreach ($data as $category) {
            $e->reply("\n{$category->category}: $category->cnt");
        }
    }

    public function query(CQEvent $e, $category)
    {
        if (intval($category) == $category) {
            return $this->queryById($e, $category);
        }

        $data = Image::where('downloaded', true)->where('category', $category)->get();
        if ($data->isEmpty()) {
            $e->reply("类别 `$category` 的已缓存涩图存量为 0");
        } else {
            $item = $data->random();
            $e->reply("id: {$item->id}\n".CQCode::image($item->local_path));
        }
    }

    public function queryDefault(CQEvent $e)
    {
        $this->query($e, 'default');
    }

    public function queryById(CQEvent $e, $id)
    {
        if (intval($id) != $id) {
            return $e->reply('请输入数字 id');
        }
        $item = Image::find($id);
        if (empty($item)) {
            return $e->reply('涩图 id 不存在');
        }
        if ($item->local_path) {
            $e->reply("id: {$item->id}\n".CQCode::image($item->local_path));
        } else {
            $e->reply("id: {$item->id}\n".CQCode::image($item->url));
        }
    }

    public function upload(CQPrivateMessageEvent $e, $text)
    {
        $seg = explode(' ', $text, 2);
        $category = 'default';
        if (count($seg) > 1) {
            $category = $seg[0];
            $text = $seg[1];
        }

        if (preg_match('/\[CQ:image,file=([^\],]*),{0,1}.*\]/', $text, $matches)) {
            $path = $matches[1];
            $item = Image::create([
                'category' => $category,
                'local_path' => $path,
                'downloaded' => true,
                'extra' => [
                    'sender' => $e->getSenderId(),
                ],
            ]);
            $e->reply("上传至分类 `$category` 成功！id: $item->id");
        } else if (preg_match('/(http[^\s]+)/', $text, $matches)) {
            $url = $matches[1];
            $item = Image::create([
                'category' => $category,
                'origin_url' => $url,
                'downloaded' => false,
                'extra' => [
                    'sender' => $e->getSenderId(),
                ],
            ]);
            $e->reply("上传至分类 `$category` 成功！id: $item->id");
        } else {
            $e->reply('未发现待上传涩图');
        }
    }
}
