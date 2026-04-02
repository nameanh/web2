<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';

$page_title = 'Tài khoản';
$body_class = 'account-page';

// FIX: Kiểm tra đăng nhập TRƯỚC khi include header
// (header.php có thể dùng $db nên phải require db.php trước)
if (!isset($_SESSION['khach_hang'])) {
    header('Location: login.php?redirect=account.php');
    exit;
}

$kh = $_SESSION['khach_hang'];
$db = get_db(); // FIX: lấy kết nối DB (file gốc thiếu dòng này nếu header.php không khởi tạo $db)

// FIX: Lấy lịch sử đơn hàng – sắp xếp ngay_dat DESC để đơn gần nhất lên trước
$stmt = $db->prepare(
    "SELECT dh.*, COUNT(ctdh.ma_ctdh) AS so_sp
     FROM don_hang dh
     LEFT JOIN chi_tiet_don_hang ctdh ON dh.ma_dh = ctdh.ma_dh
     WHERE dh.ma_kh = ?
     GROUP BY dh.ma_dh
     ORDER BY dh.ngay_dat DESC"
);
$stmt->bind_param('i', $kh['ma_kh']);
$stmt->execute();
$don_hangs = $stmt->get_result();

$trang_thai_label = ['Chờ xử lý', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'];
$trang_thai_class = ['processing', 'confirmed', 'shipping', 'delivered', 'cancelled'];

require_once 'includes/header.php';
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Tài khoản</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Tài khoản</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="account" class="account section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row g-4">

        <!-- ===== Menu trái ===== -->
        <div class="col-lg-3">
          <div class="profile-menu d-lg-block" id="profileMenu">
            <div class="user-info" data-aos="fade-right">
              <div class="user-avatar">
                <span class="status-badge"><i class="bi bi-shield-check"></i></span>
              </div>
              <h4><?= htmlspecialchars($kh['ho_ten']) ?></h4>
              <div class="user-status">
                <i class="bi bi-award"></i>
                <span>Thành viên</span>
              </div>
            </div>
            <nav class="menu-nav">
              <ul class="nav flex-column" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-bs-toggle="tab" href="#orders">
                    <i class="bi bi-box-seam"></i>
                    <span>Đơn hàng của tôi</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="tab" href="#settings">
                    <i class="bi bi-gear"></i>
                    <span>Cài đặt</span>
                  </a>
                </li>
              </ul>
              <div class="menu-footer">
                <a href="logout.php" class="logout-link">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Đăng xuất</span>
                </a>
              </div>
            </nav>
          </div>
        </div>

        <!-- ===== Nội dung ===== -->
        <div class="col-lg-9">
          <div class="content-area">
            <div class="tab-content">

              <!-- ===== Tab: Đơn hàng ===== -->
              <div class="tab-pane fade show active" id="orders">
                <div class="section-header" data-aos="fade-up">
                  <h2>Đơn hàng của tôi</h2>
                  <?php if ($don_hangs->num_rows > 0): ?>
                  <small class="text-muted">(<?= $don_hangs->num_rows ?> đơn hàng)</small>
                  <?php endif; ?>
                </div>

                <div class="orders-grid">
                  <?php if ($don_hangs->num_rows === 0): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bag-x" style="font-size:3rem;color:#ccc;"></i>
                        <p class="mt-3 text-muted">Bạn chưa có đơn hàng nào trong lịch sử.</p>
                        <a href="all.php" class="btn btn-primary mt-2">Khám phá sản phẩm ngay</a>
                    </div>

                  <?php else:
                    // FIX: reset con trỏ kết quả để đảm bảo đọc từ đầu
                    $don_hangs->data_seek(0);
                    while ($dh = $don_hangs->fetch_assoc()):
                      $tt = (int)$dh['trang_thai'];
                      // Bảo vệ tránh index out of range
                      $tt_label = $trang_thai_label[$tt] ?? 'Không xác định';
                      $tt_class = $trang_thai_class[$tt] ?? 'processing';
                  ?>
                  <div class="order-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="order-header">
                      <div class="order-id">
                        <span class="label">Mã đơn hàng:</span>
                        <span class="value">#DH-<?= str_pad($dh['ma_dh'], 6, '0', STR_PAD_LEFT) ?></span>
                      </div>
                      <div class="order-date"><?= date('d/m/Y H:i', strtotime($dh['ngay_dat'])) ?></div>
                    </div>

                    <div class="order-content">
                      <div class="order-info">
                        <div class="info-row">
                          <span>Tình trạng:</span>
                          <span class="status <?= $tt_class ?>">
                            <?= $tt_label ?>
                          </span>
                        </div>
                        <div class="info-row">
                          <span>Số sản phẩm:</span>
                          <span><?= (int)$dh['so_sp'] ?></span>
                        </div>
                        <div class="info-row">
                          <span>Tổng tiền:</span>
                          <span class="price"><?= number_format($dh['tong_tien'], 0, ',', '.') ?> VNĐ</span>
                        </div>
                        <div class="info-row">
                          <span>Thanh toán:</span>
                          <span><?= htmlspecialchars($dh['phuong_thuc_tt']) ?></span>
                        </div>
                      </div>
                    </div>

                    <div class="order-footer">
                      <button type="button" class="btn-details"
                              data-bs-toggle="collapse"
                              data-bs-target="#details_<?= $dh['ma_dh'] ?>"
                              aria-expanded="false">
                        Xem chi tiết <i class="bi bi-chevron-down ms-1"></i>
                      </button>
                    </div>

                    <!-- Chi tiết đơn hàng (collapse) -->
                    <div class="collapse order-details" id="details_<?= $dh['ma_dh'] ?>">
                      <?php
                      // FIX: dùng cùng biến $db đã kết nối ở đầu file
                      $stmt3 = $db->prepare(
                          "SELECT ctdh.*, sp.ten_sp, sp.hinh_anh
                           FROM chi_tiet_don_hang ctdh
                           JOIN san_pham sp ON ctdh.ma_sp = sp.ma_sp
                           WHERE ctdh.ma_dh = ?"
                      );
                      $stmt3->bind_param('i', $dh['ma_dh']);
                      $stmt3->execute();
                      $items3 = $stmt3->get_result();
                      ?>
                      <div class="details-content">
                        <!-- Địa chỉ giao hàng -->
                        <div class="detail-section">
                          <h5>Địa chỉ giao hàng</h5>
                          <div class="address-info">
                            <p>
                              <strong><?= htmlspecialchars($dh['ten_nguoi_nhan']) ?></strong><br>
                              <?= htmlspecialchars($dh['dia_chi_giao']) ?>
                              <?= !empty($dh['phuong_xa_giao'])  ? ', ' . htmlspecialchars($dh['phuong_xa_giao'])  : '' ?>
                              <?= !empty($dh['quan_huyen_giao']) ? ', ' . htmlspecialchars($dh['quan_huyen_giao']) : '' ?>
                              <?= !empty($dh['tinh_tp_giao'])    ? ', ' . htmlspecialchars($dh['tinh_tp_giao'])    : '' ?>
                            </p>
                            <p class="contact">
                              <i class="bi bi-telephone me-1"></i>
                              <?= htmlspecialchars($dh['dien_thoai_nhan']) ?>
                            </p>
                            <?php if (!empty($dh['ghi_chu'])): ?>
                            <p><em>Ghi chú: <?= htmlspecialchars($dh['ghi_chu']) ?></em></p>
                            <?php endif; ?>
                          </div>
                        </div>

                        <!-- Danh sách sản phẩm -->
                        <div class="detail-section">
                          <h5>Sản phẩm</h5>
                          <div class="order-items">
                            <?php while ($it = $items3->fetch_assoc()): ?>
                            <div class="item"
                                 style="display:flex;align-items:center;margin-bottom:10px;">
                              <?php if (!empty($it['hinh_anh'])): ?>
                              <img src="<?= htmlspecialchars($it['hinh_anh']) ?>"
                                   style="width:50px;height:50px;object-fit:cover;border-radius:6px;
                                          margin-right:12px;border:1px solid #eee;">
                              <?php endif; ?>
                              <div class="item-info" style="flex:1;">
                                <h6 style="margin-bottom:2px;"><?= htmlspecialchars($it['ten_sp']) ?></h6>
                                <div class="item-meta" style="font-size:13px;color:#777;">
                                  SL: <?= $it['so_luong'] ?> ×
                                  <?= number_format($it['gia_ban'], 0, ',', '.') ?> VNĐ
                                </div>
                              </div>
                              <div class="item-price" style="font-weight:600;white-space:nowrap;">
                                <?= number_format($it['gia_ban'] * $it['so_luong'], 0, ',', '.') ?> VNĐ
                              </div>
                            </div>
                            <?php endwhile; ?>
                          </div>
                        </div>

                        <!-- Tổng tiền -->
                        <div class="detail-section">
                          <div class="price-breakdown">
                            <div class="price-row total d-flex justify-content-between fw-bold"
                                 style="border-top:1px solid #eee;padding-top:8px;margin-top:4px;">
                              <span>Tổng cộng</span>
                              <span><?= number_format($dh['tong_tien'], 0, ',', '.') ?> VNĐ</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div><!-- /collapse -->
                  </div><!-- /order-card -->
                  <?php endwhile; endif; ?>
                </div>
              </div>

              <!-- ===== Tab: Cài đặt ===== -->
              <div class="tab-pane fade" id="settings">
                <div class="section-header" data-aos="fade-up">
                  <h2>Cài đặt tài khoản</h2>
                </div>
                <div class="settings-content">
                  <div class="settings-section" data-aos="fade-up">
                    <h3>Thông tin cá nhân</h3>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" class="form-control"
                               value="<?= htmlspecialchars($kh['ho_ten']) ?>" readonly>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control"
                               value="<?= htmlspecialchars($kh['email'] ?? '') ?>" readonly>
                      </div>
                      <?php if (!empty($kh['dien_thoai'])): ?>
                      <div class="col-md-6">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control"
                               value="<?= htmlspecialchars($kh['dien_thoai']) ?>" readonly>
                      </div>
                      <?php endif; ?>
                    </div>
                    <p class="text-muted mt-3 small">
                      <i class="bi bi-info-circle me-1"></i>
                      Để thay đổi thông tin, vui lòng liên hệ bộ phận hỗ trợ.
                    </p>
                  </div>
                </div>
              </div>

            </div><!-- /tab-content -->
          </div>
        </div>

      </div><!-- /row -->
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>