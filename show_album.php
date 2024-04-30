<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>相册展示</title>
    <style>
        .waterfall {
            column-count: 4;
            column-gap: 10px;
        }
        .item {
            display: inline-block;
            margin-bottom: 10px;
            break-inside: avoid;
        }
        .item img {
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
        }
        #bigimg {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        #bigimg img {
            max-width: 90%;
            max-height: 90%;
            margin-top: 5%;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<?php
if (isset($_GET['album'])) {
    $albumName = $_GET['album'];
    $albumFile = "albums/$albumName.txt";
    $imageUrls = file($albumFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    echo "<div class='waterfall'>";
    foreach ($imageUrls as $imageUrl) {
        echo "<div class='item'><img src='$imageUrl' alt='Photo'></div>";
    }
    echo "</div>";
}
?>
<div id="bigimg" onclick="closeBigImg();"></div>
<script>
    var images = document.querySelectorAll('.item img');
    var bigImg = document.getElementById('bigimg');

    images.forEach(function(image) {
        image.addEventListener('click', function() {
            bigImg.innerHTML = '<img src="' + this.src + '">';
            bigImg.style.display = 'block';
        });
    });

    function closeBigImg() {
        bigImg.style.display = 'none';
        bigImg.innerHTML = '';
    }
</script>
</body>
</html>
