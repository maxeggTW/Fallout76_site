<?php
// navbar.php
// 確保資料庫連線變數 $link 存在，以便計算購物車數量
$cart_count = 0; // 預設購物車數量為 0
if (isset($link)) {
  try {
    $SQLstring = "SELECT * FROM cart WHERE orderid is NULL AND ip='" . $_SERVER['REMOTE_ADDR'] . "'";
    $cart_rs = $link->query($SQLstring);
    // **【關鍵修改】** 只有在查詢成功且回傳的是一個有效的物件時，才去計算數量
    if ($cart_rs instanceof PDOStatement) {
      $cart_count = $cart_rs->rowCount();
    }
  } catch (PDOException $e) {
    // 如果連線或查詢出錯，紀錄錯誤，但不要讓頁面崩潰
    error_log("Cart count query failed: " . $e->getMessage());
    $cart_count = 0; // 出錯時數量也設為 0
  }
}
?>
<nav class="navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <?php
    //讀取後台購物車內的產品數量
    $SQLstring = "SELECT * FROM cart WHERE orderid is NULL AND ip='" . $_SERVER['REMOTE_ADDR'] . "'";
    $cart_rs = $link->query($SQLstring);
    ?>
    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="logoimg">
        <a href="index_P01.php" style="display: block;" class="img-logo"><img src="./IMAGES/logo.png" alt="logo" class="logo" /></a></div>
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index_purchase.php">瀏覽所有商品</a></li>
        <li class="nav-item"><a class="nav-link" href="#">玩家特區</a></li>
        <li class="nav-item"><a class="nav-link" href="#">遊戲攻略</a></li>
        <li class="nav-item">
          <a class="nav-link" href="index_P01.php">回首頁</a>
        </li>
      </ul>
      <ul class="navbar-icon ul">
        <div class="nav-item">
           <form class="search-container" id="searchForm" action="search_results.php" method="get">
            <input type="text" class="search-input" id="searchInput" name="query" placeholder="搜尋商品..." required>
            <button type="button" class="search-btn" id="searchBtn">
              <i class="fa-solid fa-magnifying-glass"></i>
            </button>
          </form>
          <?php if (isset($_SESSION['login']) && $_SESSION['login']) { ?>
            <?php
            $displayName = 'User';
            if (isset($_SESSION['email'])) {
              $email_address = $_SESSION['email'];
              $atPos = strpos($email_address, '@');
              $displayName = ($atPos !== false) ? substr($email_address, 0, $atPos) : $email_address;
            }
            ?>
            <ul class="navbar-nav ms-auto">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                  <i class="fa-solid fa-user"></i>
                  <?php echo htmlspecialchars($displayName); ?>
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="orderlist.php">Order List</a></li>
                  <li><a class="dropdown-item" href="profile.php">Edit Profile</a></li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item" href="#" onclick="return btn_confirmLink('請確認是否要登出','logout.php');">Log Out</a></li>
                </ul>
              </li>
            </ul>
          <?php } else { ?>
            <a class="nav-link" href="login.php" title="登入/註冊"><i class="fa-solid fa-user"></i></a>
          <?php } ?>
          <a class="nav-link" href="cart.php"><i class="fa-solid fa-basket-shopping"><span class="badge text-bg-info"><?php echo ($cart_rs) ? $cart_rs->rowCount() : ''; ?></i></a>
        </div>
      </ul>
    </div>
  </div>
</nav>