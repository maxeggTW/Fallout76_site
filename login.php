<!-- 將資料庫連線程式載入 -->
<?php require_once("Connections/conn_db.php"); ?>
<!-- 如果SESSION沒有啟動，則啟動SESSION功能，這是跨網頁變數存取 -->
<?php (!isset($_SESSION)) ? session_start() : ""; ?>
<!-- 載入共用PHP函數庫 -->
<?php require_once("php_lib.php"); ?>
<?php
//取得要返回的php頁面
if (isset($_GET['sPath'])) {
  $sPath = $_GET['sPath'] . ".php";
} else {
  //登入完成預設要進入首頁
  $sPath = "index_P01.php";
}
//檢查是否完成登入驗證
if (isset($_SESSION['login'])) {
  header(sprintf("location: %s", $sPath));
  //php 5.2.6舊版採用以下方式
  //echo "<script>window.location.href = '" . $sPath . "';</script>";
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
  <meta charset="UTF-8">
  <title>齒輪切換登入註冊</title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.2.1/css/all.css">
  <style>
    :root {
      --panel-width: 600px;
      /* 您可以調整這個值 */
      --panel-height: 480px;
      /* 面板的高度 */
      --panel-horizontal-offset: 100px;
      /* 面板距離中心線的水平偏移 */
      --gear-size: 500px;
      --overlap-amount: 500px;
      /* 齒輪覆蓋在面板上的量 */
    }

    * {
      box-sizing: border-box;
    }

    body,
    html {
      margin: 0;
      padding: 0;
      height: 100%;
      background: #ccc;
      font-family: 'Segoe UI', sans-serif;
    }

    .container {
      position: relative;
      width: 100%;
      height: 100vh;
      z-index: 0;
      display: flex;
      /* 使用 Flexbox */
      justify-content: center;
      /* 水平居中所有內容 */
      align-items: center;
      /* 垂直居中所有內容 */
      overflow: hidden;
      /* 防止內容溢出 */
    }

    .container::before {
      content: "";
      position: absolute;
      inset: 0;
      background: url('./IMAGES/Loginbackground.webp') center center no-repeat;
      background-size: cover;
      opacity: 0.3;
      z-index: -1;
    }

    .panel {
      background: rgba(255, 255, 255, 0.9);
      width: var(--panel-width);
      height: var(--panel-height);
      border-radius: 20px;
      /* 外部容器的圓角，齒輪會蓋住部分 */
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
      z-index: 1;
      /* 面板在背景和齒輪之下 */
      position: absolute;
      /* 保持絕對定位，因為 Flexbox 難以實現這種複雜的 Z 軸疊加和個別元素動畫 */
      top: 50%;
      transform: translateY(-50%);
      /* 垂直居中 */
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.5s ease, transform 0.5s ease;
      padding: 0;
      /* 確保 panel 自身沒有 padding 推動內容 */
      display: flex;
      /* 新增 Flexbox 布局 */
    }

    /* 新增的內容容器，負責白色背景和內邊距 */
    .panel-content {
      /* 實際的白色背景和透明度加在這裡 */
      border-radius: 20px;
      /* 內容容器的圓角應該和外部面板一致 */
      padding: 30px;
      /* 原本 panel 的 padding 移到這裡 */
      display: flex;
      flex-direction: column;
      gap: 15px;
      /* 原本 panel 的 gap 移到這裡 */
      width: 60%;
      /* 佔滿 panel 容器 */
      height: 100%;
      /* 佔滿 panel 容器 */
      box-sizing: border-box;
      /* 確保 padding 不增加寬高 */
      z-index: 3;
      /* 確保內容在齒輪之上 */
      position: relative;
      /* 確保 z-index 生效 */
    }

    /* 登入面板內容位於左側 */
    #loginPanel .panel-content {
      margin-left: 0;
      /* 登入面板內容靠左 */
    }

    /* 註冊面板內容位於右側 */
    #registerPanel .panel-content {
      margin-left: auto;
      /* 註冊面板內容靠右 */
    }

    /* 登入面板活躍時的位置 */
    .panel.is-login-active {
      opacity: 1;
      pointer-events: auto;
      /* 登入面板左移：中心點向左偏移量 */
      transform: translate(calc(-50% - var(--panel-horizontal-offset)), -50%);
    }

    /* 註冊面板活躍時的位置 */
    .panel.is-register-active {
      opacity: 1;
      pointer-events: auto;
      /* 註冊面板右移：中心點向右偏移量 */
      transform: translate(calc(50% + var(--panel-horizontal-offset)), -50%);
    }

    /* 面板隱藏時的位置 (移出螢幕) */
    .panel.is-hidden-left {
      opacity: 0;
      pointer-events: none;
      transform: translate(calc(-100% - var(--panel-width) - 100px), -50%);
      /* 確保完全移出視窗 */
    }

    .panel.is-hidden-right {
      opacity: 0;
      pointer-events: none;
      transform: translate(calc(100% + var(--panel-width) + 100px), -50%);
      /* 確保完全移出視窗 */
    }


    /* ------------------------------------------------------------- */
    .gear-wrapper {
      width: var(--gear-size);
      height: var(--gear-size);
      position: absolute;
      /* 保持絕對定位 */
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      /* 將齒輪本身中心對齊容器中心 */
      cursor: pointer;
      z-index: 2;
      /* 齒輪在 panel (z-index:1) 之上，但在 panel-content (z-index:3) 之下 */
      transition: transform 0.5s ease;
      /* 讓齒輪有滑動效果 */
    }

    /* 齒輪在登入面板右側時 (登入面板活躍時) */
    .gear-wrapper.on-login-side {
      /* 齒輪向右移動，與登入面板重疊 */
      /* 計算：登入面板中心點的 X 座標 + 面板寬度一半 - 重疊量 + 齒輪自身一半寬度 */
      transform: translate(calc(-50% + var(--panel-width) / 2 - var(--overlap-amount) + var(--panel-horizontal-offset)), -50%);
    }

    /* 齒輪在註冊面板左側時 (註冊面板活躍時) */
    .gear-wrapper.on-register-side {
      /* 齒輪向左移動，與註冊面板重疊 */
      transform: translate(calc(-50% - var(--panel-width) / 2 + var(--overlap-amount) - var(--panel-horizontal-offset)), -50%);
    }


    .gear-wrapper.rotate .gear-image {
      animation: spin 1s forwards;
    }



    @keyframes spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(180deg);
      }
    }

    @keyframes counter-spin {
      from {
        transform: rotate(0deg);
      }

      to {
        transform: rotate(0deg);
      }
    }

    .gear-image {
      background: url('./IMAGES/gear.svg') center/contain no-repeat;
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 1;
      /* 齒輪圖片在齒輪文字下方 */
    }

    .gear_76 {
      position: absolute;
      top: 5%;
      left: 5%;
      z-index: 2;
    }

    /* ------------------------------------------------------------- */
    input {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #aaa;
      border-radius: 5px;
    }

    button {
      padding: 10px;
      font-size: 18px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }

    .login-btn {
      background-color: #2563eb;
      color: white;
    }

    .register-btn {
      background-color: #dc2626;
      color: white;
    }

    .switch-link {
      font-size: 14px;
      text-align: right;
      color: #dc2626;
      cursor: pointer;
    }

    .input-hint {
      font-size: 12px;
      color: #888;
      margin-top: -10px;
      margin-bottom: 5px;
      text-align: left;
    }

    .forgot-password-link {
      font-size: 14px;
      color: #2563eb;
      text-align: right;
      cursor: pointer;
      margin-top: -10px;
      margin-bottom: 5px;
    }

    .checkbox-and-link-group {
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 14px;
      color: #333;
    }

    .checkbox-and-link-group input[type="checkbox"] {
      margin-right: 5px;
    }

    .checkbox-and-link-group .switch-link.right-aligned {
      color: #dc2626;
    }

    form {
      margin: 0;
      padding: 0;
      display: contents;
    }


    .input-group>.form-control {
      width: 100%;
    }

    /* 錯誤提示文字 */
    span.error-tips {
      color: red;
      font-family: 'Arial', '微軟正黑體', sans-serif;
      /* 設定您希望的文字字型 */
      font-size: 12px;
      /* 請調整此數值來改變文字大小，例如 12px, 0.9em 等 */
      font-weight: normal;
      /* 或其他標準字重，例如 400 */
      display: block;
      /* 確保換行與 margin 生效 */
      margin-top: -15px;
      /* 調整此處以設定與輸入框的距離 */
    }

    /* 正確提示圖示 */
    span.valid-tips::before {
      font-family: "Font Awesome 5 Free";
      font-weight: 900;
      content: "\f00c";
      /* 您選擇的正確圖示 */
      margin-right: 0.4em;
    }

    /* 正確提示文字 */
    span.valid-tips {
      color: #28a745;
      /* 建議使用深一點的綠色，greenyellow 在白色背景上不易閱讀 */
      font-family: 'Arial', '微軟正黑體', sans-serif;
      font-size: 14px;
      /* 與錯誤提示大小一致或按需調整 */
      font-weight: normal;
    }
  </style>
  </style>
