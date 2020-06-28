<?php
namespace App\Modules\CQHttp;

use Intervention\Image\ImageManagerStatic as Image;

class CQCode
{
    static public function image($url)
    {
        //  Image::make(storage("/image/$url"))->encode('webp')->save(storage("/image/$url.webp"));
        return "$url\n[CQ:image,file=$url]";
    }

    static public function at($qq)
    {
        return "[CQ:at,qq=$qq]";
    }
}
