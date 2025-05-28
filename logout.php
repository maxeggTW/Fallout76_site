<?php (!isset($_SESSION)) ? session_start() : ""; ?>
<?php
 $_SESSION['login'] = null;
 $_SESSION['emailid'] = null;
 $_SESSION['email'] = null;
 unset($_SESSION['login']);
 unset($_SESSION['emailid']);
 unset($_SESSION['email']);
 $sPath="index_P01.php";
 //header(sprintf("Location:%s", $sPath));
 // php 5.2.6舊版採用下列方法
 echo "<script>window.location.href = '" . $sPath . "';</script>";
?>