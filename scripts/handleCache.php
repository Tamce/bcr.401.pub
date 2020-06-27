<?php
use App\Models\Image;

require '../src/bootstrap.php';

$i = 0;
foreach (Image::all() as $img) {
    echo "\r[{$img->id}] ";
    if (!file_exists(storage("/image/{$img->local_path}"))) {
        echo "Missing...";
        $new_path = Image::handleCQCache($img->local_path);
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