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
      <?php require_once("checkout_content.php"); ?>
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
  <script type="text/javascript" src="commlib.js"></script>
  <script type="text/javascript" src="jquery.validate.js"></script>
  <script type="text/javascript" src="additional-methods.js"></script>
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
    // 在此處加入驗證碼
      // 如果沒有使用 additional-methods.js，請為 pattern 驗證添加自訂方法
      if ($.validator && !$.validator.methods.pattern) {
        $.validator.addMethod("pattern", function(value, element, param) {
          if (this.optional(element)) {
            return true;
          }
          if (typeof param === "string") {
            param = new RegExp("^(?:" + param + ")$");
          }
          return param.test(value);
        }, "格式無效。");
      }

      $("#checkoutForm").validate({
        rules: { // 定義驗證規則
          checkout_name: { // 姓名欄位
            required: true, // 必填
            minlength: 2    // 最小長度為 2
          },
          checkout_email: { // E-mail 欄位
            required: true, // 必填
            email: true     // 必須是有效的 E-mail 格式
          },
          checkout_phone: { // 電話欄位
            required: true, // 必填
            pattern: /^(0\d{1,2})-?\d{6,8}(#\d{1,5})?$/ // 例如：02-12345678 或 04-12345678#分機
          },
          checkout_mobile: { // 手機欄位
            // 此欄位在 HTML 中非必填，因此只有在填寫時才進行驗證
            pattern: /^09\d{2}-?\d{3}-?\d{3}$/ // 例如：0912-345-678 或 0912345678
          },
           checkout_address: {
                // 'required' 屬性由您現有的 JavaScript 動態處理
            },
            cc_name: {
                // 'required' 屬性由您現有的 JavaScript 動態處理
                // (當選擇信用卡付款時，您的 checkout_content.php 中的 JS 會將其設為 required)
            },
            cc_number: {
                // 'required' 屬性由您現有的 JavaScript 動態處理
                // 您也可以為信用卡號碼加上 pattern 或使用 additional-methods.js 中的 'creditcard' 規則
                creditcard:true
            },
            cc_expiry: {
                // 'required' 屬性由您現有的 JavaScript 動態處理
                pattern: /^(0[1-9]|1[0-2])\/\d{2}$/ // MM/YY 格式 (例如：03/28)
            },
            cc_cvv: {
                // 'required' 屬性由您現有的 JavaScript 動態處理
                pattern: /^\d{3,4}$/ // 3 或 4 位數字
            }
        },
        messages: { // 定義驗證失敗時的提示訊息
          checkout_name: {
            required: "請輸入姓名",
            minlength: "姓名至少需要2個字元"
          },
          checkout_email: {
            required: "請輸入E-mail",
            email: "請輸入有效的E-mail格式 (例如：user@example.com)"
          },
          checkout_phone: {
            required: "請輸入電話號碼",
            pattern: "請輸入有效的電話號碼格式 (例如：02-12345678 或 04-12345678#分機)"
          },
          checkout_mobile: {
            pattern: "請輸入有效的手機號碼格式 (例如：0912-345-678 或 0912345678)"
          },
          checkout_address: {
            required: "請輸入收件地址"
          },
          cc_name: {
            required: "請輸入持卡人姓名"
          },
          cc_number: {
            required: "請輸入信用卡號碼",
             creditcard: "請輸入有效的信用卡號碼"
          },
          cc_expiry: {
            required: "請輸入有效期限 (MM/YY)"
          },
          cc_cvv: {
            required: "請輸入安全碼 (CVV)"
          }
        },
        errorElement: "div", // 設定錯誤訊息使用 <div> 標籤包裹
        errorPlacement: function(error, element) { // 設定錯誤訊息的顯示位置
          error.addClass("invalid-feedback"); // 加入 Bootstrap 5 的錯誤訊息樣式
          if (element.prop("type") === "checkbox" || element.prop("type") === "radio") {
            error.insertAfter(element.closest(".form-check"));
          } else if (element.closest(".input-group").length) { // 處理 input-group 的情況
            error.insertAfter(element.closest(".input-group"));
          }
           else {
            error.insertAfter(element);
          }
        },
        highlight: function(element, errorClass, validClass) { // 設定驗證失敗時的樣式
          $(element).addClass("is-invalid").removeClass("is-valid"); // 加入 Bootstrap 5 無效欄位樣式
        },
        unhighlight: function(element, errorClass, validClass) { // 設定驗證成功時的樣式
          $(element).removeClass("is-invalid").addClass("is-valid"); // 加入 Bootstrap 5 有效欄位樣式
        },
        submitHandler: function(form) { // 表單通過驗證後執行的函數
          // 您可以在此處加入 AJAX 提交的邏輯，
          // 或者直接讓表單正常提交。
          form.submit();
        }
      });
    });
  </script>
</body>

</html>