<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';

$ma_sp = intval($_GET['ma_sp'] ?? 0);
if (!$ma_sp) { header('Location: all.php'); exit; }

$db = get_db();
$stmt = $db->prepare("SELECT sp.*, l.ten_loai,
                             ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) AS gia_ban
                      FROM san_pham sp
                      JOIN loai_sp l ON sp.ma_loai = l.ma_loai
                      WHERE sp.ma_sp = ? AND sp.an_hien = 1 LIMIT 1");
$stmt->bind_param("i", $ma_sp);
$stmt->execute();
$sp = $stmt->get_result()->fetch_assoc();
if (!$sp) { header('Location: all.php'); exit; }

// Xử lý thêm vào giỏ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_gio'])) {
    if (!isset($_SESSION['khach_hang'])) {
        header('Location: login.php?redirect=' . urlencode("product-details.php?ma_sp=$ma_sp"));
        exit;
    }
    $so_luong = max(1, intval($_POST['so_luong'] ?? 1));
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$ma_sp])) {
        $_SESSION['cart'][$ma_sp]['so_luong'] += $so_luong;
    } else {
        $_SESSION['cart'][$ma_sp] = [
            'so_luong' => $so_luong,
            'sp'       => [
                'ma_sp'       => $ma_sp,
                'ten_sp'      => $sp['ten_sp'],
                'gia_ban'     => $sp['gia_ban'],
                'hinh_anh'    => $sp['hinh_anh'],
                'so_luong_ton'=> $sp['so_luong_ton'],
            ],
        ];
    }
    header('Location: product-details.php?ma_sp=' . $ma_sp . '&added=1');
    exit;
}

$page_title = $sp['ten_sp'];
$body_class = 'product-details-page';
require_once 'includes/header.php';
?>
<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Chi tiết sản phẩm</h1>
      <nav class="breadcrumbs"><ol>
        <li><a href="index.php">Trang chủ</a></li>
        <li><a href="all.php">Sản phẩm</a></li>
        <li class="current"><?= htmlspecialchars($sp['ten_sp']) ?></li>
      </ol></nav>
    </div>
  </div>

  <?php if (isset($_GET['added'])): ?>
  <div class="container mt-3">
    <div class="alert alert-success alert-dismissible fade show">
      Đã thêm vào giỏ hàng! <a href="cart.php">Xem giỏ hàng</a>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  </div>
  <?php endif; ?>

  <section id="product-details" class="product-details section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row g-4">

        <!-- Ảnh sản phẩm -->
        <div class="col-lg-7" data-aos="zoom-in" data-aos-delay="150">
          <div class="product-gallery">
            <div class="main-showcase">
              <div class="image-zoom-container">
                <?php if ($sp['hinh_anh']): ?>
                  <img id="main-img" src="<?= htmlspecialchars($sp['hinh_anh']) ?>"
                       alt="<?= htmlspecialchars($sp['ten_sp']) ?>"
                       style="width:100%;max-height:450px;object-fit:contain;border-radius:12px;border:1px solid #eee;">
                <?php else: ?>
                  <div style="width:100%;height:400px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;border-radius:12px;">
                    <i class="bi bi-image" style="font-size:5rem;color:#ccc;"></i>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="col-lg-5" data-aos="fade-left" data-aos-delay="200">
          <div class="product-details">
            <div class="product-badge-container">
              <span class="badge-category"><?= htmlspecialchars($sp['ten_loai']) ?></span>
            </div>
            <h1 class="product-name"><?= htmlspecialchars($sp['ten_sp']) ?></h1>

            <div class="pricing-section">
              <div class="price-display">
                <span class="sale-price"><?= number_format($sp['gia_ban'], 0, ',', '.') ?> VND</span>
              </div>
            </div>

            <?php if ($sp['mo_ta']): ?>
            <div class="product-description">
              <p><?= nl2br(htmlspecialchars($sp['mo_ta'])) ?></p>
            </div>
            <?php endif; ?>

            <div class="availability-status">
              <?php if ($sp['so_luong_ton'] > 0): ?>
                <div class="stock-indicator">
                  <i class="bi bi-check-circle-fill"></i>
                  <span class="stock-text">Còn hàng</span>
                </div>
                <div class="quantity-left">Còn <?= $sp['so_luong_ton'] ?> sản phẩm</div>
              <?php else: ?>
                <div class="stock-indicator" style="color:#999">
                  <i class="bi bi-x-circle-fill"></i>
                  <span class="stock-text">Hết hàng</span>
                </div>
              <?php endif; ?>
            </div>

            <?php if ($sp['so_luong_ton'] > 0): ?>
            <form method="POST" action="product-details.php?ma_sp=<?= $ma_sp ?>">
              <div class="purchase-section">
                <div class="quantity-control">
                  <label class="control-label">Số lượng:</label>
                  <div class="quantity-input-group">
                    <div class="qty-selector-custom">
                      <!-- Dùng class "qty-btn-minus" / "qty-btn-plus" thay vì "decrease"/"increase"
                           để tránh main.js của template gắn thêm event listener vào -->
                      <button type="button" class="quantity-btn qty-btn-minus"
                        onclick="var i=document.getElementById('qty-input'); if(+i.value>1) i.value=+i.value-1;">
                        <i class="bi bi-dash"></i>
                      </button>
                      <input type="number" id="qty-input" class="quantity-input" name="so_luong"
                             value="1" min="1" max="<?= $sp['so_luong_ton'] ?>" />
                      <button type="button" class="quantity-btn qty-btn-plus"
                        onclick="var i=document.getElementById('qty-input'); if(+i.value<+i.max) i.value=+i.value+1;">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="action-buttons mt-3">
                  <button type="submit" name="them_gio" class="btn primary-action">
                    <i class="bi bi-bag-plus"></i> Thêm vào giỏ hàng
                  </button>
                  <button type="button" class="btn secondary-action"
                    onclick="document.querySelector('[name=them_gio]').click(); setTimeout(()=>window.location='cart.php',300)">
                    <i class="bi bi-lightning"></i> Mua ngay
                  </button>
                </div>
              </div>
            </form>
            <?php endif; ?>

            <div class="benefits-list mt-3">
              <div class="benefit-item"><i class="bi bi-truck"></i><span>Free ship cho đơn từ 5 triệu đồng</span></div>
              <div class="benefit-item"><i class="bi bi-arrow-clockwise"></i><span>30 ngày 1 đổi 1 (lỗi NSX)</span></div>
              <div class="benefit-item"><i class="bi bi-shield-check"></i><span>Bảo hành 12 tháng</span></div>
              <div class="benefit-item"><i class="bi bi-headset"></i><span>Hỗ trợ kỹ thuật 24/7</span></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>