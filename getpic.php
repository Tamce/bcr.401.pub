<?php

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;

require 'src/bootstrap.php';

$client = new Client;
$response = $client->get('https://yande.re/post?tags=princess_connect', [
    'timeout' => 5,
]);
$dom = new Dom;
$dom->load($response->getBody()->getContents());
$result = [];
foreach ($dom->find('a.thumb img') as $item) {
    $tag = $item->getTag();
    $url = $tag->getAttribute('src')['value'];
    $result[] = $url;
}
file_put_contents(storage('/hpic.json'), json_encode($result));
$count = count($result);
echo "Saved $count items to ".storage('/hpic.json')."\n";
