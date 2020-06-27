<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Exception;
use GuzzleHttp\Client;

class Image extends Model
{
    protected $table = 'images';
    protected $guarded = ['id'];
    protected $casts = [
        'extra' => 'array',
    ];

    static public function download($url, $category = 'default')
    {
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
            $ext = '.'.substr($type, strrpos($type, '/') + 1);
        }
        if (strlen($ext) > 5 or empty($ext)) {
            $ext = '.jpg';
        }
        $name = Str::random(32).$ext;
        $name = "downloaded/$name";
        file_put_contents(storage("/image/$name"), $data);
        return [
            'category' => $category,
            'origin_url' => $url,
            'local_path' => $name,
            'downloaded' => 1,
        ];
    }

    static public function handleCQCache($file)
    {
        if (!Str::startsWith($file, 'downloaded/')) {
            $dest = storage("/image/downloaded/$file");
            $src = storage("/image/$file");
            if (file_exists($src)) {
                file_put_contents($dest, file_get_contents($src));
                return "downloaded/$file";
            } else if (file_exists("$src.cqimg")) {
                $data = file_get_contents("$src.cqimg");
                if (preg_match('/url=(.*)/', $data, $matches)) {
                    $url = $matches[1];
                    $data = static::download($url);
                    return $data['local_path'];
                }
            }
        }
        return $file;
    }
}
