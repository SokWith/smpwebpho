<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>简易网络相册</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .albums-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* 创建4列 */
            gap: 10px; /* 设置网格项之间的间隙 */
        }
        .albums-grid li {
            list-style: none; /* 移除默认列表样式 */
        }
    </style>
</head>
<body>
<a href="manage_albums.php">管理相册</a> <!-- 新增的管理相册链接 -->
<ul class="albums-grid">
<?php
$albumDir = 'albums/';
$albums = glob($albumDir . '*.txt');
foreach ($albums as $index => $album) {
    $albumName = basename($album, '.txt');
    $imageUrls = file($album, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $thumbnailUrl = $imageUrls[0]; // 假设每个.txt文件的第一行是缩略图

    // 缩略图现在被包裹在一个链接中，点击可以打开相册
    echo "<li><a href='show_album.php?album=$albumName'>";
    echo "<img src='$thumbnailUrl' alt='Thumbnail' class='album-thumbnail'></a>";
    echo "<a href='show_album.php?album=$albumName'>$albumName</a></li>";

    // 在每4个相册后添加一个新行
    if (($index + 1) % 4 == 0) {
        echo "</ul><ul class='albums-grid'>";
    }
}
echo "</ul>";
?>
</body>
</html>
