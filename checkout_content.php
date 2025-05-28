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

<div class="container py-4">
    <h2 class="text-center mb-4">結帳</h2>

    <div class="mb-4 order-review-section">
        <h4 class="mb-3 d-md-none">您的商品</h4>
        <div class="row d-none d-md-flex text-muted border-bottom mb-2 pb-2 align-items-center">
            <div class="col-md-5">您的商品</div>
            <div class="col-md-2 text-center">價格</div>
            <div class="col-md-2 text-center">數量</div>
            <div class="col-md-2 text-center">小計</div>
            <div class="col-md-1"></div>
        </div>

        <?php
        if ($cart_items_count > 0) { // 確保有商品才進行迴圈
            foreach ($cart_items_data as $item) :
                $item_image_path = "./IMAGES/" . htmlspecialchars($item['img_file']);
                $item_name = htmlspecialchars($item['p_name']);
                $item_price = $item['p_price'];
                $item_qty = $item['qty'];
                $item_subtotal = $item_price * $item_qty;
                $item_id_for_removal = $item['cartid'];
        ?>
                <div class="row align-items-center mb-3 border-bottom pb-3 pt-2">
                    <div class="col-3 col-md-1 text-center">
                        <img src="<?php echo $item_image_path; ?>" class="img-fluid rounded" style="max-height: 60px; width:auto;" alt="<?php echo $item_name; ?>">
                    </div>
                    <div class="col-9 col-md-4">
                        <p class="mb-0 ms-2 ms-md-0 small"><?php echo $item_name; ?></p>
                    </div>
                    <div class="col-4 col-md-2 text-md-center mt-2 mt-md-0">
                        <span class="d-inline d-md-none small text-muted">價格: </span>NT$<?php echo number_format($item_price); ?>
                    </div>
                    <div class="col-4 col-md-2 text-md-center mt-2 mt-md-0">
                        <span class="d-inline d-md-none small text-muted">數量: </span><?php echo $item_qty; ?>
                    </div>
                    <div class="col-4 col-md-2 text-md-center mt-2 mt-md-0">
                        <span class="d-inline d-md-none small text-muted">小計: </span>NT$<?php echo number_format($item_subtotal); ?>
                    </div>
                    <div class="col-12 col-md-1 text-end text-md-center mt-2 mt-md-0">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" title="移除商品" onclick="btn_confirmLink('確認刪除此商品「<?php echo htmlspecialchars(addslashes($cart_data['p_name'])); ?>」?','shopcart_del.php?mode=1&cartid=<?php echo htmlspecialchars($cart_data['cartid']); ?>');">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
        <?php
            endforeach;
        }
        ?>
    </div>

    <form id="checkoutForm" name="checkoutForm" action="checkout_complete.php" method="POST">

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">收件人資訊</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="checkout_name" class="form-label">姓名 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="checkout_name" name="checkout_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="checkout_email" class="form-label">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="checkout_email" name="checkout_email" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="checkout_phone" class="form-label">電話 <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="checkout_phone" name="checkout_phone" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="checkout_mobile" class="form-label">手機</label>
                        <input type="tel" class="form-control" id="checkout_mobile" name="checkout_mobile">
                    </div>
                </div>
               
            </div>

        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">取貨方式</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="shippingMethod" id="storePickup" value="store_pickup" checked>
                    <label class="form-check-label" for="storePickup">
                        超商取貨 (7-11 / FamilyMart)
                    </label>
                </div>
                <div id="storeSelectionArea" class="ms-4">
                    <div class="d-flex mb-3">
                        <button type="button" class="btn btn-outline-primary me-2" onclick="selectStore('711');">
                            <img src="./IMAGES/7-11_logo.svg" alt="7-11" style="height: 30px;"> </button>
                        <button type="button" class="btn btn-outline-primary me-2" onclick="selectStore('familymart');">
                            <img src="./IMAGES/FamilyMart_logo.svg" alt="FamilyMart" style="height: 30px;"> </button>
                    </div>
                    <div id="selectedStoreInfo" class="border p-3 rounded bg-light">
                        <p class="mb-1 fw-bold">天佑門市</p>
                        <p class="mb-1 small">台中市西屯區中工二路196號198號1樓</p>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-1">重新選擇門市</button>
                        <input type="hidden" name="selected_store_id" id="selected_store_id" value="STORE_ID_HERE">
                        <input type="hidden" name="selected_store_name" id="selected_store_name" value="天佑門市">
                        <input type="hidden" name="selected_store_address" id="selected_store_address" value="台中市西屯區中工二路196號198號1樓">
                    </div>
                </div>

                <div class="form-check mt-3">
                    <input class="form-check-input" type="radio" name="shippingMethod" id="homeDelivery" value="home_delivery">
                    <label class="form-check-label" for="homeDelivery"><span class="text-danger"></span>
                        宅配到府 (需填寫收件地址)
                    </label>
                </div>
                 <div id="homeDeliveryAddressGroup" class="mb-3" style="display: none;">
                    <label for="checkout_address" class="form-label">收件地址 <span class="text-danger" id="address_required_star">*</span></label>
                    <input type="text" class="form-control" id="checkout_address" name="checkout_address" placeholder="請輸入完整的收件地址">
                </div>
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">付款方式</h5>
            </div>
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="cod" value="cod" checked>
                    <label class="form-check-label" for="cod">
                        貨到付款
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="paymentMethod" id="creditCard" value="credit_card">
                    <label class="form-check-label" for="creditCard">
                        信用卡付款 (一次付清)
                    </label>
                </div>
                
                 <div id="creditCardPaymentFields" class="mt-3 border p-3 rounded bg-light-subtle" style="display: none;">
                    <h6 class="mb-3">請輸入信用卡資訊</h6>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="cc_name" class="form-label">持卡人姓名 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cc_name" name="cc_name" placeholder="例：WANG XIAOMING">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="cc_number" class="form-label">信用卡號碼 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cc_number" name="cc_number" placeholder="XXXX-XXXX-XXXX-XXXX">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-7 mb-3">
                            <label for="cc_expiry" class="form-label">有效期限 (MM/YY) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cc_expiry" name="cc_expiry" placeholder="MM/YY">
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="cc_cvv" class="form-label">安全碼 (CVV) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cc_cvv"  name="cc_cvv" placeholder="XXX">
                        </div>
                    </div>
                    <p class="small text-muted mt-2 mb-0">
                        <i class="fas fa-lock"></i> 您的信用卡資訊將透過安全的支付閘道處理。
                    </p>
                </div>
                </div> 

            </div>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
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
                      <button type="submit" class="btn btn-danger w-100 mt-4 checkout-btn">下訂單</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
   function selectStore(storeType) {
        // 實際應用中，這裡會彈出門市選擇的地圖或列表
        // 並在選擇後更新 #selectedStoreInfo 的內容和隱藏的 input 欄位
        alert("選擇 " + storeType + " (此處應實作門市選擇功能)");
        // 範例更新
        document.getElementById('selectedStoreInfo').querySelector('p.fw-bold').textContent = storeType === '711' ? '7-11 範例門市' : '全家 範例門市';
        document.getElementById('selectedStoreInfo').querySelector('p.small').textContent = '範例門市地址';
    }

    // 監聽取貨方式的變化，來顯示/隱藏門市選擇區域或地址欄位
    document.querySelectorAll('input[name="shippingMethod"]').forEach((elem) => {
        elem.addEventListener("change", function(event) {
            var selectedValue = event.target.value;
            var storeSelectionArea = document.getElementById("storeSelectionArea");
            var homeDeliveryAddressGroup = document.getElementById("homeDeliveryAddressGroup");
            var checkoutAddressInput = document.getElementById("checkout_address");
            var addressRequiredStar = document.getElementById("address_required_star");

            if (selectedValue === "store_pickup") {
                if (storeSelectionArea) storeSelectionArea.style.display = "block"; // 顯示超商選擇區域
                if (homeDeliveryAddressGroup) homeDeliveryAddressGroup.style.display = "none"; // 隱藏地址輸入框群組
                if (checkoutAddressInput) checkoutAddressInput.required = false; // 地址設為非必填
                if (addressRequiredStar) addressRequiredStar.style.display = "none"; // 隱藏地址欄位星號

            } else if (selectedValue === "home_delivery") {
                if (storeSelectionArea) storeSelectionArea.style.display = "none"; // 隱藏超商選擇區域
                if (homeDeliveryAddressGroup) homeDeliveryAddressGroup.style.display = "block"; // 顯示地址輸入框群組
                if (checkoutAddressInput) checkoutAddressInput.required = true; // 地址設為必填
                if (addressRequiredStar) addressRequiredStar.style.display = "inline"; // 顯示地址欄位星號
            }
        });
    });
      document.querySelectorAll('input[name="paymentMethod"]').forEach((elem) => {
        elem.addEventListener("change", function(event) {
            var selectedPaymentMethod = event.target.value;
            var creditCardFieldsDiv = document.getElementById("creditCardPaymentFields");

            // 獲取信用卡相關的 input 元素 (用於設定 required 屬性)
            var ccNameInput = document.getElementById("cc_name");
            var ccNumberInput = document.getElementById("cc_number");
            var ccExpiryInput = document.getElementById("cc_expiry");
            var ccCvvInput = document.getElementById("cc_cvv");

            if (selectedPaymentMethod === "credit_card") {
                if (creditCardFieldsDiv) creditCardFieldsDiv.style.display = "block";
                // 當信用卡付款被選中時，這些欄位應為必填
                // 注意：實際應用中，這些欄位的驗證和 'required' 屬性管理
                // 通常由支付閘道的 JavaScript SDK 處理。
                if (ccNameInput) ccNameInput.required = true;
                if (ccNumberInput) ccNumberInput.required = true;
                if (ccExpiryInput) ccExpiryInput.required = true;
                if (ccCvvInput) ccCvvInput.required = true;

            } else {
                if (creditCardFieldsDiv) creditCardFieldsDiv.style.display = "none";
                // 其他付款方式，信用卡欄位非必填
                if (ccNameInput) ccNameInput.required = false;
                if (ccNumberInput) ccNumberInput.required = false;
                if (ccExpiryInput) ccExpiryInput.required = false;
                if (ccCvvInput) ccCvvInput.required = false;
            }
        });
    });


    // DOM載入完成後，模擬一次change事件，以確保初始狀態正確
    document.addEventListener('DOMContentLoaded', function() {
        var selectedShippingMethod = document.querySelector('input[name="shippingMethod"]:checked');
        if (selectedShippingMethod) {
            // 手動觸發一次 'change' 事件來設定初始的顯示狀態
            var event = new Event('change');
            selectedShippingMethod.dispatchEvent(event);
        }
   
     // 設定 paymentMethod 的初始狀態
        var selectedPaymentMethodRadio = document.querySelector('input[name="paymentMethod"]:checked');
        if (selectedPaymentMethodRadio) {
            selectedPaymentMethodRadio.dispatchEvent(new Event('change'));
        }
    });
</script>