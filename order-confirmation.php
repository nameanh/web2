<?php
$page_title = 'Xác nhận đơn hàng';
$body_class = 'order-confirmation-page';
require_once 'includes/header.php';

if (!isset($_SESSION['khach_hang'])) {
    header('Location: login.php');
    exit;
}

$ma_dh = (int)($_GET['ma_dh'] ?? 0);
if (!$ma_dh) {
    header('Location: index.php');
    exit;
}

// Lấy đơn hàng (chỉ của khách hàng đang đăng nhập)
$stmt = $db->prepare("SELECT * FROM don_hang WHERE ma_dh = ? AND ma_kh = ?");
$stmt->bind_param('ii', $ma_dh, $_SESSION['khach_hang']['ma_kh']);
$stmt->execute();
$dh = $stmt->get_result()->fetch_assoc();

if (!$dh) {
    header('Location: index.php');
    exit;
}

// Lấy chi tiết sản phẩm trong đơn
$stmt2 = $db->prepare("SELECT ctdh.*, sp.ten_sp, sp.hinh_anh
                        FROM chi_tiet_don_hang ctdh
                        JOIN san_pham sp ON ctdh.ma_sp = sp.ma_sp
                        WHERE ctdh.ma_dh = ?");
$stmt2->bind_param('i', $ma_dh);
$stmt2->execute();
$items = $stmt2->get_result();
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Xác nhận đơn hàng</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Xác nhận đơn hàng</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="order-confirmation" class="order-confirmation section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="order-confirmation-3">
        <div class="row g-0">
          <!-- Sidebar tóm tắt -->
          <div class="col-lg-4 sidebar" data-aos="fade-right">
            <div class="sidebar-content">
              <div class="success-animation">
                <i class="bi bi-check-lg"></i>
              </div>
              <div class="order-id">
                <h4>#DH-<?= str_pad($ma_dh, 6, '0', STR_PAD_LEFT) ?></h4>
                <div class="order-date"><?= date('d/m/Y H:i', strtotime($dh['ngay_dat'])) ?></div>
              </div>
              <div class="price-summary">
                <h5>Tóm tắt đơn hàng</h5>
                <ul class="summary-list">
                  <li>
                    <span>Tổng sản phẩm</span>
                    <span><?= number_format($dh['tong_tien'], 0, ',', '.') ?> VNĐ</span>
                  </li>
                  <li>
                    <span>Phí vận chuyển</span>
                    <span>Miễn phí</span>
                  </li>
                  <li class="total">
                    <span>Tổng cộng</span>
                    <span><?= number_format($dh['tong_tien'], 0, ',', '.') ?> VNĐ</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Nội dung chính -->
          <div class="col-lg-8 main-content" data-aos="fade-in">
            <div class="thank-you-message">
              <h1>Cảm ơn <?= htmlspecialchars($_SESSION['khach_hang']['ho_ten']) ?>!</h1>
              <p>Chúng tôi đã nhận được đơn hàng và sẽ xử lý ngay.</p>
            </div>

            <div class="details-card" data-aos="fade-up">
              <div class="card-header">
                <h3><i class="bi bi-geo-alt"></i> Chi tiết giao hàng</h3>
              </div>
              <div class="card-body">
                <address>
                  <strong><?= htmlspecialchars($dh['ten_nguoi_nhan']) ?></strong><br>
                  <?= htmlspecialchars($dh['dia_chi_giao']) ?>
                  <?= $dh['phuong_xa_giao'] ? ', ' . htmlspecialchars($dh['phuong_xa_giao']) : '' ?>
                  <?= $dh['quan_huyen_giao'] ? ', ' . htmlspecialchars($dh['quan_huyen_giao']) : '' ?>
                  <?= $dh['tinh_tp_giao'] ? ', ' . htmlspecialchars($dh['tinh_tp_giao']) : '' ?><br>
                  SĐT: <?= htmlspecialchars($dh['dien_thoai_nhan']) ?><br>
                  Thanh toán: <?= htmlspecialchars($dh['phuong_thuc_tt']) ?>
                </address>
              </div>
            </div>

            <div class="details-card" data-aos="fade-up">
              <div class="card-header">
                <h3><i class="bi bi-bag-check"></i> Sản phẩm đã đặt</h3>
              </div>
              <div class="card-body">
                <?php while ($item = $items->fetch_assoc()): ?>
                <div class="confirmation-order-item" style="display:flex;align-items:center;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid #eee;">
                  <?php if ($item['hinh_anh']): ?>
                  <img src="<?= htmlspecialchars($item['hinh_anh']) ?>"
                       alt="<?= htmlspecialchars($item['ten_sp']) ?>"
                       style="width:80px;height:80px;object-fit:cover;border-radius:8px;margin-right:20px;border:1px solid #eee;">
                  <?php else: ?>
                  <div style="width:80px;height:80px;background:#f0f0f0;border-radius:8px;margin-right:20px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-phone" style="color:#ccc;font-size:2rem;"></i>
                  </div>
                  <?php endif; ?>
                  <div>
                    <h6 style="font-size:16px;font-weight:600;margin-bottom:5px;"><?= htmlspecialchars($item['ten_sp']) ?></h6>
                    <div style="font-size:14px;color:#777;">
                      <?= $item['so_luong'] ?> × <?= number_format($item['gia_ban'], 0, ',', '.') ?> VNĐ
                    </div>
                  </div>
                </div>
                <?php endwhile; ?>
              </div>
            </div>

            <div class="action-area" data-aos="fade-up">
              <div class="row g-3">
                <div class="col-md-6">
                  <a href="all.php" class="btn btn-back">
                    <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                  </a>
                </div>
                <div class="col-md-6">
                  <a href="account.php#orders" class="btn btn-account">
                    <span>Xem trong Tài khoản</span>
                    <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>