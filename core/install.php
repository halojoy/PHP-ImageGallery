<?php

if (is_file('database.db') || is_file('core/database.db'))
    exit();

if (isset($_POST['setpass'])) {
    if ($_POST['setpass'] == $_POST['setpass2']) {
        $pdo = new PDO('sqlite:database.db');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $sql = "CREATE TABLE IF NOT EXISTS images (
            id INTEGER PRIMARY KEY, 
            image     TEXT UNIQUE,
            comment   TEXT,
            imagecode BLOB,
            thumbcode BLOB,
            imorder INTEGER DEFAULT 0 )";
        $pdo->exec($sql);
        $sql = "CREATE TABLE IF NOT EXISTS settings (
            title        TEXT,
            passhash     TEXT,
            image_height INTEGER DEFAULT 800,
            thumb_size   INTEGER DEFAULT 120 )";
        $pdo->exec($sql);
        $setpass = $_POST['setpass'];
        $passhash = password_hash($setpass, PASSWORD_BCRYPT);
        $title = $_POST['title'];
        $sql = "INSERT INTO settings (title, passhash) VALUES ('$title', '$passhash')";
        $pdo->exec($sql);
        $pdo = null;
        header('location:addcomments.php');
        exit();
    }else{
        echo 'Passwords do not match!<br><br>';
    }
}

echo '<strong>Install</strong><br><br>';
echo 'You should set a password for admin login<br>';
echo 'and a title for your gallery.<br><br>';
echo '<form method="post">';
echo 'Gallery Title: <input name="title" value="My Gallery" required><br><br>';
echo 'Set Password: <input type="password" name="setpass" required><br>';
echo 'Passw. Again: <input type="password" name="setpass2" required><br><br>';
echo '<input type="submit">';
echo '</form>';
