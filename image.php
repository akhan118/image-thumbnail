<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function resize_image($file, $w, $h, $crop = FALSE) {
    list($width, $height) = getimagesize($file);
    $file_type = get_filetype($file);
    $r = $width / $height;
    if ($crop) {
        if ($width > $height) {
            $width = ceil($width - ($width * abs($r - $w / $h)));
        } else {
            $height = ceil($height - ($height * abs($r - $w / $h)));
        }
        $newwidth = (int) $w;
        $newheight = (int) $h;
   } else {
        if ($w / $h > $r) {
            $newwidth = (int) $h * $r;
            $newheight = (int) $h;
        } else {
            $newheight = (int) $w / $r;
            $newwidth = (int) $w;
        }
    }
 
    switch ($file_type) {
        case 'image/jpeg':
            $src = imagecreatefromjpeg($file);
            $dst = imagecreatetruecolor($newwidth, $newheight);
            break;
        case 'image/png':
            $src = imagecreatefrompng($file);
            $dst = imagecreatetruecolor($newwidth, $newheight);
 
 
            imagealphablending($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefill($dst, 0, 0, $transparent);
            break;
    }
 
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    switch (get_filetype($file)) {
        case 'image/jpeg':
            break;
        case 'image/png':
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            break;
    }          
 
    return $dst;
}
 