<?php
require_once("Connections/conn_db.php"); // 載入 PDO 連線 ($link 變數)
(!isset($_SESSION)) ? session_start() : ""; // 如果需要 Session

$image_directory = './IMAGES/';

// --- PHP: 獲取商品資料 ---
$featured_products = [];
$preorder_products = [];
$hot_selling_products = [];

try {
  // 1. 獲取主打商品
  $stmt_featured = $link->prepare(
    "SELECT p_id, p_name, p_ename, p_price, p_image_path, p_content
         FROM product
         WHERE p_category = 'featured' AND p_open = 1
         ORDER BY p_id DESC LIMIT 4"
  );
  $stmt_featured->execute();
  $featured_products = $stmt_featured->fetchAll(PDO::FETCH_ASSOC);

  // 2. 獲取限量預購商品
  $stmt_preorder = $link->prepare(
    "SELECT p_id, p_name, p_price, p_image_path, p_content
         FROM product
         WHERE p_category = 'preorder' AND p_open = 1
         ORDER BY p_id DESC LIMIT 5"
  );
  $stmt_preorder->execute();
  $preorder_products = $stmt_preorder->fetchAll(PDO::FETCH_ASSOC);

  // 3. 獲取強力熱銷商品
  $stmt_hot_selling = $link->prepare(
    "SELECT p_id, p_name, p_price, p_image_path, p_content
         FROM product
         WHERE p_category = 'hot_selling' AND p_open = 1
         ORDER BY p_id DESC LIMIT 5"
  );
  $stmt_hot_selling->execute();
  $hot_selling_products = $stmt_hot_selling->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
  error_log("資料庫查詢錯誤: " . $e->getMessage());
  $db_error_message = "哎呀！目前無法載入商品，請稍後再試。";
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fallout 76 網站</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://use.fontawesome.com/releases/v6.2.1/css/all.css" />
  <link rel="stylesheet" href="./Website_02.css" />
</head>
<body>
  <section class="header">
    <?php require_once("product_navbar.php"); ?>

    <div class="header-image">
      <img
        src="./IMAGES/vault_boy.png"
        alt="header-image"
        class="header-image" />
      <div class="header-text">
        <h1 class="header-text-title1">Fallout 76</h1>
        <h1 class="header-text-title2">周邊商品</h1>
        <p>
          穿梭廢土也要風格滿分！探索我們獨家推出的《異塵餘生》 （Fallout）主題商品系列，從服飾、配件到收藏品一應俱全!
        </p>
      </div>
    </div>
    <div class="scroll-down-container">
      <div class="pipboy-arrow-down" title="向下捲動"></div>
    </div>
  </section>

  <section class="content">
    <div class="container">
      <?php if (isset($db_error_message)): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo htmlspecialchars($db_error_message); ?>
        </div>
      <?php endif; ?>
      <div class="featured">
         <?php require_once("product_featured.php"); ?>
      </div>
    
      <div class="preorder">
        <?php require_once("product_preorder.php"); ?>
      </div>

      <div class="hot-sale">
        <?php require_once("product_hotsale.php"); ?>
      </div>
  </section>

  <section class="scontent">
   <?php require_once("scontent.php"); ?>
  </section>

  <section class="outsidelink">
    <?php require_once("outsidelink.php"); ?>
  </section>

  <section class="footer">
    <?php require_once("footer.php"); ?>
  </section>

  <script>
    // 這是您原本的向下捲動箭頭 JS
    document.addEventListener("DOMContentLoaded", function() {
      const scrollDownContainer = document.querySelector('.scroll-down-container');
      const footer = document.querySelector('.footer');
      if (scrollDownContainer) {
        window.addEventListener('scroll', function() {
          if (!footer) return;
          const footerRect = footer.getBoundingClientRect();
          if (footerRect.top <= window.innerHeight) {
            scrollDownContainer.style.transform = 'translateX(-50%) rotate(180deg)';
          } else {
            scrollDownContainer.style.transform = 'translateX(-50%)';
          }
        });
        scrollDownContainer.addEventListener('click', function() {
          if (!footer) return;
          const footerRect = footer.getBoundingClientRect();
          if (footerRect.top <= window.innerHeight) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
          } else {
            window.scrollBy({ top: window.innerHeight, behavior: 'smooth' });
          }
        });
      }
    });
  </script>

  <script>
    // 這是搜尋框的 JS
    document.addEventListener("DOMContentLoaded", function() {
        const searchContainer = document.getElementById('searchForm');
        const searchBtn = document.getElementById('searchBtn');
        const searchInput = document.getElementById('searchInput');

        if(searchContainer && searchBtn && searchInput) {
            searchBtn.addEventListener('click', () => {
                searchContainer.classList.toggle('active');
                if (searchContainer.classList.contains('active')) {
                    searchInput.focus();
                }
            });

            searchContainer.addEventListener('submit', (event) => {
                if (searchInput.value.trim() === '') {
                    event.preventDefault(); 
                    searchContainer.classList.remove('active');
                }
            });

            document.addEventListener('click', (event) => {
                if (!searchContainer.contains(event.target)) {
                    searchContainer.classList.remove('active');
                }
            });
        }
    });
   </script>

  <?php require_once("JSfile.php"); ?>
</body>
</html>