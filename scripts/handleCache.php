<?php

use App\Models\Image;
use illuminate\Support\Str;

require '../src/bootstrap.php';

$i = 0;
foreach (Image::all() as $img) {
    echo "\r[{$img->id}] ";
    if (!file_exists(storage("/image/{$img->local_path}"))) {
        echo "Handle CQCache: {$img->local_path} ...";
        $new_path = Image::handleCQCache($img->local_path);
        if (file_exists(storage("/image/$new_path"))) {
            $img->local_path = $new_path;
            $img->save();
            $i++;
            echo " Updated.\n";
        } else {
            echo " Failed.\n";
        }
    } else if (Str::endsWith($img->local_path, '.image')) {
        echo "Handle CQHttpGoCache: {$img->local_path} ...";
        $new_path = Image::handleCQGoCache($img->local_path);
        if (file_exists(storage("/image/$new_path"))) {
            $img->local_path = $new_path;
            $img->save();
            $i++;
            echo " Updated.\n";
        } else {
            echo " Failed.\n";
        }
    }
}
echo "\nDone! $i record fixed.\n";
