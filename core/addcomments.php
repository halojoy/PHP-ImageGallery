<?php

if (isset($_GET['logout'])) {
    setcookie('imageprep', '', time() - 3600);
    header('location:../index.php');
    exit();
}

if (!is_file('database.db')) {
    header('location:install.php');
    exit();
}

require('dbase.php');

if (isset($_POST['pass'])) {
    $sql = "SELECT passhash FROM settings";
    $passhash = $pdo->query($sql)->fetchColumn();
    $pdo = null;
    if (password_verify($_POST['pass'], $passhash)) {
        setcookie('imageprep', '1', time() + 7*24*3600);
        header('location:addcomments.php');
        exit();        
    }
}
if (!isset($_COOKIE['imageprep'])) {
    echo '<strong>Login</strong><br><br>';
    echo '<form method="post">';
    echo 'Password: <input type="password" name="pass"><br><br>';
    echo '<input type="submit">';
    echo '</form>';

    exit();
}

echo '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
require('menu.php');

if (isset($_POST['comment'])) {
    $id      = $_POST['imgid'];
    $comment = $_POST['comment'];
    $sql = "UPDATE images SET comment=? WHERE id=$id";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(1, $comment, PDO::PARAM_STR);
    $sth->execute();
    $sth = null;
    $pdo = null;
    header('location: addcomments.php');
    exit();    
}

if (isset($_GET['addcom'])) {
    $id = $_GET['addcom'];
    $sql = "SELECT id, image, comment, thumbcode FROM images WHERE id=$id";
    $row = $pdo->query($sql)->fetch();
    $pdo = null;
    $thumb = 'tmb_'.$row->image;
    file_put_contents('../temp/'.$thumb, $row->thumbcode);
    ?>
    <strong>Add Comment</strong><br>
    <div style="float:left"><img src="../temp/<?php echo $thumb ?>"></div>
    <style>textarea{font-family:Times New Roman;font-size:16px}</style>
    <form action="addcomments.php" method="post">
        <textarea name="comment" rows="5" 
                    cols="34"><?php echo $row->comment ?></textarea>
        <input type="hidden" name="imgid" value="<?php echo $id ?>">
        <br><br>
        <input type="submit">
        <input type="button" value="Cancel"
            onClick="window.location.href='addcomments.php'">
    </form>
<?php
    exit();
}
echo '<div style="margin-left:10px">';
echo '<strong>Add Comments</strong><br>';
$sql = "SELECT id, image, comment, thumbcode FROM images ORDER BY imorder DESC";
$rows = $pdo->query($sql)->fetchAll();
$break1 = ceil(count($rows)/3);
$break2 = ceil((count($rows) - $break1)/2) + $break1;
$sql = "SELECT thumb_size FROM settings";
$thumb_size = $pdo->query($sql)->fetchColumn();
$pdo = null;

echo '<div style="float:left;width:460px">'."\n\n";
$count = 0;
foreach($rows as $row) {
    $thumb = 'tmb_'.$row->image;
    file_put_contents('../temp/'.$thumb, $row->thumbcode);
    echo '<div style="float:left;width:'.$thumb_size.';margin-bottom:3">';
    echo '<a style="float:right" href="?addcom='.$row->id.
            '"><img src="../temp/'.$thumb.'"></a>';
    echo '</div>';
    echo '<div style="float:left;width:'.(410 - $thumb_size).';padding:0 8px;
            border-top:1px solid black">';
    echo nl2br($row->comment, false);
    echo '</div>';
    echo '<div style="clear:both"></div>'; 
    $count++;
    if ($count == $break1 || $count == $break2) {
        echo '</div>';
        echo '<div style="float:left;width:460px">'."\n\n";    
    }
}
echo '</div>';
echo '<div style="clear:both"></div>'; 
echo '</div>';
exit();
