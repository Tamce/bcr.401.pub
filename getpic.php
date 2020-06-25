<?php

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

require 'src/bootstrap.php';

$client = new Client;

$result = [];
for ($page = 1; $page <= 40; ++$page)
{
    $response = $client->get('https://yande.re/post?tags=princess_connect&page='.$page, [
        'timeout' => 5,
    ]);
    $dom = new Dom;
    $dom->load($response->getBody()->getContents());
    $items = $dom->find('a.thumb')->toArray();
    $i = 1;
    $pageCnt = count($items);
    foreach ($items as $item) {
        echo "\rPage $page / 40: \t$i / $pageCnt      ";
        $i++;
        $tag = $item->getTag();
        $url = $tag->getAttribute('href')['value'];
        $response = $client->get('https://yande.re'.$url);
        $dom->load($response->getBody()->getContents());
        $url = $dom->find('#image')[0]->getTag()->getAttribute('src')['value'];
        $result[] = $url;
    }
    echo "\n";
}
file_put_contents(storage('/hpic.json'), json_encode($result));
$count = count($result);
echo "Saved $count items to ".storage('/hpic.json')."\n";
