<?php

use App\Models\Image;
use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

require 'src/bootstrap.php';

$client = new Client;
$data = json_decode(file_get_contents(storage('/hpic.json')));

$cnt = count($data);
$i = 1;
foreach ($data as $url) {
    echo "\r$i/$cnt   ";
    $i++;
    $ext = substr($url, strrpos($url, '.'));
    $name = Illuminate\Support\Str::random(32).$ext;
    try {
        $data = $client->get($url, [
            'timeout' => 5,
        ])->getBody()->getContents();
    } catch (Exception $e) {
        echo $e->getMessage().", skipped\n";
        continue;
    }
    if (empty($data)) {
        echo "skipped.\n";
        continue;
    }

    file_put_contents(storage("/image/$name"), $data);
    Image::create([
        'category' => 'default',
        'origin_url' => $url,
        'local_path' => $name,
        'downloaded' => 1,
    ]);
}