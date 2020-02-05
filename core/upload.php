<?php

if (!isset($_COOKIE['imageprep'])) {
    header('location:../index.php');
    exit();
}

require('menu.php');

if (isset($_FILES['image']['name'])) {
    $temp = $_FILES['image']['tmp_name'];
    $name = $_FILES['image']['name'];
    move_uploaded_file($temp, '../images/'.$name);
    echo '<strong>'.$name.'</strong> was uploaded.';
    echo '<br><br>';
    echo 'After uploading new images<br> 
            you need to run <strong>Setup</strong> again.';
    echo '<br><br>';
}
?>
<strong>Image Upload:</strong>
<br><br>
<form enctype="multipart/form-data" method="post">
<input type="file" name="image" required>
<br><br>
<input type="submit">
</form>
