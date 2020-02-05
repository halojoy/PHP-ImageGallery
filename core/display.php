<?php

if (!isset($_GET['id']))
    exit();

$pdo = new PDO('sqlite:database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$id = $_GET['id'];
$sql = "SELECT image, imagecode FROM images WHERE id=$id";
$row = $pdo->query($sql)->fetch();
file_put_contents('../temp/'.$row->image, $row->imagecode);
$pdo = null;

echo '<script src="../js/polyfills.js"></script>
<script src="../js/webp-hero.bundle.js"></script>
<script>var webpMachine = new webpHero.WebpMachine();
webpMachine.polyfillDocument();</script>';

echo '<center><img src="../temp/'.$row->image.'"></center>';

exit();
