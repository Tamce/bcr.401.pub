<?php
namespace App\Modules\CQHttp;
use Intervention\Image\ImageManagerStatic as Image;

class CQCode
{
    static public function image($url)
    {
        return "[CQ:image,file=$url]";
        if (strlen($url) > 10 and substr($url, 0, 10) == 'downloaded') {
            $img = Image::make(storage('/image/'.$url));
            // $img->resize($img->width()*0.5, $img->height()*0.5);
            $ext = 'jpg';
            $img->encode($ext)->save(storage("/image/temp.$ext"));
            return "$url [CQ:image,file=temp.$ext]";
        }
        return "[CQ:image,file=$url]";
    }

    static public function at($qq)
    {
        return "[CQ:at,qq=$qq]";
    }
}
