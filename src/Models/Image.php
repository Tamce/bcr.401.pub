<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Exception;

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
        $client = app('http.client');
        try {
            $data = $client->get($url, [
                'timeout' => 5,
            ])->getBody()->getContents();
        } catch (Exception $e) {
            return false;
        }
        if (empty($data)) {
            return false;
        }

        $ext = substr($url, strrpos($url, '.'));
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
}