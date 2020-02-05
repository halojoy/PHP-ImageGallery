<?php

if (!isset($_COOKIE['imageprep'])) {
    header('location:../index.php');
    exit();
}

require('classImageTool.php');

require('menu.php');
require('dbase.php');

if (extension_loaded('imagick'))
    define('EXT', 'imagick');
elseif (extension_loaded('gd'))
    define('EXT', 'gd');
else
    exit('PHP no image support');

chdir('../');

echo '<strong>Images Prepare</strong><br><br>';
$scanfiles = scandir('images');
$allimages = array();
foreach($scanfiles as $file) {
    if (substr($file, 0, 1) == '.')    continue;
    if (substr($file, 0, 4) == 'tmb_') continue;
    if (strpos(mime_content_type('images/'.$file), 'image') === 0) {
        $allimages[] = 'images/'.$file;
     }
}

if (EXT == 'imagick')
    $magic = new ImageTool();
$count = 0;
foreach($allimages as $image) {

    $imagename = pathinfo($image, PATHINFO_FILENAME).'.webp';
    $sql = "SELECT thumbcode FROM images WHERE image='$imagename'";
    $res = $pdo->query($sql)->fetchColumn();

    if (!$res) {

        if (EXT == 'imagick') {
            $magic->read($image);
            $typ = $magic->getImageFormat();
            if ($typ != 'WEBP') {
                $magic->convert('webp');
            }
            if ($magic->getImageHeight() > $image_height) {
                $magic->resize(null, $image_height);
            }
            $magic->setImageCompressionQuality(92);
            $magic->write('temp/'.$imagename);
            $magic->clear();

            $thumb = 'tmb_'.$imagename;
            $magic->read('temp/'.$imagename);
            $ts = $thumb_size;
            $magic->resize($ts, $ts);
            $magic->write('temp/'.$thumb);
            $magic->clear();

        }else{ //EXT = 'gd'
            $im = gd_imagecreate($image);
            $sx = imagesx($im);
            $sy = imagesy($im);
            $ih = $image_height;
            if ($sy > $ih) {
                $dest = gd_resize($im, $ih*$sx/$sy, $ih, $sx, $sy);
            }else{
                $dest = $im;
            }
            imagewebp($dest, 'temp/'.$imagename, 92);
            imagedestroy($im);

            $thumb = 'tmb_'.$imagename;
            $im = imagecreatefromwebp('temp/'.$imagename);
            $sx = imagesx($im);
            $sy = imagesy($im);
            $ts = $thumb_size;
            $k = min($ts/$sx, $ts/$sy);
            $dest = gd_resize($im, $k*$sx, $k*$sy, $sx, $sy);
            imagewebp($dest, 'temp/'.$thumb);
            imagedestroy($im);
        }

        $imagedata = file_get_contents('temp/'.$imagename);
        $thumbdata = file_get_contents('temp/'.$thumb);
        $sql = "INSERT INTO images (image, imagecode, thumbcode) VALUES (?,?,?)";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(1, $imagename, PDO::PARAM_STR);
        $sth->bindParam(2, $imagedata, PDO::PARAM_LOB);
        $sth->bindParam(3, $thumbdata, PDO::PARAM_LOB);
        $sth->execute();
        $id = $pdo->lastInsertId();
        $sql = "SELECT imorder FROM images ORDER BY imorder DESC";
        $num = $pdo->query($sql)->fetchColumn();
        $num++;
        $sql = "UPDATE images SET imorder=$num WHERE id=$id";
        $pdo->exec($sql);

    }else{
        $thumb = 'tmb_'.$imagename;
        file_put_contents('temp/'.$thumb, $res);
    }

    echo '<img src="../temp/'.$thumb.'" width="80">&nbsp;';
    $count++;
    if ($count % 12 == 0) echo '<br>';
}

$pdo = null;
echo '<br><br><strong>Images Preparation was run successfully</strong>';
exit();
