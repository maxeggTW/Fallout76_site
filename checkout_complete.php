<?php
//  載入設定和連線
require_once("Connections/conn_db.php");
require_once("php_lib.php");
if (!isset($_SESSION)) {
    session_start();
}


?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fallout 76 網站</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="./Website_03.css" />
</head>

<body>
    <section class="header">
        <?php require_once("item_header.php") ?>
    </section>

    <section class="content py-5">
        <div class="container text-center">
            <div class="thumbsupimg mb-4"> <img src="./IMAGES/thumbsup.svg" alt="購買成功圖示" style="max-width: 150px; height: auto;"> </div>
            <h1 class="mb-3" style="color: red;">購買成功!感謝您的購買</h1>
            <p class="lead mb-4"> 訂單編號: <span class="fw-bold">87631145140857</span> </p>
            <div class="backorcontinue">
                <a href="index_P01.php" class="btn btn-lg btn-outline-secondary me-2">回首頁</a> <a href="index_purchase.php" class="btn btn-lg btn-success">繼續購買</a>
            </div>
        </div>
    </section>


    <section class="footer">
        <?php require_once("footer.php") ?>
    </section>

</body>

</html>