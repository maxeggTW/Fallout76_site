<?php
// PDO SQL連線指令
$dsn="mysql:host=localhost;dbname=fallout76;charset=utf8";
$user="sales";
$password="123456";
$link=new PDO($dsn,$user,$password);
date_default_timezone_set("Asia/Taipei");
// // 在建立 $link (PDO 物件) 之後加入這行
// $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
