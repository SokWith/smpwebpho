<?php
session_start(); // 开始会话

$albumDir = 'albums/';
$albums = glob($albumDir . '*.txt');
$correctPassword = 'addroot'; // 设置固定的口令

// 处理备份相册的操作
if (isset($_POST['backup']) && isset($_SESSION['password']) && $_SESSION['password'] === $correctPassword) {
    $zip = new ZipArchive();
    $backupFileName = $albumDir . 'backup_' . date('YmdHis') . '.zip';
    if ($zip->open($backupFileName, ZipArchive::CREATE) === TRUE) {
        foreach ($albums as $album) {
            $zip->addFile($album, basename($album));
        }
        $zip->close();

        // 触发下载
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($backupFileName) . '"');
        header('Content-Length: ' . filesize($backupFileName));
        ob_clean(); // 清除缓冲区
        flush(); // 刷新输出缓冲
        readfile($backupFileName);
        // 删除服务器上的备份文件
        unlink($backupFileName);
        exit;
    } else {
        echo "<p>备份失败。</p>";
    }
}

// 检查会话中的口令或表单提交的口令
if (isset($_SESSION['password']) && $_SESSION['password'] === $correctPassword) {
    // 口令正确，显示管理界面

    // 显示备份按钮和返回首页按钮
    echo "<div style='display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;'>";
    echo "<a href='index.php' style='text-decoration: none;'>";
    echo "<button type='button'>返回首页</button>";
    echo "</a>";
    echo "<form method='post'>";
    echo "<input type='submit' name='backup' value='备份相册'>";
    echo "</form>";
    echo "</div>";

    // 处理创建新相册的操作
    if (isset($_POST['create']) && !empty($_POST['newAlbumName'])) {
        $newAlbumName = trim($_POST['newAlbumName']);
        $newAlbumFile = $albumDir . $newAlbumName . '.txt';
        if (!file_exists($newAlbumFile)) {
            file_put_contents($newAlbumFile, '');
            echo "<p>新相册 '{$newAlbumName}' 创建成功。</p>";
        } else {
            echo "<p>相册 '{$newAlbumName}' 已存在。</p>";
        }
    }

    // 如果设置了album参数，显示相册内容管理界面
    if (isset($_GET['album'])) {
        $albumName = basename($_GET['album'], '.txt');
        $albumFile = $albumDir . $albumName . '.txt';

        // 显示相册内容管理界面
        echo "<h2>管理相册: $albumName</h2>";
        echo "<a href='?'>返回相册列表</a><br>";

        // 处理增加图片的操作
        if (isset($_POST['add'])) {
            $imageUrls = explode("\n", $_POST['imageUrls']); // 从文本区域获取多个URL
            foreach ($imageUrls as $imageUrl) {
                if (!empty($imageUrl)) {
                    file_put_contents($albumFile, trim($imageUrl) . "\n", FILE_APPEND);
                }
            }
        }

        // 处理删除图片的操作
        if (isset($_POST['delete'])) {
            $imageUrl = $_POST['imageUrl'];
            $images = file($albumFile, FILE_IGNORE_NEW_LINES);
            $images = array_filter($images, function ($line) use ($imageUrl) {
                return trim($line) !== trim($imageUrl);
            });
            file_put_contents($albumFile, implode("\n", $images) . "\n");
        }

       // 显示图片和删除按钮
      $images = file($albumFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
      echo "<div style='display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px;'>";
      foreach ($images as $imageUrl) {
        echo "<div style='text-align: center;'>";
        // 包裹<a>标签以创建可点击的图片链接
        echo "<a href='$imageUrl' target='_blank'>"; // target='_blank'使链接在新标签页打开
        echo "<img src='$imageUrl' alt='Thumbnail' width='100' height='100'>";
        echo "</a>"; // 关闭<a>标签
        echo "<form method='post' style='display: inline;' onsubmit='return confirmDelete();'>";
        echo "<input type='hidden' name='imageUrl' value='$imageUrl'>";
        echo "<input type='submit' name='delete' value='删除'>";
        echo "</form>";
        echo "</div>";
      }
      echo "</div>";

        // 显示添加图片表单
        echo "<form method='post' style='margin-top: 20px;'>";
        echo "<textarea name='imageUrls' placeholder='输入图片URL，每行一个' style='width: 500px; height: 100px;'></textarea>";
        echo "<input type='submit' name='add' value='批量添加图片'>";
        echo "</form>";

    } else {
        // 显示相册列表
        echo "<h2>相册列表</h2>";
        echo "<div style='display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px;'>";
        foreach ($albums as $album) {
            $albumName = basename($album, '.txt');
            echo "<a href='?album=$albumName' style='text-align: center;'>$albumName</a>";
        }
        echo "</div>";

        // 显示创建新相册表单
        echo "<form method='post' style='margin-top: 20px;'>";
        echo "<input type='text' name='newAlbumName' placeholder='输入新相册名称'>";
        echo "<input type='submit' name='create' value='创建新相册'>";
        echo "</form>";
    }

} elseif (isset($_POST['password']) && $_POST['password'] === $correctPassword) {
    // 口令正确，保存口令到会话
    $_SESSION['password'] = $_POST['password'];
    // 重定向到相同页面，避免表单重复提交
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
} else {
    // 显示口令输入表单
    echo "<form method='post'>";
    echo "<input type='password' name='password' placeholder='输入口令'>";
    echo "<input type='submit' value='提交'>";
    echo "</form>";
}
?>

<!-- HTML 和 JavaScript 部分 -->
<html>
<head>
    <title>图片上传</title>
    <link rel="stylesheet" type="text/css" href="/styles.css">
    <!-- 引入image-compression库 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/browser-image-compression/2.0.2/browser-image-compression.min.js"></script>
    <!-- 其他头部信息 -->
</head>
<body>
    <!-- 相册管理界面的HTML代码 -->

    <!-- 加载外部JavaScript文件 -->
    <script src="imageUpload.js"></script>
</body>
</html>
