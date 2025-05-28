<div class="hot-sale-title">
          <h3>強力熱銷中</h3>
          <img src="./IMAGES/caps.png" alt="caps" class="caps" />
        </div>
        <div class="preorder-merch"> <?php if (!empty($hot_selling_products)): ?>
            <?php foreach ($hot_selling_products as $product): ?>
              <a href="main_product.php?id=<?php echo htmlspecialchars($product['p_id']); ?>" style="text-decoration: none; color: inherit; display: contents;">
                <div class="preorder-item"> <img src="<?php echo htmlspecialchars($image_directory . $product['p_image_path']); ?>" alt="<?php echo htmlspecialchars($product['p_name']); ?>" class="product-image" />
                  <div class="preorder-text">
                    <h2><?php echo htmlspecialchars($product['p_name']); ?></h2>
                    <h3>NT$<?php echo htmlspecialchars(number_format($product['p_price'])); ?></h3>
                  </div>
                </div>
              </a>
            <?php endforeach; ?>
          <?php elseif (!isset($db_error_message)): ?>
            <p style="text-align: center; width: 100%;">目前沒有熱銷商品。</p>
          <?php endif; ?>
        </div>