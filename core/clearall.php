<?php

if (!isset($_COOKIE['imageprep'])) {
    header('location:../index.php');
    exit();
}

require('menu.php');
require('dbase.php');

if (isset($_POST['clear'])) {
    $pdo->exec("DELETE FROM images");
    $pdo =  null;
    echo 'Stored images and database are empty alright.<br>';
    echo  'You can start again by a run of \'Setup\'.';
    exit();
}
$pdo = null;
?>
<strong>Clear All</strong><br><br>
This will delete all images in the database<br>
and empty the database of all your submissions.<br><br>
<form method="post">
    <input type="submit" name="clear" value="Clear All"
        onclick="return confirm('Do you really want to Clear?')">
    <input type="button" value="Cancel"
        onClick="window.location.href='addcomments.php'">
</form>
