<?php

// 檢查資料庫連線 (這是好的習慣)
if (!isset($link) || !($link instanceof PDO)) {
    // 在實際部署時，不應直接 die() 輸出此訊息給使用者，而應記錄錯誤並顯示通用錯誤頁面
    die("<p style='color:red; font-weight:bold;'>錯誤：cart_content.php 中資料庫連線 (\$link) 無效或未設定！</p>");
}

$user_ip = $_SERVER['REMOTE_ADDR'];

// SQL 查詢
$SQLstring_final = "SELECT
                        c.cartid,
                        c.p_id,
                        c.qty,
                        p.p_name,
                        p.p_price,
                        (SELECT pi_inner.img_file FROM product_img pi_inner WHERE pi_inner.p_id = c.p_id LIMIT 1) AS img_file
                    FROM
                        cart c
                    INNER JOIN
                        product p ON c.p_id = p.p_id
                    WHERE
                        c.ip = :user_ip AND c.orderid IS NULL
                    ORDER BY
                        c.cartid DESC";

$stmt = $link->prepare($SQLstring_final);
$stmt->bindParam(':user_ip', $user_ip, PDO::PARAM_STR);
$execute_success = $stmt->execute();

$cart_items_count = 0;
$cart_items_data = []; // 用來儲存查詢結果
$ptotal = 0; // 商品小計

if ($execute_success) {
    $cart_items_count = $stmt->rowCount();
    if ($cart_items_count > 0) {
        $cart_items_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cart_items_data as $item) {
            $ptotal += $item['p_price'] * $item['qty'];
        }
    }
} else {
    // 查詢失敗的處理，例如記錄錯誤
    error_log("Cart content SQL query failed: " . print_r($stmt->errorInfo(), true));
    // 可以考慮在這裡設定一個錯誤訊息給使用者
}

$shipping_fee = 100; // 假設固定運費
$grand_total = $ptotal + $shipping_fee;

?>

<div class="container cart-page-container py-3 py-md-4">
    <h2 class="text-center mb-4 cart-title">購物車</h2>
    <div class="row">
        <?php if ($cart_items_count > 0) : ?>
            <div class="col-lg-8 cart-items-column mb-4 mb-lg-0">
                <div class="cart-table-header d-none d-md-flex row mx-0 text-muted mb-3 align-items-center">
                    <div class="col-md-5">您的商品</div>
                    <div class="col-md-2 text-center">價格</div>
                    <div class="col-md-2 text-center">數量</div>
                    <div class="col-md-2 text-center">小計</div>
                </div>

                <?php foreach ($cart_items_data as $cart_data) : ?>
                    <div class="cart-item-card card mb-3 shadow-sm">  <div class="row g-0 align-items-center p-2 p-md-3">
                            <div class="col-md-2 col-3 text-center"> <?php
                                $image_display_path = "./IMAGES/"; // 預設圖片路徑
                                if (!empty($cart_data['img_file'])) {
                                    $image_display_path = "./IMAGES/" . htmlspecialchars($cart_data['img_file']);
                                }
                                ?>
                                <img src="<?php echo $image_display_path; ?>" class="img-fluid rounded cart-item-image" alt="<?php echo htmlspecialchars($cart_data['p_name']); ?>">
                            </div>
                            <div class="col-md-3 col-9"> <div class="card-body py-md-2 px-md-3 p-0 ps-2 ps-md-0">
                                    <h5 class="card-title cart-item-name mb-1"><?php echo htmlspecialchars($cart_data['p_name']); ?></h5>
                                     <small class="text-muted d-md-none">價格: NT$<?php echo number_format($cart_data['p_price']); ?></small>
                                </div>
                            </div>
                            <div class="col-md-2 col-4 text-center price-col pt-2 pt-md-0 d-none d-md-block"> NT$<?php echo number_format($cart_data['p_price']); ?>
                            </div>
                            <div class="col-md-2 col-4 quantity-col pt-2 pt-md-0"> <div class="input-group quantity-input-group justify-content-center">
                                    <input type="number" class="form-control form-control-sm text-center cart-item-qty" name="qty[]" value="<?php echo htmlspecialchars($cart_data['qty']); ?>" min="1" max="49" cartid="<?php echo htmlspecialchars($cart_data['cartid']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-2 col-4 text-center subtotal-col pt-2 pt-md-0"> <span class="d-md-none">小計：</span>NT$<?php echo number_format($cart_data['p_price'] * $cart_data['qty']); ?>
                            </div>
                            <div class="col-md-1 col-12 text-center delete-col pt-2 pt-md-0"> <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" title="移除商品" onclick="btn_confirmLink('確認刪除此商品「<?php echo htmlspecialchars(addslashes($cart_data['p_name'])); ?>」?','shopcart_del.php?mode=1&cartid=<?php echo htmlspecialchars($cart_data['cartid']); ?>');">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                 <div class="d-flex justify-content-between mt-3">
                    <a href="index_purchase.php" class="btn btn-outline-primary">繼續購物</a>
                    <button type="button" class="btn btn-outline-secondary" onclick="btn_confirmLink('確定清空整個購物車?','shopcart_del.php?mode=2');">清空購物車</button>
                </div>
            </div> 

            <div class="col-lg-4 cart-summary-column">   <div class="card summary-card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">金額統計</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                商品小計
                                <span>NT$<?php echo number_format($ptotal); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                運費
                                <span>NT$<?php echo number_format($shipping_fee); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold total-amount px-0 pt-3">
                                總金額
                                <span>NT$<?php echo number_format($grand_total); ?></span>
                            </li>
                        </ul>
                        <a href="checkout.php" class="btn btn-danger w-100 mt-4 checkout-btn">立即付款</a>
                    </div>
                </div>
            </div> 

        <?php else : // 如果查詢失敗或購物車為空 ?>
            <div class="col-12 text-center empty-cart-message mt-5">
                <p class="display-6 my-4" style="color:rgb(244, 0, 0);">您目前購物車內沒有商品喔!</p>
                <a href="index_purchase.php" class="btn btn-lg btn-success">趕緊去選購吧~</a>
            </div>
        <?php endif; // 對應 if ($cart_items_count > 0) ?>

    </div> 
</div> 
