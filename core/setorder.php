<?php

if (!isset($_COOKIE['imageprep'])) {
    header('location:../index.php');
    exit();
}

require('menu.php');
require('dbase.php');

if (isset($_POST['disp_ord'])) {
    $neworder = $_POST['in'];
    foreach ($neworder as $id => $disp_ord) {
        $sql = "UPDATE images SET imorder=$disp_ord WHERE id=$id";
        $pdo->exec($sql);
    }
}

echo '<strong>Images Display Order</strong><br><br>';
$sql = "SELECT id, image, thumbcode, imorder FROM images ORDER BY imorder DESC";
$imgs = $pdo->query($sql)->fetchAll();
$pdo = null;
?>
<form method="post">
    <input type="hidden" name="disp_ord" value="1">
    <input type="submit" value="Submit">
    <input type="button" value="Cancel"
        onClick="window.location.href='addcomments.php'">
    <br><br>
<?php
$break1 = ceil(count($imgs)/3);
$break2 = ceil((count($imgs) - $break1)/2) + $break1;

echo '<table style="float:left;width:220px">';
$count = 0;
foreach ($imgs as $row) {
    $thumb = 'tmb_'.$row->image;
    file_put_contents('../temp/'.$thumb, $row->thumbcode);
    echo "<tr>\n";
    echo '<td width="80px"><img src="../temp/'.$thumb.'" width="80"></td>';
    echo '<td>
        <input type="number" min="1" max="99" name="in['.$row->id.']"
        value="'.$row->imorder.'" required>';
    echo '</td>'."\n";
    echo "</tr>\n";
    $count++;
    if ($count == $break1 || $count == $break2) {
        echo '</table>';
        echo '<table style="float:left;width:220px">';
    }
}
?>
</table>
</form>
<?php
exit();
