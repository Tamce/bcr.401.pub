<?php
use App\Models\Image;

require '../src/bootstrap.php';

$i = 1;
foreach (Image::all() as $img) {
    echo "\r[$i]   ";
    if (!file_exists(storage("/image/{$img->local_path}"))) {
        $new_path = Image::handleCQCache($img->local_path);
        if (file_exists(storage("/image/$new_path"))) {
            $img->local_path = $new_path;
            $img->save();
            echo " Updated.\n";
        } else {
            echo " Failed.\n";
        }
    }
    $i++;
}
echo "\nDone!\n";