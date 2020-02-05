<?php

if (!isset($_COOKIE['imageprep'])) {
    header('location:../index.php');
    exit();
}

require('menu.php');
require('dbase.php');
echo '<strong>Resize Images</strong><br><br>';

if (!isset($_POST['resize'])) {
    
    echo 'Image Height is now '.$image_height.' pixels<br><br>';
    echo '<form method="post">';
    echo 'Resize to ';
    echo '<input type="number" min="600" max="1000" name="resize">';
    echo '<br><br>';
    echo '<input type="submit">';
    echo '</form>';
    
    exit();    
}

$image_height = $_POST['resize'];

require('classImageTool.php');

if (extension_loaded('imagick'))
    define('EXT', 'imagick');
elseif (extension_loaded('gd'))
    define('EXT', 'gd');
else
    exit('PHP no image support');

chdir('../');

$sql = "SELECT id, imagecode FROM images";
$rows = $pdo->query($sql)->fetchAll();

$size = 0;
foreach($rows as $row) {
    file_put_contents('temp/image.webp', $row->imagecode);
    list($x, $y) = getimagesize('temp/image.webp');
    $size = max($size, $y);
}

if ($size != $image_height) {

    if (EXT == 'imagick')
        $magic = new ImageTool();
    foreach($rows as $row) {
        file_put_contents('temp/image.webp', $row->imagecode);
        list($x, $y) = getimagesize('temp/image.webp');
        if ($y == $size || $y > $image_height) {
            if (EXT == 'imagick') {
                $magic->read('temp/image.webp');
                $magic->resize(null, $image_height);
                $magic->setImageCompressionQuality(92);
                $magic->write('temp/image.webp');
                $magic->clear();

            }else{ // EXT = gd
                $im = imagecreatefromwebp('temp/image.webp');
                $ih = $image_height;
                $dest = gd_resize($im, $ih*$x/$y, $ih, $x, $y);
                imagewebp($dest, 'temp/image.webp', 92);
                imagedestroy($im);
            }
            $code = file_get_contents('temp/image.webp');
            $id = $row->id;
            $sql = "UPDATE images SET imagecode=? WHERE id=$id";
            $sth = $pdo->prepare($sql);
            $sth->bindParam(1, $code, PDO::PARAM_LOB);
            $sth->execute();
            $sth = null;
        }
    }
}
$sql = "UPDATE settings SET image_height=$image_height";
$pdo->exec($sql);
$pdo = null;
echo '<strong>Image Height is now '.$image_height.' pixels</strong>';