</head>

<body>
  <?php
  if (isset($_POST['formctl']) && $_POST['formctl'] == 'reg') {
    $email = $_POST['email'];
    $pw1 = md5($_POST['pw1']);
    $insertsql = "INSERT INTO member (email, pw1) VALUES('" . $email . "','" . $pw1 . "')";
    $Result = $link->query($insertsql);
    $emailid = $link->lastInsertId();  //讀刷新增會員編號
    if ($Result) {
      // 將會員的姓名,電話,地址寫數addbook
      $insertsql = "INSERT INTO addbook (emailid,setdefault) VALUES ('" . $emailid . "','1',)";
      $Result = $link->query($insertsql);
      $_SESSION['login'] = false;    //設定會員註冊完直接登入
      $_SESSION['emailid'] = $emailid;
      $_SESSION['email'] = $email;
      echo "<script language='javascript'>alert('謝謝您，會員資料已完成註冊');</script>";
    }
  }
  ?>
  <div class="container">
    <!-- 登入 -->
    <form action="" method="POST" class="form-signin" id="form1">
      <div class="panel left" id="loginPanel">
        <div class="panel-content">
          <h2>使用者登入</h2>
          <input type="email" id="inputAccount" name="inputAccount" class="form-control" placeholder="帳號" required autofocus />
          <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="密碼" required />
          <label><input type="checkbox"> 記住我</label>
          <button class="login-btn">登入</button>
          <div class="switch-link">還沒帳號嗎？</div>
          <a href="index_P01.php">回首頁</a>
        </div>
      </div>
    </form>


    <!-- 齒輪按鈕 -->
    <div class="gear-wrapper" id="gear">
      <img src="./IMAGES/76.svg" class="gear_76" alt="76">
      <div class="gear-image"></div>
    </div>


    <!-- 註冊 -->
    <form id="reg" name="reg" action="" method="POST">
      <div class="panel right hidden" id="registerPanel">
        <div class="panel-content">
          <h2>使用者註冊</h2>
          <input type="email" name="email" id="email" class="form-control" placeholder="*請輸入email帳號" autocomplete="off">
          <input type="password" name="pw1" id="pw1" class="form-control" placeholder="*請輸入密碼">
          <input type="password" name="pw2" id="pw2" class="form-control" placeholder="*請再次確認密碼">
          <input type="hidden" name="formctl" id="formctl" value="reg">
          <button type="submit" class="register-btn">註冊</button>
          <div class="switch-link">已經有帳號？</div>
        </div>
      </div>
    </form>
  </div>
  <?php require_once("JSfile.php"); ?>
  <script type="text/javascript" src="commlib.js"></script>
  <script type="text/javascript" src="jquery.validate.js"></script>
  <script>
    const gear = document.getElementById('gear');
    const loginPanel = document.getElementById('loginPanel');
    const registerPanel = document.getElementById('registerPanel');
    const switchLinks = document.querySelectorAll('.switch-link');

    let isLoginPanelActive = true; // 追蹤目前哪個面板是活躍的

    // 初始化狀態
    function initializePanels() {
      loginPanel.classList.add('is-login-active');
      registerPanel.classList.add('is-hidden-right'); // 初始狀態：註冊面板隱藏在右側
      gear.classList.add('on-login-side'); // 初始狀態：齒輪定位在登入面板一側
    }

    // 首次載入時調用
    initializePanels();

    function switchPanels() {
      // 齒輪旋轉動畫
      gear.classList.add('rotate');
      setTimeout(() => {
        gear.classList.remove('rotate');
      }, 1000); // 確保動畫時間與 CSS 中設定的 'spin' 和 'counter-spin' 時間一致

      if (isLoginPanelActive) {
        // 從登入切換到註冊
        loginPanel.classList.remove('is-login-active');
        loginPanel.classList.add('is-hidden-left');

        registerPanel.classList.remove('is-hidden-right');
        registerPanel.classList.add('is-register-active');

        gear.classList.remove('on-login-side');
        gear.classList.add('on-register-side');

      } else {
        // 從註冊切換到登入
        registerPanel.classList.remove('is-register-active');
        registerPanel.classList.add('is-hidden-right');

        loginPanel.classList.remove('is-hidden-left');
        loginPanel.classList.add('is-login-active');

        gear.classList.remove('on-register-side');
        gear.classList.add('on-login-side');
      }

      isLoginPanelActive = !isLoginPanelActive; // 切換狀態
    }

    // 點擊任一切換連結時也觸發切換
    switchLinks.forEach(link => {
      link.addEventListener('click', switchPanels);
    });
  </script>

  <script type="text/javascript">
    $(function() {
      $("#form1").submit(function() {
        const inputAccount = $("#inputAccount").val();
        const inputPassword = MD5($("#inputPassword").val());
        $("#loading").show();
        // 利用$ajax函數呼叫後台的auth_user.php驗證帳號密碼
        $.ajax({
          url: 'auth_user.php',
          type: 'post',
          dataType: 'json',
          data: {
            inputAccount: inputAccount,
            inputPassword: inputPassword,
          },
          success: function(data) {
            if (data.c == true) {
              alert(data.m);
              // window.location.reload();
              window.location.href = "<?php echo $sPath; ?>";
            } else {
              alert(data.m);
            }
          },
          error: function(date) {
            alert("系統目前無法連接到後台資料庫");
          }
        });
      });
    });
  </script>
  <script type="text/javascript">
    $('#reg').validate({
      rules: {
        email: {
          required: true,
          email: true,
          remote: 'checkemail.php'
        },
        pw1: {
          required: true,
          maxlength: 20,
          minlength: 4,
        },
        pw2: {
          required: true,
          equalTo: '#pw1'
        },
      },
      messages: {
        email: {
          required: 'email信箱不得為空白!!',
          email: 'email信箱格式有誤',
          remote: 'email信箱已經註冊'
        },
        pw1: {
          required: '密碼不得為空白!!',
          maxlength: '密碼最大長度為20位(4-20位英文字母與數字的組合)',
          minlength: '密碼最小長度為4位(4-20位英文字母與數字的組合)'
        },
        pw2: {
          required: '密碼不得為空白!!',
          equalTo: '兩次輸入的密碼必須一致！'
        },
      },
    });
  </script>

</body>

</html>