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
        '涩图备注 (\w+) (.+)' => 'comment',
        '涩图搜索 (.+)' => 'search',
        '涩图 (.+)$' => 'query',
        '涩图$' => 'queryDefault',
        '上传涩图(.*)$' => 'upload',
        '涩图帮助$' => 'help',
    ];
    protected $listen = '*';

    public function help(CQEvent $e)
    {
        $e->reply(<<<EOD
帮助如下

%涩图存量
    查看涩图总量及已缓存的数量
%涩图分类
    查看涩图分类有哪些
%涩图 [分类,可选,默认为 all]
    来一份已缓存的随机涩图
%涩图 id
    按id查看涩图
%涩图备注 id 备注内容
    给涩图写备注方便搜索
%涩图搜索 关键词
    搜索备注含有关键词的涩图
%上传涩图 [分类,可选] [图片/url]
    上传指定图片或url到指定分类的涩图库，默认分类为 default"
EOD);
    }

    public function comment(CQEvent $e, $id, $comment)
    {
        $img = Image::find($id);
        if (empty($img)) {
            return $e->reply("未找到 id 为 $id 的涩图");
        }
        $img->comment = $comment;
        $img->save();
        return $e->reply("成功设置涩图 $id 的备注为: $comment");
    }

    public function search(CQEvent $e, $comment)
    {
        $result = Image::where('comment', 'like', "%$comment%")->get();
        if ($result->isEmpty()) {
            return $e->reply('未找到符合的涩图');
        }
        $e->reply('找到 '.$result->count().' 张符合的涩图:');
        if ($result->count() <= 2) {
            foreach ($result as $item) {
                $e->reply("\nid: {$item->id}\n".CQCode::image($this->getUrlOrDownload($item)));
            }
        } else {
            foreach ($result as $item) {
                $e->reply("\nid: {$item->id}");
            }
        }
    }

    public function count(CQEvent $e)
    {
        $data = Image::select(DB::raw('count(*) as cnt'), 'downloaded')->groupBy('downloaded')->get();
        $e->reply('涩图存量共 '. $data->sum('cnt') ." 份，其中");
        foreach ($data as $downloadGrouped) {
            if ($downloadGrouped->downloaded) {
                $e->reply("\n已下载 {$downloadGrouped->cnt} 份");
            } else {
                $e->reply("\n未下载 {$downloadGrouped->cnt} 份");
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
        if (is_numeric($category)) {
            return $this->queryById($e, $category);
        }

        $data = Image::where('downloaded', true);
        // $data = Image::whereRaw('true');
        if ($category != 'all') {
            $data = $data->where('category', $category);
        }
        $data = $data->get();
        if ($data->isEmpty()) {
            $e->reply("类别 `$category` 的已缓存涩图存量为 0");
        } else {
            $item = $data->random();
            $e->reply("id: {$item->id}\n".CQCode::image($item->local_path));
        }
    }

    public function queryDefault(CQEvent $e)
    {
        $this->query($e, 'all');
    }

    public function queryById(CQEvent $e, $id)
    {
        if (!is_numeric($id)) {
            return $e->reply('请输入数字 id');
        }
        $item = Image::find($id);
        if (empty($item)) {
            return $e->reply("涩图 id: $id 不存在");
        }

        $url = $this->getUrlOrDownload($item);
        if (empty($item->local_path)) {
            $e->reply("[互联网图片,下载失败]\nurl: $url\n");
        }
        $e->reply("id: {$item->id}\n".CQCode::image($url));
    }

    public function upload(CQPrivateMessageEvent $e, $text)
    {
        $text = trim($text);
        $category = substr($text, 0, strpos($text, '['));
        if (empty($category)) {
            $category = 'default';
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

    protected function getUrlOrDownload(Image $item)
    {
        if ($item->local_path) {
            return $item->local_path;
        } else {
            $data = Image::download($item->origin_url);
            if (empty($data)) {
                return $item->origin_url;
            } else {
                $item->local_path = $data['local_path'];
                $item->downloaded = 1;
                $item->save();
                return $item->local_path;
            }
        }
    }
}
