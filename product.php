<?php
// 這個檔案預期 $product 變數已經存在且包含產品資料。
// **禁止**在這裡進行資料庫連線或查詢。

if (!isset($product) || !is_array($product)) {
  echo "<p>錯誤：產品資料未載入。Template Error.</p>";
  return;
}

// --- 從 $product 陣列提取圖片路徑和檔名 ---
$img_dir = $product['image_directory'] ?? './IMAGES/';
$main_image_filename = $product['img_file'] ?? null;
$main_image_url = $main_image_filename ? $img_dir . htmlspecialchars($main_image_filename) : $img_dir . 'placeholder.webp';

// --- 收集有效的縮圖檔名 ---
$thumbnail_filenames = [];
if (!empty($product['sub_img1'])) $thumbnail_filenames[] = $product['sub_img1'];
if (!empty($product['sub_img2'])) $thumbnail_filenames[] = $product['sub_img2'];
if (!empty($product['sub_img3'])) $thumbnail_filenames[] = $product['sub_img3'];
if (!empty($product['sub_img4'])) $thumbnail_filenames[] = $product['sub_img4'];

// --- 提取純文字產品名稱 (用於 alt) ---
$product_name_plain = strip_tags($product['p_name'] ?? '產品');

?>
<div class="container"> <?php // 這個 container 是 product section 內部的主要容器 
                        ?>
  <div class="product-showcase-container">
    <div class="product-images">
      <div class="thumbnail-gallery">
        <?php if (!empty($thumbnail_filenames)): ?>
          <?php foreach ($thumbnail_filenames as $index => $thumb_filename): ?>
            <?php
            $thumb_url = $img_dir . htmlspecialchars($thumb_filename);
            $main_src_for_thumb = $thumb_url; // 點擊縮圖時，主圖顯示對應的大圖
            $active_class = ($index === 0) ? ' active' : '';
            $alt_text = htmlspecialchars($product_name_plain) . " 縮圖 " . ($index + 1);
            ?>
            <img src="<?= $thumb_url ?>" alt="<?= $alt_text ?>" class="thumbnail<?= $active_class ?>" data-main-src="<?= $main_src_for_thumb ?>">
          <?php endforeach; ?>
        <?php else: ?>
          <p>沒有可用的縮圖。</p>
        <?php endif; ?>
      </div>
      <div class="main-image-area">
        <?php
        $initial_main_image_url = $main_image_url; // 頁面載入時顯示的主圖
        $main_alt_text = htmlspecialchars($product_name_plain) . " 主要圖片";
        ?>
        <img src="<?= $initial_main_image_url ?>" alt="<?= $main_alt_text ?>" id="mainProductImage" data-main-src="<?= $initial_main_image_url ?>">
      </div>
    </div>
    <div class="product-details">
      <h1><?php echo $product['p_name'] ?? '產品名稱'; ?></h1>
      <div class="price">NT$<?= number_format($product['p_price'] ?? 0, 0) ?></div>
      <div class="description">
        <h4>商品介紹</h4>
        <?php if (!empty($product['p_content'])) {
          // 1. trim() 去除頭尾多餘空白 (包括可能的換行)
          $product_content = trim($product['p_content']);
          // 2. htmlspecialchars() 將特殊字元轉為 HTML 實體，防止 XSS
          $safe_content = htmlspecialchars($product_content, ENT_QUOTES, 'UTF-8');
          // 3. nl2br() 將 \n 轉換為 <br> 標籤
          echo nl2br($safe_content);
        } else {
          echo '<p>尚無詳細介紹。</p>';
        } ?>
        <p class="notes">**請注意：** 由於螢幕顯示的差異，實際商品顏色可能與您看到的略有不同。</p>
      </div>
      <div class="coupon-info">
        <a href="#" class="coupon-link">折價券</a>
        <a href="#" class="coupon-link">查看折價券</a>
      </div>
      <div class="actions">
        <div class="quantity-selector">
          <button class="quantity-btn minus" type="button">-</button>
          <input type="number" value="1" min="1" max="49" class="quantity-input" id="qty" name="qty">
          <button class="quantity-btn plus" type="button">+</button>
        </div>
        <button class="add-to-cart-btn" onclick="addcart(<?php echo $product['p_id']; ?>)">加入購物車</button>
      </div>
    </div>
  </div>
</div>