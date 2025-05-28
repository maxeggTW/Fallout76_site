<?php
//  載入設定和連線
require_once("Connections/conn_db.php");
require_once("php_lib.php");
if (!isset($_SESSION)) {
  session_start();
}

//  初始化變數
$product_id = null;
$product = null;
$error_message = null;
$image_directory = './IMAGES/';
$random_products = [];

//  從 URL 獲取產品 ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $product_id = (int)$_GET['id'];
} else {
  $error_message = "未指定有效的產品 ID。";
}

//  如果產品 ID 有效且先前沒有錯誤，則執行資料查詢
if ($product_id && empty($error_message)) {
  if (isset($link) && $link instanceof PDO) {
    try {
      $sql = "SELECT
                        p.p_id, p.p_name, p.p_price, p.p_open, p.p_content, p.p_date, p.p_category, p.p_detail,
                        pi.img_id, pi.img_file, pi.sub_img1, pi.sub_img2, pi.sub_img3, pi.sub_img4
                    FROM product AS p
                    LEFT JOIN product_img AS pi ON p.p_id = pi.p_id
                    WHERE p.p_id = :product_id AND p.p_open = 1
                    ORDER BY  pi.img_id ASC
                    LIMIT 1";

      $stmt = $link->prepare($sql);
      $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
      $stmt->execute();
      $product = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($stmt) $stmt->closeCursor();

      if ($product) {
        $product['image_directory'] = $image_directory;
      } else {
        $error_message = "找不到您要的產品，或該產品目前未開放。";
      }
    } catch (PDOException $e) {
      error_log("DB PDOException in main_product.php for product_id " . $product_id . ": " . $e->getMessage() . " (SQL being run: " . $sql . ")");
      // 對使用者顯示較通用的錯誤訊息
      $error_message = "系統忙碌中，無法載入產品資料，請稍後再試。";
    } catch (Exception $e) {
      error_log("General Exception in main_product.php for product_id " . $product_id . ": " . $e->getMessage());
      // 對使用者顯示較通用的錯誤訊息
      $error_message = "系統發生非預期錯誤，請稍後再試。";
    }
  } else {
    $error_message = "資料庫連線設定有誤。"; // 這個錯誤通常是給開發者看的
    error_log("Database connection object (\$link) not available or not a PDO instance in main_product.php.");
  }
}
?>
  <?php $stmt_random = $link->prepare(
        "SELECT p_id, p_name, p_ename, p_price, p_image_path, p_content
         FROM product
         WHERE p_open = 1
         ORDER BY RAND() -- ✨ 關鍵：使用 RAND() 進行隨機排序
         LIMIT 5"        // ✨ 關鍵：限制數量為 5
    );
    $stmt_random->execute();
    $random_products = $stmt_random->fetchAll(PDO::FETCH_ASSOC);?>
    
 <!-- 資料處理完畢 -->
<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo ($product && isset($product['p_name'])) ? htmlspecialchars($product['p_name']) . ' - ' : '產品詳情 - '; ?>Fallout 76 網站</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="./Website_03.css" /> 
</head>

<body>
  <section class="header">
    <?php require_once("item_header.php")?>
  </section>

  <section class="content">
    <?php require_once("item_content.php")?>
  </section>

  <section class="scontent">
   <?php require_once("item_scontent.php") ?>
  </section>

  <section class="outsidelink">
    <?php require_once("outsidelink.php") ?>
  </section>

  <section class="footer">
    <?php require_once("footer.php") ?>
  </section>
   <!-- 引入javascript -->
  <?php require_once("JSfile.php"); ?>

  <script>
    // 等待整個 HTML 文件都載入並解析完畢後再執行
    document.addEventListener('DOMContentLoaded', function() {
      // 選取所有的縮圖元素
      const thumbnails = document.querySelectorAll('.thumbnail-gallery .thumbnail');
      // 選取主圖顯示區的圖片元素
      const mainImage = document.getElementById('mainProductImage');

      // 確保找到了縮圖和主圖元素，避免錯誤
      if (thumbnails.length > 0 && mainImage) {
        // 為每一張縮圖加上點擊事件監聽器
        thumbnails.forEach(function(thumb) {
          thumb.addEventListener('click', function() {
            // 1. 從被點擊的縮圖讀取 data-main-src 屬性，取得大圖路徑
            const mainImageSrc = thumb.getAttribute('data-main-src');
            // 2. 將主圖的 src 屬性更新為新的大圖路徑
            mainImage.setAttribute('src', mainImageSrc);
            // 3. 更新縮圖的選中狀態 (active class)
            thumbnails.forEach(function(innerThumb) {
              innerThumb.classList.remove('active');
            });
            thumb.classList.add('active');
          });
        });
      } else {
        // 如果沒找到元素，可以在控制台顯示警告訊息 (如果 product.php 沒有被正確載入或其內部元素不存在)
        // console.warn("找不到產品展示區的縮圖或主圖元素。");
      }
    });
     $('.product-details .quantity-selector .quantity-btn.plus').on('click', function() {
        var $input = $(this).siblings('.quantity-input'); // 找到與按鈕同層的 .quantity-input (現在它也有 id="qty")
        var currentValue = parseInt($input.val());
        var maxQty = parseInt($input.attr('max')) || 49; // 從 input 的 max 屬性讀取上限，預設49

        if (isNaN(currentValue)) { // 如果輸入的不是數字，預設為0，這樣+1後會是1
            currentValue = 0;
        }

        if (currentValue < maxQty) {
            $input.val(currentValue + 1);
            // 不需要 .trigger('change')，因為這個輸入框的值最終是由 addcart() 讀取的
        } else {
            alert("單次購買數量已達上限 " + maxQty + " 件。");
        }
    });

    // 監聽 .quantity-selector 內部的 .quantity-btn.minus 按鈕點擊事件
    $('.product-details .quantity-selector .quantity-btn.minus').on('click', function() {
        var $input = $(this).siblings('.quantity-input');
        var currentValue = parseInt($input.val());
        var minQty = parseInt($input.attr('min')) || 1; // 從 input 的 min 屬性讀取下限，預設1

        if (isNaN(currentValue)) {
            currentValue = minQty + 1; // 如果不是數字，且要減少，可以設為比 minQty 大一點的值再減
        }

        if (currentValue > minQty) {
            $input.val(currentValue - 1);
        }
    });

    // (建議) 監聽數量輸入框的直接修改，確保其值在有效範圍內
    $('.product-details .quantity-selector .quantity-input').on('change input', function() { // 監聽 change 和 input 事件
        var currentValue = parseInt($(this).val());
        var minQty = parseInt($(this).attr('min')) || 1;
        var maxQty = parseInt($(this).attr('max')) || 49;

        if (isNaN(currentValue)) {
            $(this).val(minQty); // 如果輸入無效，設為最小值
            return; // 避免後續判斷出錯
        }
        if (currentValue < minQty) {
            $(this).val(minQty);
            // alert("數量至少為 " + minQty + " 件。"); // 可選提示
        } else if (currentValue > maxQty) {
            $(this).val(maxQty);
            alert("單次購買數量上限為 " + maxQty + " 件。");
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
</body>

</html>