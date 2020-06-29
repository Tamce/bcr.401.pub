<?php
require __DIR__.'/../src/bootstrap.php';

use App\Modules\CQHttp\CQHttp;
$cq = app(CQHttp::class);
$ret = $cq->sendGroupMessage('675663307', "每日提醒买药小助手提醒您:\n大郎，该吃药了");
app('logger')->withName('cron')->info('发送每日吃药提醒', ['ret' => $ret]);
