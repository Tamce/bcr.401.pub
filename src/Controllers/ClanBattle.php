<?php
namespace App\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ClanBattle
{
    public function homework(Request $request)
    {
        $items = Image::where('comment', 'like', '%作业%')->get();
        $result = "<h2>会战作业</h2>";
        $result .= "共 ".$items->count()." 条<br>";
        foreach ($items as $item) {
            $result .= "<hr>[{$item->id}] <pre>{$item->comment}</pre><img style='max-width:400px;width:100vw;' src='/download/image?id=$item->id'>";
        }
        return $result;
    }
}