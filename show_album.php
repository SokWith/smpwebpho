<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>相册展示</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
if (isset($_GET['album'])) {
    $albumName = $_GET['album'];
    $albumFile = "albums/$albumName.txt";
    $imageUrls = file($albumFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    echo "<div class='waterfall'>";
    foreach ($imageUrls as $imageUrl) {
        // 每张图片都被一个链接包裹，点击后可以在新标签页中完整打开图片
        echo "<div class='item'><a href='$imageUrl' target='_blank'><img src='$imageUrl' alt='Photo'></a></div>";
    }
    echo "</div>";
}
?>
</body>
</html>
