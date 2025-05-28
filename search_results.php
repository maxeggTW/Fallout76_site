<?php
// search_results.php
require_once("Connections/conn_db.php"); // 載入資料庫連線
(!isset($_SESSION)) ? session_start() : "";

// 獲取從 URL 傳來的搜尋關鍵字
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$search_results = [];
$image_directory = './IMAGES/';

if ($search_query !== '') {
    try {
        $stmt = $link->prepare(
            "SELECT p_id, p_name, p_ename, p_price, p_image_path, p_content
             FROM product
             WHERE (p_name LIKE :query OR p_ename LIKE :query) AND p_open = 1
             ORDER BY p_id DESC"
        );
        $stmt->bindValue(':query', '%' . $search_query . '%', PDO::PARAM_STR);
        $stmt->execute();
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("搜尋查詢錯誤: " . $e->getMessage());
        $db_error_message = "搜尋時發生錯誤，請稍後再試。";
    }
}
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>搜尋結果 - "<?php echo htmlspecialchars($search_query); ?>"</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.2.1/css/all.css">
  <link rel="stylesheet" href="./Website_02.css" />
  <style>
    /* 專為搜尋結果頁面設計的簡單樣式 */
    .search-results-page {
        padding: 50px 0;
        background-color: #f3f1ce;
        min-height: 60vh;
    }
    .search-results-title {
        color: #000;
        margin-bottom: 30px;
    }
    .search-results-title span {
        color: #e3242b;
        font-style: italic;
    }
    .result-link {
        text-decoration: none; /* 移除連結底線 */
        color: inherit; /* 讓連結繼承父層的文字顏色 */
        display: block; /* 讓連結變成區塊元素，才能設定寬高和邊距 */
        margin-bottom: 20px;
    }
    .result-item {
        display: flex;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
        transition: transform 0.2s ease;
    }
     .result-link:hover .result-item {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .result-item-img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        flex-shrink: 0;
    }
    .result-item-content {
        padding: 20px;
        color: #000;
    }
    .result-item-content h5 {
        font-weight: bold;
    }
    .result-item-content .price {
        color: #e3242b;
        font-size: 1.2rem;
        font-weight: bold;
        margin-top: 10px;
    }
    .no-results {
        color: #333;
        font-size: 1.2rem;
    }
  </style>
</head>
<body>
   <header>
    <?php require_once("product_navbar.php"); ?>
 </header>

  <main class="search-results-page">
    <div class="container">
      <h2 class="search-results-title">關於 "<span><?php echo htmlspecialchars($search_query); ?></span>" 的搜尋結果</h2>
      
      <?php if (isset($db_error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($db_error_message); ?></div>
      <?php elseif (empty($search_results)): ?>
        <p class="no-results">找不到符合 "<?php echo htmlspecialchars($search_query); ?>" 的商品，試試看別的關鍵字吧！</p>
      <?php else: ?>
        <div class="results-list">
          <?php foreach ($search_results as $item): ?>
            <a href="main_product.php?id=<?php echo $item['p_id']; ?>" class="result-link">
            <div class="result-item">
              <img src="<?php echo $image_directory . htmlspecialchars($item['p_image_path']); ?>" alt="<?php echo htmlspecialchars($item['p_name']); ?>" class="result-item-img">
              <div class="result-item-content">
                <h5><?php echo htmlspecialchars($item['p_name']); ?></h5>
                <h6><?php echo htmlspecialchars($item['p_ename']); ?></h6>
                <p><?php echo mb_substr(htmlspecialchars($item['p_content']), 0, 80, 'UTF-8'); ?>...</p>
                <div class="price">$<?php echo number_format($item['p_price']); ?></div>
              </div>
            </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div class="prevpage">
    <a href="index_purchase.php" style="text-align: center;">回上一頁</a>
    </div>
  </main>
  
  <section class="footer">
    <?php require_once("footer.php") ?>
  </section>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
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