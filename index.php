<?php
$page_title = 'Trang chủ';
$body_class = 'index-page';
require_once 'includes/header.php';

$db = get_db();

// Lấy số lượng giỏ hàng
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) $cart_count += $item['so_luong'];
}

// Lấy sản phẩm đang hot
$sql_hot = "SELECT sp.ma_sp, sp.ten_sp, sp.hinh_anh, sp.so_luong_ton,
                   l.ten_loai,
                   ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) AS gia_ban
            FROM san_pham sp
            JOIN loai_sp l ON sp.ma_loai = l.ma_loai
            WHERE sp.an_hien = 1 AND sp.so_luong_ton > 0
            ORDER BY sp.ma_sp DESC LIMIT 4";
$hot_products = $db->query($sql_hot);

// Lấy 1 sản phẩm nổi bật cho hero
$hero = $db->query("SELECT sp.ma_sp, sp.ten_sp, sp.hinh_anh,
                           ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) AS gia_ban
                    FROM san_pham sp WHERE sp.an_hien = 1 AND sp.so_luong_ton > 0
                    ORDER BY sp.ma_sp DESC LIMIT 1")->fetch_assoc();
?>

<main class="main">
  <!-- Hero Section -->
  <section id="hero" class="hero section">
    <div class="hero-container">
      <div class="hero-content">
        <div class="content-wrapper" data-aos="fade-up" data-aos-delay="100">
          <h1 class="hero-title">Siêu Phẩm Mới Ra Mắt</h1>
          <p class="hero-description">
            Khám phá bộ sưu tập điện thoại thông minh chính hãng, hiệu năng vượt trội,
            thiết kế dẫn đầu. Tất cả đều sẵn sàng với ưu đãi độc quyền và giao hàng siêu tốc.
          </p>
          <div class="hero-actions" data-aos="fade-up" data-aos-delay="200">
            <a href="all.php" class="btn-secondary">Xem toàn bộ điện thoại</a>
          </div>
          <div class="features-list" data-aos="fade-up" data-aos-delay="300">
            <div class="feature-item"><i class="bi bi-truck"></i><span>Giao hàng siêu tốc</span></div>
            <div class="feature-item"><i class="bi bi-award"></i><span>Bảo hành chính hãng</span></div>
            <div class="feature-item"><i class="bi bi-headset"></i><span>Hỗ trợ 24/7</span></div>
          </div>
        </div>
      </div>

      <div class="hero-visuals">
        <div class="product-showcase" data-aos="fade-left" data-aos-delay="200">
          <?php if ($hero): ?>
          <div class="product-card featured">
            <a href="product-details.php?ma_sp=<?= $hero['ma_sp'] ?>">
              <?php if ($hero['hinh_anh']): ?>
                <img src="<?= htmlspecialchars($hero['hinh_anh']) ?>"
                     alt="<?= htmlspecialchars($hero['ten_sp']) ?>"
                     style="width:100%;border-radius:12px;">
              <?php endif; ?>
            </a>
            <div class="product-badge">Bán chạy nhất</div>
            <div class="product-info">
              <a href="product-details.php?ma_sp=<?= $hero['ma_sp'] ?>" class="product-title" style="font-size:20px">
                <?= htmlspecialchars($hero['ten_sp']) ?>
              </a>
              <div class="price">
                <span class="sale-price"><?= number_format($hero['gia_ban'], 0, ',', '.') ?> VND</span>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <div class="floating-elements">
          <a href="cart.php" class="floating-icon cart" data-aos="fade-up" data-aos-delay="600">
            <i class="bi bi-cart3"></i>
            <span class="notification-dot cart-item-count-badge"><?= $cart_count ?></span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Best Sellers -->
  <section id="best-sellers" class="best-sellers section">
    <div class="container section-title" data-aos="fade-up">
      <h2>Điện thoại đang hot</h2>
      <p>Top những sản phẩm được săn đón nhiều nhất</p>
    </div>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row g-5">
        <?php if ($hot_products && $hot_products->num_rows > 0):
          while ($sp = $hot_products->fetch_assoc()): ?>
        <div class="col-lg-3 col-md-6">
          <div class="product-item">
            <div class="product-image">
              <a href="product-details.php?ma_sp=<?= $sp['ma_sp'] ?>">
                <?php if ($sp['hinh_anh']): ?>
                  <img src="<?= htmlspecialchars($sp['hinh_anh']) ?>"
                       alt="<?= htmlspecialchars($sp['ten_sp']) ?>"
                       style="width:100%;height:200px;object-fit:contain;">
                <?php else: ?>
                  <div style="width:100%;height:200px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-image" style="font-size:3rem;color:#ccc;"></i>
                  </div>
                <?php endif; ?>
              </a>
            </div>
            <div class="product-info">
              <div class="product-category"><?= htmlspecialchars($sp['ten_loai']) ?></div>
              <h4 class="product-title">
                <a href="product-details.php?ma_sp=<?= $sp['ma_sp'] ?>">
                  <?= htmlspecialchars($sp['ten_sp']) ?>
                </a>
              </h4>
              <div class="product-price">
                <span class="current-price"><?= number_format($sp['gia_ban'], 0, ',', '.') ?> VND</span>
              </div>
            </div>
          </div>
        </div>
        <?php endwhile;
        else: ?>
        <div class="col-12 text-center py-5">
          <p class="text-muted">Chưa có sản phẩm. Vui lòng thêm sản phẩm qua trang admin.</p>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Call to Action -->
  <section id="call-to-action" class="call-to-action section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row">
        <div class="col-lg-8 mx-auto">
          <div class="main-content text-center" data-aos="zoom-in" data-aos-delay="200">
            <div class="offer-badge" data-aos="fade-down" data-aos-delay="250">
              <span class="limited-time">Ưu đãi độc quyền</span>
              <span class="offer-text">Giảm giá lên đến 40%</span>
            </div>
            <h2 data-aos="fade-up" data-aos-delay="300">Săn Sale Bùng Cháy - Giá Sập Sàn</h2>
            <p class="subtitle" data-aos="fade-up" data-aos-delay="350">
              Đừng bỏ lỡ cơ hội sở hữu chiếc điện thoại mơ ước với mức giá không thể tốt hơn.
              Đây là thời điểm vàng để nâng cấp dế yêu của bạn.
            </p>
            <div class="countdown-wrapper" data-aos="fade-up" data-aos-delay="400">
              <div class="countdown d-flex justify-content-center" data-count="2025/11/20">
                <div><h3 class="count-days"></h3><h4>Ngày</h4></div>
                <div><h3 class="count-hours"></h3><h4>Giờ</h4></div>
                <div><h3 class="count-minutes"></h3><h4>Phút</h4></div>
                <div><h3 class="count-seconds"></h3><h4>Giây</h4></div>
              </div>
            </div>
            <div class="action-buttons" data-aos="fade-up" data-aos-delay="450">
              <a href="all.php" class="btn-shop-now">Mua sắm ngay</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>
