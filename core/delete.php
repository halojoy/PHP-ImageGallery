<?php

if (!isset($_COOKIE['imageprep'])) {
    header('location:../index.php');
    exit();
}

require('menu.php');
require('dbase.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    $sql = "SELECT image FROM images WHERE id=$id";
    $image = $pdo->query($sql)->fetchColumn();
    $sql = "DELETE FROM images WHERE id=$id";
    $pdo->exec($sql);
    chdir('../');
    $imgpart = pathinfo($image, PATHINFO_FILENAME);
    $files = scandir('images');
    foreach($files as $file) {
        if (strpos($file, $imgpart) === 0) {
            rename('images/'.$file, 'deleted/'.$file);
        }  
    }
    chdir('core');
}

echo '<strong>Delete Image</strong><br>';
$sql = "SELECT id, image, thumbcode FROM images ORDER BY imorder DESC";
$imgs = $pdo->query($sql)->fetchAll();
$pdo = null;
$break1 = ceil(count($imgs)/3);
$break2 = ceil((count($imgs) - $break1)/2) + $break1;

?>
<table>
<?php
echo '<table style="float:left;width:240px">';
$count = 0;
foreach ($imgs as $row) {
    $thumb = 'tmb_'.$row->image;
    file_put_contents('../temp/'.$thumb, $row->thumbcode);
    echo "<tr>\n";
    echo '<td width="80px"><img src="../temp/'.$thumb.'" width="80"></td>';
    echo '<td>';
    echo '<form method="post">';
    echo '<input type="hidden" name="id" value="'.$row->id.'">';
    echo '<input type="submit" value="Delete Image"
        onclick="return confirm(\'Do you really want to Delete?\')">';
    echo '</form>';
    echo '</td>'."\n";
    echo "</tr>\n";
    $count++;
    if ($count == $break1 || $count == $break2) {
        echo '</table>';
        echo '<table style="float:left;width:240px">';
    }
}
?>
</table>
<?php
exit();
