<?php

// ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);

$_path = array_values(array_filter(explode("/",parse_url($_SERVER['REQUEST_URI'])['path'])));
$folder = filter_text($_path[0]);
$text = filter_text(pathinfo($_path[1],PATHINFO_FILENAME));

$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';

if($folder=='trakteer'){
    $image1 = imagecreatefrompng('trakteer_kiri.png');
    $image2 = imagecreatefrompng('trakteer_kanan.png');
}else{
    include("info.html");
    die();
}

if(strlen($text)<2){
    include("info.html");
    die();
}

header('Content-Type: image/png');

//Proses text dan hitung panjang lebar
$font_size = 28;
$font_file = './HelveticaNeueMed.ttf';
$type_space = imagettfbbox($font_size, 0, $font_file, $text);
$text_width = abs($type_space[4] - $type_space[0]);
$text_height = abs($type_space[5] - $type_space[1]);

//Bolehkan transparan
imagealphablending($image1, true);
imagealphablending($image1, true);
imagesavealpha($image1, true);
imagesavealpha($image2, true);

// widht dan height kanan
$wkn = $text_width+20;
$hkn = ImageSY($image2);

//potong bagian kanan sesuai panjang text
$cropkanan = imagecreatetruecolor($wkn, $hkn);
$transparentColor = imagecolorallocate($cropkanan, 0, 0, 0);
imagecolortransparent($cropkanan, $transparentColor);
imagefill($cropkanan, 0, 0, $transparentColor);
imagealphablending($cropkanan, false);
imagesavealpha($cropkanan, true);
imagecopy($cropkanan, $image2, 0, 0, ImageSX($image2)-$wkn, 0, $wkn, $hkn);

//width kiri
$wkr = ImageSX($image1);

//gabungkan kiri dan kanan
$merged_image = imagecreatetruecolor($wkr+$wkn, $hkn);
$transparentColor = imagecolorallocate($merged_image, 0, 0, 0);
imagecolortransparent($merged_image, $transparentColor);
imagefill($merged_image, 0, 0, $transparentColor);
imagealphablending($merged_image, false);
imagesavealpha($merged_image, true);

imagecopy($merged_image, $image1, 0, 0, 0, 0, $wkr+$wkn, $hkn);
imagecopy($merged_image, $cropkanan, $wkr, 0, 0, 0, $wkr+$wkn, $hkn);

//Text
$text_color = imagecolorallocate($merged_image, 0, 0, 0);
$x = $wkr+5;
$y = $hkn-(($hkn-$text_height)/2);
imagettftext($merged_image, $font_size, 0, $x, $y, $text_color, $font_file, $text);

//Simpan
imagepng($merged_image, $folder."/".$text.".png");
imagedestroy($merged_image);
imagedestroy($cropkanan);
imagedestroy($image1);
imagedestroy($image2);
readfile($folder."/".$text.".png");

//hapus karakter tidak penting
function filter_text($s){
    return preg_replace("/[^a-zA-Z0-9.-]+/", "", $s);
}