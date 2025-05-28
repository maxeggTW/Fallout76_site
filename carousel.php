<?php

// --- PHP 資料庫查詢開始 ---
// 建立廣告輪播carousel資料查詢
$SQLstring = "SELECT * FROM carousel WHERE caro_online=1 ORDER BY caro_sort";
$carousel_stmt = null; // 初始化
$carousel_data = [];   // 初始化
$totalSlides = 0;      // 初始化

// 確保 $link 存在且有效
if (isset($link) && $link) {
    try {
        $carousel_stmt = $link->query($SQLstring);
        if ($carousel_stmt) {
            // 使用 fetchAll 獲取所有數據，這樣可以安全地獲取數量並進行迭代
            $carousel_data = $carousel_stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalSlides = count($carousel_data);
        } else {
            // 可以選擇記錄錯誤或顯示一個消息
            error_log("Carousel query failed."); // 記錄錯誤到伺服器日誌
        }
    } catch (PDOException $e) {
        // 處理資料庫查詢錯誤
        error_log("Database error in carousel.php: " . $e->getMessage());
        // 可以選擇顯示一個對使用者友好的錯誤訊息，但避免顯示詳細錯誤
        // echo "<p>無法載入輪播內容，請稍後再試。</p>";
    }
} else {
    error_log("Database connection (\$link) not available in carousel.php");
    // echo "<p>資料庫連線錯誤。</p>";
}
// --- PHP 資料庫查詢結束 ---

// --- 開始輸出輪播區塊的 HTML ---
?>

<div class="header-top">
    <div class="logo">
        <img src="./IMAGES/Logo.png" alt="Fallout 76 Logo" class="game-logo" />
    </div>
    <div class="sign-in">
        <?php if (isset($_SESSION['login']) && $_SESSION['login']) { ?>
            <?php
            // 處理使用者名稱顯示 (使用 $_SESSION['email'])
            $displayName = 'User'; // 預設顯示名稱
            if (isset($_SESSION['email'])) {
                $email_address = $_SESSION['email'];
                $atPos = strpos($email_address, '@');
                if ($atPos !== false) {
                    $displayName = substr($email_address, 0, $atPos);
                } else {
                    $displayName = $email_address;
                }
            }
            ?>
            <ul class="navbar-nav ms-auto me-4" style="list-style: none; padding-left: 0; margin: 0; display: flex; align-items: center;">
                <li class="nav-item dropdown" style="font-size: 1.5rem ;">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                        <i class="fas fa-user-circle"></i>
                        <?php echo htmlspecialchars($displayName); // 顯示處理後的名稱 
                        ?>
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
            <button class="sign-in-btn" onclick="window.location.href='login.php';"> SIGN IN <i class="fas fa-user-circle"></i>
            </button>
        <?php } ?>
    </div>
</div>
<div class="carousel-arrows">
    <div class="arrow" id="prevBtn">&#10094;</div>
    <div class="arrow" id="nextBtn">&#10095;</div>
</div>

<div class="carousel">
    <?php
    // 檢查是否有從資料庫獲取到輪播項目
    if ($totalSlides > 0) {
        $slideIndex = 0;
        foreach ($carousel_data as $data) {
            $isActive = ($slideIndex == 0) ? 'active' : ''; // 標記第一個幻燈片為 active
    ?>
            <div class="carousel-slide <?php echo $isActive; ?>">
                <img src="./IMAGES/<?php echo htmlspecialchars($data['caro_pic']); ?>" alt="<?php echo htmlspecialchars($data['caro_title']); ?>" />
                <div class="carousel-content">
                    <?php
                    // --- 按鈕生成邏輯 ---
                    // 預設按鈕文字為標題
                    $button_text = htmlspecialchars($data['caro_button_text']);

                    $button_link = '#';
                    // 如果資料庫有 'caro_link' 欄位且內容不為空，則使用它
                    if (isset($data['caro_link']) && !empty($data['caro_link'])) {
                        $button_link = htmlspecialchars($data['caro_link']);
                    }

                    $button_class = 'buy-now-btn';
                    if (isset($data['caro_button_class']) && !empty($data['caro_button_class'])) {
                        $button_class = htmlspecialchars($data['caro_button_class']);
                    }

                    $final_link_classes = $button_class . ' dynamic-carousel-link'; // 加上 JS 用的 class

                    echo '<a href="' . $button_link . '" class="' . $final_link_classes . '">' . $button_text . '</a>';
                    ?>
                </div>
            </div>
    <?php
            $slideIndex++;
        } // 結束 foreach 迴圈
    } else {
        // 如果沒有輪播項目，可以顯示提示訊息
        echo '<div class="carousel-slide active" style="display: flex; justify-content: center; align-items: center; background-color: #333; color: white; height: 300px;">'; // 添加一些基本樣式
        echo '<p>目前沒有輪播內容。</p>';
        echo '</div>';
    }
    ?>
</div>

<div class="carousel-dots">
    <?php
    // 根據實際的幻燈片數量生成點
    if ($totalSlides > 0) {
        for ($dotIndex = 0; $dotIndex < $totalSlides; $dotIndex++) {
            $isDotActive = ($dotIndex == 0) ? 'active' : ''; // 標記第一個點為 active
    ?>
            <div class="dot <?php echo $isDotActive; ?>" data-index="<?php echo $dotIndex; ?>"></div>
    <?php
        } // 結束 for 迴圈
    }
    ?>
</div>

<div class="social-bar">
    <div class="social-text">避難所科技指引(社群):</div>
    <div class="social-icons">
        <div class="social-icon"><i class="fa-brands fa-instagram"></i></div>
        <div class="social-icon"><i class="fa-brands fa-facebook-f"></i></div>
        <div class="social-icon"><i class="fa-brands fa-youtube"></i></div>
        <div class="social-icon"><i class="fa-brands fa-discord"></i></div>
        <div class="social-icon"><i class="fa-brands fa-x-twitter"></i></div>
        <div class="social-icon"><i class="fa-brands fa-twitch"></i></div>
    </div>
</div>