<?php
error_reporting(E_ALL); // 顯示所有錯誤
ini_set('display_errors', 1); // 將錯誤顯示在瀏覽器上 (僅限開發環境)

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json;charset=utf-8');

require_once('Connections/conn_db.php'); // 確保 $link 已正確初始化

$response = ['c' => '0', 'm' => '初始錯誤，請求未處理。']; // 預設回應

if (!isset($link) || !($link instanceof PDO)) {
    $response['m'] = '資料庫連線物件 ($link) 無效！';
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_GET['p_id']) && isset($_GET['qty'])) {
    $p_id_raw = $_GET['p_id'];
    $qty_raw = $_GET['qty'];
    $u_ip = $_SERVER['REMOTE_ADDR'];

    // 基本驗證和過濾 (非常重要，防止 SQL 注入的前提)
    $p_id = filter_var($p_id_raw, FILTER_VALIDATE_INT);
    $qty = filter_var($qty_raw, FILTER_VALIDATE_INT);

    if ($p_id === false || $p_id <= 0) {
        $response['m'] = "無效的產品 ID: " . htmlspecialchars($p_id_raw);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($qty === false || $qty <= 0) {
        $response['m'] = "無效的數量: " . htmlspecialchars($qty_raw);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    if ($qty >= 50) { // 後端也做一次上限檢查
        $qty = 49;
    }

    // --- 開始資料庫操作 ---
    try {
        // 查詢是否有相同的產品編號 (使用預備語句)
        $query_select = "SELECT cartid, qty FROM cart WHERE p_id = :p_id AND ip = :ip AND orderid IS NULL";
        $stmt_select = $link->prepare($query_select);
        $stmt_select->bindParam(':p_id', $p_id, PDO::PARAM_INT);
        $stmt_select->bindParam(':ip', $u_ip, PDO::PARAM_STR);
        $stmt_select->execute();

        if ($stmt_select->rowCount() == 0) {
            // 商品不在購物車中，執行 INSERT (使用預備語句)
            // 假設 cart 表有 add_date 欄位且希望自動填入 (如果資料庫沒有預設值 CURRENT_TIMESTAMP)
            // $query_action = "INSERT INTO cart (p_id, qty, ip, add_date) VALUES (:p_id, :qty, :ip, NOW())";
            // 如果沒有 add_date 或資料庫會自動處理：
            $query_action = "INSERT INTO cart (p_id, qty, ip) VALUES (:p_id, :qty, :ip)";
            $stmt_action = $link->prepare($query_action);
            $stmt_action->bindParam(':p_id', $p_id, PDO::PARAM_INT);
            $stmt_action->bindParam(':qty', $qty, PDO::PARAM_INT); // $qty 是本次要加入的
            $stmt_action->bindParam(':ip', $u_ip, PDO::PARAM_STR);
            $action_type = "INSERT";
        } else {
            // 商品已在購物車中，執行 UPDATE (使用預備語句)
            $cart_data = $stmt_select->fetch(PDO::FETCH_ASSOC);
            $new_total_qty = $cart_data['qty'] + $qty; // $qty 是本次要加入的
            if ($new_total_qty > 49) {
                $new_total_qty = 49;
            }
            $query_action = "UPDATE cart SET qty = :qty WHERE cartid = :cartid";
            $stmt_action = $link->prepare($query_action);
            $stmt_action->bindParam(':qty', $new_total_qty, PDO::PARAM_INT);
            $stmt_action->bindParam(':cartid', $cart_data['cartid'], PDO::PARAM_INT);
            $action_type = "UPDATE";
        }

        if ($stmt_action->execute()) {
            if ($stmt_action->rowCount() > 0) {
                $response = ['c' => '1', 'm' => '謝謝您!產品已成功 ' . ($action_type == "INSERT" ? '加入' : '更新至') . ' 購物車中。'];
            } else {
                // 可能是 UPDATE 時數量未變，或者 INSERT 失敗但未拋異常 (罕見)
                $response = ['c' => '1', 'm' => '購物車狀態已是最新或操作已執行但未更改資料。IP: ' . $u_ip];
            }
        } else {
            $errorInfo = $stmt_action->errorInfo();
            $response['m'] = '抱歉!資料庫操作 (' . $action_type . ') 失敗，請聯絡管理人員。Error: ' . ($errorInfo[2] ?? 'Unknown DB error');
        }
    } catch (PDOException $e) {
        // error_log("Database error in addcart.php: " . $e->getMessage()); // 寫入伺服器日誌
        $response['m'] = '資料庫處理錯誤，請聯絡管理人員。(Exception: ' . $e->getMessage() . ')';
    }
} else {
    $response['m'] = '必要參數 (p_id 或 qty) 缺失。';
}
echo json_encode($response, JSON_UNESCAPED_UNICODE);
// return; // 在 PHP 檔案末尾的 return 通常是多餘的
?>