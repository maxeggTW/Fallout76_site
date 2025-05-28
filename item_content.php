 <?php
    // 5. 根據查詢結果顯示內容或錯誤訊息
    if ($product) {
      // 資料成功載入，嘗試載入模板
      try {
        include 'product.php'; // 模板使用 $product 變數來顯示商品細節
      } catch (Throwable $t) { // 捕捉模板內的潛在錯誤 (PHP 7+)
        error_log("Template Error (product.php): " . $t->getMessage());
        echo '<div class="container alert alert-danger text-center"><p>顯示產品主要資訊時發生錯誤，請回報管理員。</p></div>';
      }

      echo '<div class="detail-section" >'; // 給這個區塊一個 class，方便 CSS 定位，並設定文字靠左
      echo '<h1 >商品詳細介紹</h1>'; // 調整標題樣式

      if (isset($product['p_detail']) && !empty(trim($product['p_detail']))) {
        $details_array = explode("\n", trim($product['p_detail']));

        echo '<ul class="product-detail-list">'; 

        foreach ($details_array as $detail_line) {
          $trimmed_line = trim($detail_line);
          if (!empty($trimmed_line)) { // 避免空的 li
            // 檢查是否為注意事項行
            if (strpos($trimmed_line, '**') === 0) { // 如果該行以 ** 開頭
              // 移除開頭的 ** 並加上特殊樣式或處理
              $note_text = htmlspecialchars(substr($trimmed_line, 2)); // 移除前兩個 ** 字元
              echo '<p class="detail-note" style="list-style-type: none; margin-top: 15px; font-size: 0.9em; color: #555;"><strong>**</strong>' . $note_text . '</p>';
            } else {
              echo '<p style="margin-bottom: 8px;">' . htmlspecialchars($trimmed_line) . '</p>';
            }
          }
        }
        echo '</ul>';
      } else {
        echo '<p class="text-center" style="font-size: 1.1em;">尚無詳細規格介紹。</p>'; // 調整提示文字樣式
      }
      echo '</div>';
      echo '</div>';
    } else {
      // 資料載入失敗，顯示錯誤訊息
      echo '<div class="container alert alert-warning text-center" style="padding: 20px;"><h2>' . htmlspecialchars($error_message ?? '無法載入產品資料') . '</h2></div>';
    }
    ?>