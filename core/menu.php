<?php

echo '<style>a{color:blue}</style>';
echo '<a href="../index.php">Index</a>&nbsp;&nbsp;';
echo '<a href="setup.php">Setup Images</a>&nbsp;&nbsp;';
echo '<a href="upload.php">Upload Images</a>&nbsp;&nbsp;';
echo '<a href="addcomments.php">Add Comments</a>&nbsp;&nbsp;';
echo '<a href="setorder.php">Set Order</a>&nbsp;&nbsp;';
echo '<a href="sizeimages.php">Resize Images</a>&nbsp;&nbsp;';
echo '<a href="sizethumbs.php">Resize Thumbs</a>&nbsp;&nbsp;';
echo '<a href="delete.php">Delete Image</a>&nbsp;&nbsp;';
echo '<a href="clearall.php">Clear All</a>&nbsp;&nbsp;';
echo '<a href="addcomments.php?logout=1">Logout</a><br><br>';

//Fix for Internet Explorer
echo '<script src="../js/polyfills.js"></script>
<script src="../js/webp-hero.bundle.js"></script>
<script>var webpMachine = new webpHero.WebpMachine();
webpMachine.polyfillDocument();</script>';
