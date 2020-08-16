<?php
namespace App\Modules\CQHttp;

use Intervention\Image\ImageManagerStatic as Image;

class CQCode
{
    /**
     * 发送图片
     *
     * @param  mixed $url 图片url。
     * @return String CQ码图片串。
     */
    static public function image($url)
    {
        //  Image::make(storage("/image/$url"))->encode('webp')->save(storage("/image/$url.webp"));
        return "[CQ:image,file=$url]";
    }

    /**
     * at特定用户。
     *
     * @param  mixed $qq QQ号ID。
     * @return String CQ码at串。
     */
    static public function at($qq)
    {
        return "[CQ:at,qq=$qq]";
    }

    /**
     * 发送语音。
     * 
     * @param  mixed $url 音频url。
     * @return String CQ码语音串。
     */
    static public function record($url)
    {
        return "[CQ:record,file=$url]";
    }

    /**
     * 发送表情。
     * 表情码与QQ对照关系参见 @link [https://github.com/richardchien/coolq-http-api/wiki/%E8%A1%A8%E6%83%85-CQ-%E7%A0%81-ID-%E8%A1%A8] <QQ表情ID对照表>}
     *
     * @param  mixed $id QQ表情ID。
     * @return String CQ码表情串。
     */
    static public function face($id)
    {
        return "[CQ:face,id=$id]";
    }

        
    /**
     * 分享链接。
     *
     * @param  mixed $url 分享链接url
     * @param  mixed $title 分享内容标题
     * @param  mixed $content (Optional) 分享内容描述
     * @param  mixed $image (Optional) 缩略图url
     * @return String CQ码分享串
     */
    static public function share($url, $title, $content = NULL, $image = NULL)
    {
        return "[CQ:share, url=$url, title=$title"
        .(isset($content) ? "" : ",content=$content")
        .(isset($image) ? "" : ",image=$image")."]";
    }
   
    /**
     * 分享音乐。
     *
     * @param  mixed $type 分享类型。目前的可选值包括qq音乐("qq")，网易云("163")与虾米("xm")。
     * @param  mixed $id 音乐id
     * @return String CQ码音乐分享串
     */
    static public function musicShare($type, $id)
    {
        return "[CQ:music,type=$type,id=$id]";
    }

    /**
     * 回复消息。
     *
     * @param  mixed $id 消息id
     * @return String CQ码回复串
     */
    static public function reply($id)
    {
        return "[CQ:reply,id=$id]";
    }
}
