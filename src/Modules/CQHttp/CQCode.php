<?php
namespace App\Modules\CQHttp;

class CQCode
{
    static public function image($url)
    {
        return "[CQ:image,file=$url]";
    }

    static public function at($qq)
    {
        return "[CQ:at,qq=$qq]";
    }
}
