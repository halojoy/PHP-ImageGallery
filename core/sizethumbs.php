<?php

if (!isset($_COOKIE['imageprep'])) {
    header('location:../index.php');
    exit();
}

require('menu.php');
require('dbase.php');
echo '<strong>Resize Thumbs</strong><br><br>';

if (!isset($_POST['resize'])) {
    
    echo 'Thumb Size is now '.$thumb_size.' pixels<br><br>';
    echo '<form method="post">';
    echo 'Resize to ';
    echo '<input type="number" min="80" max="300" name="resize">';
    echo '<br><br>';
    echo '<input type="submit">';
    echo '</form>';
    
    exit();    
}

$thumb_size = $_POST['resize'];

require('classImageTool.php');

if (extension_loaded('imagick'))
    define('EXT', 'imagick');
elseif (extension_loaded('gd'))
    define('EXT', 'gd');
else
    exit('PHP no image support');

chdir('../');

$sql = "SELECT id, thumbcode FROM images";
$rows = $pdo->query($sql)->fetchAll();

if (EXT == 'imagick')
    $magic = new ImageTool();
foreach($rows as $row) {

    file_put_contents('temp/thumb.webp', $row->thumbcode);

    if (EXT == 'imagick') {
        $magic->read('temp/thumb.webp');
        $sx = $magic->getImageWidth();
        $sy = $magic->getImageHeight();
        $ts = $thumb_size;
        $k = min($ts/$sx, $ts/$sy);
        if ($k != 1) {
            $magic->resize($ts, $ts);
            $magic->write('temp/thumb.webp');
        }
        $magic->clear();

    }else{ //EXT = 'gd'
        $im = imagecreatefromwebp('temp/thumb.webp');
        $sx = imagesx($im);
        $sy = imagesy($im);
        $ts = $thumb_size;
        $k = min($ts/$sx, $ts/$sy);
        if ($k != 1) {
            $dest = gd_resize($im, $k*$sx, $k*$sy, $sx, $sy);
            imagewebp($dest, 'temp/thumb.webp');
        }        
        imagedestroy($im);
    }

    if ($k != 1) {
        $id = $row->id;
        $thumbdata = file_get_contents('temp/thumb.webp');
        $sql = "UPDATE images SET thumbcode=?  WHERE id=$id";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(1, $thumbdata, PDO::PARAM_LOB);
        $sth->execute();
        $sth = null;
    }
}
$sql = "UPDATE settings SET thumb_size=$thumb_size";
$pdo->exec($sql);
$pdo = null;
echo '<strong>Thumbs were resized to '.$thumb_size.' pixels</strong>';
exit();
