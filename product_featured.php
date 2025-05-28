  
        <div class="featured-title">
          <img src="./IMAGES/caps.png" alt="caps" class="caps" />
          <h3>主打商品，強力販售中</h3>
          <img src="./IMAGES/caps.png" alt="caps" class="caps" />
        </div>
        <div class="featured-merch">
          <?php if (!empty($featured_products)): ?>
            <?php foreach ($featured_products as $product): ?>
              <div class="merch-item">
                <img src="<?php echo htmlspecialchars($image_directory . $product['p_image_path']); ?>"
                  alt="" />
                <div class="hotmerch-text">
                  <h2><?php echo htmlspecialchars($product['p_ename']); // 英文商品名 
                      ?></h2>
                  <h3><?php echo htmlspecialchars($product['p_name']); // 中文商品名 
                      ?></h3>
                </div>
                <a href="main_product.php?id=<?php echo htmlspecialchars($product['p_id']); ?>" class="merch-btn">查看商品</a>
              </div>
            <?php endforeach; ?>
          <?php elseif (!isset($db_error_message)): // 只有在沒有資料庫錯誤時才顯示“沒有商品” 
          ?>
            <p style="text-align: center; width: 100%;">目前沒有主打商品。</p>
          <?php endif; ?>
        </div>