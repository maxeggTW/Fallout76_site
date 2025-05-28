<!-- 將資料庫連線程式載入 -->
<?php require_once("Connections/conn_db.php"); ?>
<!-- 如果SESSION沒有啟動，則啟動SESSION功能，這是跨網頁變數存取 -->
<?php (!isset($_SESSION)) ? session_start() : ""; ?>
<!-- 載入共用PHP函數庫 -->
<?php require_once("php_lib.php"); ?>
<?php

?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fallout 76 網站</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
    crossorigin="anonymous" />
  <link
    rel="stylesheet"
    href="https://use.fontawesome.com/releases/v6.2.1/css/
  all.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="./Website_01.css" />
</head>

<body>
  <section class="carousel-container">
    <?php require_once("carousel.php"); ?>
  </section>
  <div class="scroll-down-container">
    <div class="pipboy-arrow-down" title="向下捲動"></div>
  </div>
  <section class="content">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="content-text fade-in">
            <h2>《Fallout 76》周邊必備商品</h2>
            <p>
              踏上廢土之旅，成為專業廢土人前，總覺得少了點甚麼嗎?
            </p>
            <p>這裡有你需要的所有周邊商品，讓你在廢土之旅中無往不利!</p>
            <a href="index_purchase.php" class="custom-button">
              點此前往瀏覽
            </a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="side-image">
            <img
              src="./IMAGES/FAllout-best-Merch-Hero-Images.avif"
              class="img-fluid"
              alt="賽季屍鬼圖" />
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="contentR">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 image-column">
          <img src="./IMAGES/FO76_Season20_CommunityCalendar-TCN-04.jpg" alt="活動行事曆" class="content-image">
        </div>
        <div class="col-md-6 text-column">
          <div class="contentR-text fade-in">
            <h2>《Fallout 76》資訊攻略區</h2>
            <div class="yellow-arrowR"><img src="./IMAGES/arrow.png" alt=""></div>
            <p>想了解更多有關《Fallout 76》的遊戲資訊以及攻略嗎?
              來這裡就對了!</p>
            <a href="#" class="custom-button">敬請期待</a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="contentL">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 text-column">
          <div class="contentL-text fade-in">
            <h2>原子商店每週更新</h2>
            <div class="yellow-arrowL"><img src="./IMAGES/arrow.png" alt=""></div>
            <p>請繼續閱讀，預覽目前原子商店提供最新最棒的物品和優惠，包括本週的免費。</p>
            <a href="#" class="custom-button">敬請期待</a>
          </div>
        </div>
        <div class="col-md-6 image-column">
          <img src="./IMAGES/LCard_RadiantShelterBundle.webp" alt="AtomicShop" class="content-image">
        </div>
      </div>
    </div>
  </section>

  <section class="contentR">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 image-column">
          <img src="./IMAGES/LargeCard_lv50-boost.webp" alt="lv50" class="content-image">
        </div>
        <div class="col-md-6 text-column">
          <div class="contentR-text fade-in">
            <h2>廢土居民大躍進</h2>
            <div class="yellow-arrowR"><img src="./IMAGES/arrow.png" alt=""></div>
            <p>用《Fallout 76》的等級50角色跳級，讓你的廢土居民贏在起跑點。</p>
            <a href="#" class="custom-button">敬請期待</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="outsidelink">
    <div class="outsidelink-title">
      <h2>本站玩家專屬連結</h2><img src="./IMAGES/Vault-Tec.svg.png" alt="vaultLogo" class="vaultLogo">
    </div>
    <img src="./IMAGES/gR.png" alt="齒輪左" class="gear-left">
    <img src="./IMAGES/gL.png" alt="齒輪右" class="gear-right">
    <img src="./IMAGES/link1.png" alt="連結1" class="link1">
    <img src="./IMAGES/link2.png" alt="連結2" class="link2">
    <img src="./IMAGES/link3.png" alt="連結3" class="link3">
  </section>


  <section class="footer">
    <div class="footer-content">
      <div class="footer-section">
        <h2>Fallout76中文資訊網</h2>

      </div>
    </div>
    <div class="social-icons">
      <div class="social-icon">
        <i class="fa-brands fa-instagram"></i>
      </div>
      <div class="social-icon">
        <i class="fa-brands fa-facebook-f"></i>
      </div>
      <div class="social-icon">
        <i class="fa-brands fa-youtube"></i>
      </div>
      <div class="social-icon">
        <i class="fa-brands fa-discord"></i>
      </div>
      <div class="social-icon">
        <i class="fa-brands fa-x-twitter"></i>
      </div>
      <div class="social-icon">
        <i class="fa-brands fa-twitch"></i>
      </div>
    </div>
    <div class="footer-policy">
      <p>© 2025 - 2025 Fallout76中文資訊網Contact. Privacy Policy.</p>
    </div>
  </section>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // --- 第一個監聽器的內容 (輪播圖) ---
      const slides = document.querySelectorAll(".carousel-slide");
      const dots = document.querySelectorAll(".dot");
      const prevBtn = document.getElementById("prevBtn");
      const nextBtn = document.getElementById("nextBtn");
      let currentSlide = 0;
      let slideInterval;

      function showSlide(index) {
        if (index >= slides.length) {
          currentSlide = 0;
        } else if (index < 0) {
          currentSlide = slides.length - 1;
        } else {
          currentSlide = index;
        }
        slides.forEach((slide) => slide.classList.remove("active"));
        dots.forEach((dot) => dot.classList.remove("active"));
        slides[currentSlide].classList.add("active");
        dots[currentSlide].classList.add("active");
      }

      function startAutoSlide() {
        stopAutoSlide();
        slideInterval = setInterval(() => {
          showSlide(currentSlide + 1);
        }, 5000);
      }

      function stopAutoSlide() {
        if (slideInterval) {
          clearInterval(slideInterval);
        }
      }

      if (nextBtn && prevBtn && slides.length > 0 && dots.length > 0) { // 添加檢查以防元素不存在
        nextBtn.addEventListener("click", () => {
          showSlide(currentSlide + 1);
          startAutoSlide();
        });

        prevBtn.addEventListener("click", () => {
          showSlide(currentSlide - 1);
          startAutoSlide();
        });

        dots.forEach((dot, index) => {
          dot.addEventListener("click", () => {
            showSlide(index);
            startAutoSlide();
          });
        });

        const carouselContainer = document.querySelector(".carousel-container");
        if (carouselContainer) { // 添加檢查
          carouselContainer.addEventListener("mouseenter", stopAutoSlide);
          carouselContainer.addEventListener("mouseleave", startAutoSlide);
        }


        // 初始化輪播
        showSlide(0);
        startAutoSlide();
      }


      // --- 原本第二個監聽器的內容 (淡入效果) ---
      const fadeElements = document.querySelectorAll('.content-text, .side-image, .contentR-text, .contentL-text');

      // 添加 fade-in 類 (如果元素存在) - 最好在 CSS 中預設，而不是用 JS 添加
      // fadeElements.forEach(element => {
      //  element.classList.add('fade-in');
      // });

      // 創建 Intersection Observer
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            //  observer.unobserve(entry.target); // 如果只想觸發一次，取消註解
          } else {
            entry.target.classList.remove('visible'); // 離開視窗時移除，可重複觸發
          }
        });
      }, {
        threshold: 0.15, // 元素15%進入視窗時觸發
        rootMargin: '0px'
      });

      // 開始觀察所有元素
      fadeElements.forEach(element => {
        if (element) { // 添加檢查
          observer.observe(element);
        }
      });


      // 獲取箭頭元素
      const scrollDownContainer = document.querySelector('.scroll-down-container');
      const footer = document.querySelector('.footer');

      // 監聽滾動事件
      window.addEventListener('scroll', function() {
        const footerRect = footer.getBoundingClientRect();

        // 當滾動到 footer 時
        if (footerRect.top <= window.innerHeight) {
          // 改變箭頭方向（向上）
          scrollDownContainer.style.transform = 'translateX(-50%) rotate(180deg)';
        } else {
          // 恢復箭頭方向（向下）
          scrollDownContainer.style.transform = 'translateX(-50%)';
        }
      });

      // 箭頭點擊事件
      scrollDownContainer.addEventListener('click', function() {
        const footerRect = footer.getBoundingClientRect();

        if (footerRect.top <= window.innerHeight) {
          // 如果在 footer 附近，點擊回到頂部
          window.scrollTo({
            top: 0,
            behavior: 'smooth'
          });
        } else {
          // 否則向下滾動一個視窗高度
          window.scrollBy({
            top: window.innerHeight,
            behavior: 'smooth'
          });
        }
      });

    }); // <-- DOMContentLoaded 監聽器結束
  </script>
  <!-- 引入javascript -->
  <?php require_once("JSfile.php"); ?>
</body>

</html>