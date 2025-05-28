<?php
//  載入設定和連線
require_once("Connections/conn_db.php");
require_once("php_lib.php");
if (!isset($_SESSION)) {
  session_start();
}


?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fallout 76 網站</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="./Website_03.css" />
</head>

<body>
  <section class="header">
    <?php require_once("item_header.php") ?>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php require_once("cart_content.php"); ?>
    </div>
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

  <?php require_once("JSfile.php"); ?>

  <script type="text/javascript">
    $(document).ready(function() { // 使用 DOM ready 確保 HTML 載入完成
      // 將變更的數量寫入後台資料庫
      // 使用更精確的選擇器，針對購物車頁面中的數量輸入框
      $("input[name='qty[]'][cartid]").change(function() {
        var qty_str = $(this).val();
        const cartid = $(this).attr("cartid");

        // 轉換數量為整數
        var qty = parseInt(qty_str);

        if (isNaN(qty) || qty <= 0 || qty >= 50) { // 檢查是否為有效數字
          alert("更改數量需為 1 至 49 之間的數字。");
          // 可以考慮將數量重置為之前的值，或者重新載入頁面
          // 例如： $(this).val( original_value ); // 需要儲存原始值
          window.location.reload(); // 或者直接重載
          return false;
        }

        $.ajax({
          url: 'change_qty.php',
          type: 'post',
          dataType: 'json',
          data: {
            cartid: cartid,
            qty: qty,
          },
          success: function(data) {
            if (data.c == true || data.c == "1") { // 後端回傳的是字串 "1"
              // alert(data.m); // 成功的訊息可以不用 alert，直接重載
              window.location.reload();
            } else {
              alert(data.m);
            }
          },
          error: function(jqXHR, textStatus, errorThrown) { // 更詳細的錯誤處理
            alert("系統目前無法連接到後台資料庫或發生錯誤。");
            console.error("AJAX Error: ", textStatus, errorThrown);
            console.error("Response Text: ", jqXHR.responseText);
          }
        });
      });
    });
  </script>
</body>

</html>