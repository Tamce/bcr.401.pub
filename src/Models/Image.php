<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Exception;
use GuzzleHttp\Client;

class Image extends Model
{
    protected $table = 'images';
    protected $guarded = [];
    protected $casts = [
        'extra' => 'array',
    ];

    static public function download($url, $category = 'default')
    {
        app('logger')->debug("try download image from `$url` to category `$category`");
        set_time_limit(6);
        $client = new Client;
        try {
            $res = $client->get($url, [
                'timeout' => 5,
            ]);
            $data = $res->getBody()->getContents();
        } catch (Exception $e) {
            return false;
        }
        if (empty($data)) {
            return false;
        }

        $ext = substr($url, strrpos($url, '.'));
        $type = $res->getHeader('Content-Type');
        if (empty($type)) {
            $ext = '.jpg';
        } else {
            if (is_array($type)) {
                $type = $type[0];
            }
            $ext = '.' . substr($type, strrpos($type, '/') + 1);
        }
        if (strlen($ext) > 5 or empty($ext)) {
            $ext = '.jpg';
        }
        $name = Str::random(32) . $ext;
        $name = "downloaded/$name";
        file_put_contents(storage("/image/$name"), $data);
        return [
            'category' => $category,
            'origin_url' => $url,
            'local_path' => $name,
            'downloaded' => 1,
        ];
    }

    static public function handleCache($file)
    {
        $file = static::handleCQCache($file);
        $file = static::handleCQGoCache($file);
        return $file;
    }

    static public function handleCQCache($file)
    {
        if (!Str::startsWith($file, 'downloaded/') && !Str::endswith($file, '.image')) {
            $dest = storage("/image/downloaded/$file");
            $src = storage("/image/$file");
            if (file_exists($src)) {
                app('logger')->debug("handleCQGoCache: image file exists, move to downloaded");
                file_put_contents($dest, file_get_contents($src));
                return "downloaded/$file";
            } else if (file_exists("$src.cqimg")) {
                $data = file_get_contents("$src.cqimg");
                if (preg_match('/url=(.*)/', $data, $matches)) {
                    $url = $matches[1];
                    $data = static::download($url);
                    app('logger')->debug("handleCQGoCache: $file -> {$data['local_path']}");
                    return $data['local_path'];
                }
            }
        }
        return $file;
    }

    static public function handleCQGoCache($file)
    {
        if (Str::endsWith($file, '.image')) {
            $data = file_get_contents(storage("/image/$file"));
            // $name = @unpack('H*', substr($data, 0, 16))[1];
            $data = substr($data, 24);
            // $ext = 'jpg';
            // if (preg_match('/\.(\w+)/', $data, $matches)) {
            //     $ext = $matches[1];
            // }
            $url = substr($data, 4 + strpos($data, "\0\0\0"));
            $data = static::download($url);
            app('logger')->debug('handled cqhttp-go cache.', [
                'origin' => $file,
                'name' => $data['local_path'],
                'url' => $url,
            ]);
            app('logger')->debug("handleCQGoCache: $file -> {$data['local_path']}");
            return $data['local_path'];
        }
        return $file;
    }
}
