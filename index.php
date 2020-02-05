<?php

if (!is_file('core/database.db')) {
    header('location:core/install.php');
    exit();
}

$pdo = new PDO('sqlite:core/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$sql = "SELECT title FROM settings";
$title = $pdo->query($sql)->fetchColumn();
$sql = "SELECT thumb_size FROM settings";
$thumb_size = $pdo->query($sql)->fetchColumn();
array_map('unlink', glob('temp/*'));

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
            "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php echo $title ?></title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

<!-- Fix for Internet Explorer -->
<script src="js/polyfills.js"></script>
<script src="js/webp-hero.bundle.js"></script>
<script>var webpMachine = new webpHero.WebpMachine();
webpMachine.polyfillDocument();</script>

</head>
<?php

echo '<body style="background:#dd9999">';

echo '<div style="margin-left:10px">'."\n";
echo '<strong>'.$title.'</strong>&nbsp;&nbsp;&nbsp;';
echo '<a style="color:blue;text-decoration:none" 
                href="core/addcomments.php">admin</a><br>'."\n";

$sql = "SELECT id, image, comment, thumbcode FROM images ORDER BY imorder DESC";
$rows = $pdo->query($sql)->fetchAll();
$pdo = null;

$break1 = ceil(count($rows)/3);
$break2 = ceil((count($rows) - $break1)/2) + $break1;

echo '<div style="float:left;width:460px">'."\n\n";
$count = 0;
foreach($rows as $row) {
    $thumb = 'tmb_'.$row->image;
    file_put_contents('temp/'.$thumb, $row->thumbcode);
    $size = getimagesize('temp/'.$thumb);
    echo '<div style="float:left;width:'.$thumb_size.'px;margin-bottom:3px">'."\n";
    echo '<a style="float:right" href="core/display.php?id='.$row->id.
        '" target="_blank"><img src="temp/'.$thumb.'"></a>';
    echo '</div>'."\n";
    echo '<div style="float:left;width:'.(410 - $thumb_size).'px;height:'.$size[1].'px;
            padding:0 8px;background:#ffffcc">';
    echo nl2br($row->comment, false);
    echo '</div>'."\n";
    echo '<div style="clear:both"></div>'."\n\n";
    $count++;
    if ($count == $break1 || $count == $break2) {
        echo '</div>';
        echo '<div style="float:left;width:460px">'."\n\n";    
    }
}
echo '</div>';
echo '<div style="clear:both"></div>';
echo '</div>';
echo '</body>';
echo '</html>';
exit();
