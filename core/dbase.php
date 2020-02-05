<?php

$pdo = new PDO('sqlite:database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

$sql = "SELECT image_height FROM settings";
$image_height = $pdo->query($sql)->fetchColumn();
$sql = "SELECT thumb_size FROM settings";
$thumb_size = $pdo->query($sql)->fetchColumn();

array_map('unlink', glob('../temp/*'));
