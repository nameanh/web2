<?php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';

$page_title = 'Thanh toán';
$body_class = 'checkout-page';

// Phải đăng nhập
if (!isset($_SESSION['khach_hang'])) {
    header('Location: login.php?redirect=checkout.php');
    exit;
}

// Giỏ hàng phải có hàng
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$db  = get_db();
$kh  = $_SESSION['khach_hang'];

// FIX: dùng null-coalescing để tránh Undefined array key với mọi trường của $kh
$kh_ho_ten     = $kh['ho_ten']     ?? '';
$kh_dien_thoai = $kh['dien_thoai'] ?? '';   // <-- đây là dòng gây Warning trước đây
$kh_dia_chi    = $kh['dia_chi']    ?? '';
$kh_phuong_xa  = $kh['phuong_xa']  ?? '';
$kh_quan_huyen = $kh['quan_huyen'] ?? '';
$kh_tinh_tp    = $kh['tinh_tp']    ?? '';

$tong_tien       = 0;
foreach ($cart as $item) {
    $tong_tien += $item['sp']['gia_ban'] * $item['so_luong'];
}
$phi_ship        = $tong_tien >= 5000000 ? 0 : 20000;
$tong_thanh_toan = $tong_tien + $phi_ship;

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_nhan        = trim($_POST['ten_nhan']        ?? '');
    $dien_thoai_nhan = trim($_POST['dien_thoai_nhan'] ?? '');
    $dia_chi         = trim($_POST['dia_chi']         ?? '');
    $phuong_xa       = trim($_POST['phuong_xa']       ?? '');
    $quan_huyen      = trim($_POST['quan_huyen']      ?? '');
    $tinh_tp         = trim($_POST['tinh_tp']         ?? '');
    $phuong_thuc_tt  = $_POST['phuong_thuc_tt']       ?? 'COD';
    $ghi_chu         = trim($_POST['ghi_chu']         ?? '');
    $chon_dia_chi    = $_POST['chon_dia_chi']          ?? 'moi';

    // Nếu chọn dùng địa chỉ tài khoản, lấy từ hidden fields
    if ($chon_dia_chi === 'tk') {
        $dia_chi    = trim($_POST['dia_chi_hidden']    ?? $kh_dia_chi);
        $phuong_xa  = trim($_POST['phuong_xa_hidden']  ?? $kh_phuong_xa);
        $quan_huyen = trim($_POST['quan_huyen_hidden'] ?? $kh_quan_huyen);
        $tinh_tp    = trim($_POST['tinh_tp_hidden']    ?? $kh_tinh_tp);
    }

    // --- Validation ---
    if (empty($ten_nhan)) {
        $errors[] = 'Vui lòng nhập họ tên người nhận.';
    }
    if (empty($dien_thoai_nhan)) {
        $errors[] = 'Vui lòng nhập số điện thoại.';
    } elseif (!preg_match('/^[0-9]{9,11}$/', $dien_thoai_nhan)) {
        $errors[] = 'Số điện thoại không hợp lệ (9–11 chữ số).';
    }
    if (empty($dia_chi)) {
        $errors[] = 'Vui lòng nhập địa chỉ chi tiết.';
    }
    // FIX: Phường/Xã giờ bắt buộc
    if (empty($phuong_xa)) {
        $errors[] = 'Vui lòng nhập Phường/Xã.';
    }
    if (empty($quan_huyen)) {
        $errors[] = 'Vui lòng nhập Quận/Huyện.';
    }
    if (empty($tinh_tp)) {
        $errors[] = 'Vui lòng nhập Tỉnh/Thành phố.';
    }
    if (!in_array($phuong_thuc_tt, ['COD', 'Chuyển khoản', 'Trực tuyến'])) {
        $errors[] = 'Phương thức thanh toán không hợp lệ.';
    }

    if (empty($errors)) {
// --- Tạo đơn hàng ---
$stmt = $db->prepare("INSERT INTO don_hang 
    (ma_kh, ngay_dat, ten_nguoi_nhan, dien_thoai_nhan, dia_chi_giao, 
     phuong_xa_giao, quan_huyen_giao, tinh_tp_giao, phuong_thuc_tt, 
     tong_tien, ghi_chu, trang_thai) 
    VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");

// Giải thích chuỗi 'isssssssds':
// 1 'i' (ma_kh), 7 's' (chuỗi), 1 'd' (tong_tien), 1 's' (ghi_chu) = Tổng 10 biến.
$stmt->bind_param('isssssssds', 
    $kh['ma_kh'], 
    $ten_nhan, 
    $dien_thoai_nhan, 
    $dia_chi, 
    $phuong_xa, 
    $quan_huyen, 
    $tinh_tp, 
    $phuong_thuc_tt, 
    $tong_thanh_toan, 
    $ghi_chu
);
      if ($stmt->execute()) {
    $ma_dh = $db->insert_id;

    // Lấy lại giỏ hàng một lần nữa cho chắc chắn
    $cart_to_save = $_SESSION['cart'] ?? [];

    if (!empty($cart_to_save)) {
        // CHUẨN BỊ LỆNH (Prepare ngoài vòng lặp để nhanh hơn)
        $stmt2 = $db->prepare("INSERT INTO chi_tiet_don_hang (ma_dh, ma_sp, so_luong, gia_ban) VALUES (?, ?, ?, ?)");
        
        foreach ($cart_to_save as $id_san_pham => $item) {
            // Ép kiểu dữ liệu để khớp hoàn toàn với Database
            $ma_sp_int    = (int)$id_san_pham; 
            $so_luong_int = (int)$item['so_luong'];
            $gia_ban_float = (float)$item['sp']['gia_ban'];

            // Gán tham số và thực thi
            $stmt2->bind_param('iiid', $ma_dh, $ma_sp_int, $so_luong_int, $gia_ban_float);
            
            if (!$stmt2->execute()) {
                // Nếu có lỗi, ghi lại để kiểm tra (vào file error_log của server)
                error_log("Lỗi INSERT chi tiết: " . $stmt2->error);
            }
        }
        $stmt2->close();
    }

    // --- XÓA GIỎ HÀNG (Chỉ xóa sau khi đã lưu xong chi tiết) ---
    unset($_SESSION['cart']);
    
    // Chuyển hướng
    header("Location: order-confirmation.php?ma_dh=$ma_dh&phuong_thuc_tt=" . urlencode($phuong_thuc_tt));
    exit;

} else {
    $errors[] = 'Lỗi hệ thống: Không thể tạo đơn hàng chính. ' . $db->error;
}
    }
}

require_once 'includes/header.php';
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Thanh toán</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li><a href="cart.php">Giỏ hàng</a></li>
          <li class="current">Thanh toán</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="checkout" class="checkout section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">

      <?php if (!empty($errors)): ?>
      <div class="alert alert-danger mb-4">
        <ul class="mb-0">
          <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <div class="row">
        <!-- ============ FORM THANH TOÁN ============ -->
        <div class="col-lg-7">
          <div class="checkout-container" data-aos="fade-up">
            <form class="checkout-form" method="POST" action="checkout.php" id="checkoutForm"
                  novalidate>

              <!-- ---- 1. Thông tin người nhận ---- -->
              <div class="checkout-section">
                <div class="section-header">
                  <div class="section-number">1</div>
                  <h3>Thông tin người nhận</h3>
                </div>
                <div class="section-content">
                  <div class="form-group">
                    <label>Họ và tên người nhận <span class="text-danger">*</span></label>
                    <input type="text" name="ten_nhan" class="form-control"
                           value="<?= htmlspecialchars($_POST['ten_nhan'] ?? $kh_ho_ten) ?>"
                           required>
                  </div>
                  <div class="form-group">
                    <label>Số điện thoại <span class="text-danger">*</span></label>
                    <!-- FIX: dùng $kh_dien_thoai (đã được xử lý null-coalescing ở trên) -->
                    <input type="text" name="dien_thoai_nhan" class="form-control"
                           value="<?= htmlspecialchars($_POST['dien_thoai_nhan'] ?? $kh_dien_thoai) ?>"
                           placeholder="VD: 0901234567"
                           required>
                  </div>
                  <div class="form-group">
                    <label>Ghi chú</label>
                    <input type="text" name="ghi_chu" class="form-control"
                           value="<?= htmlspecialchars($_POST['ghi_chu'] ?? '') ?>"
                           placeholder="Ghi chú cho người giao hàng (không bắt buộc)">
                  </div>
                </div>
              </div>

              <!-- ---- 2. Địa chỉ giao hàng ---- -->
              <div class="checkout-section">
                <div class="section-header">
                  <div class="section-number">2</div>
                  <h3>Địa chỉ giao hàng</h3>
                </div>
                <div class="section-content">
                  <?php
                  $co_dia_chi_tk = !empty($kh_dia_chi);
                  // Giữ lại lựa chọn sau khi POST lỗi
                  $chon_hien_tai = $_POST['chon_dia_chi'] ?? ($co_dia_chi_tk ? 'tk' : 'moi');
                  ?>

                  <?php if ($co_dia_chi_tk): ?>
                  <!-- Radio chọn nguồn địa chỉ -->
                  <div class="form-group mb-3">
                    <div class="d-flex gap-4 mb-3">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="chon_dia_chi"
                               id="dung_tk" value="tk"
                               <?= $chon_hien_tai === 'tk' ? 'checked' : '' ?>
                               onchange="toggleDiaChi(this.value)">
                        <label class="form-check-label" for="dung_tk">Dùng địa chỉ tài khoản</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="chon_dia_chi"
                               id="nhap_moi" value="moi"
                               <?= $chon_hien_tai === 'moi' ? 'checked' : '' ?>
                               onchange="toggleDiaChi(this.value)">
                        <label class="form-check-label" for="nhap_moi">Nhập địa chỉ mới</label>
                      </div>
                    </div>

                    <!-- Hiển thị địa chỉ tài khoản -->
                    <div id="dia_chi_tk_box"
                         <?= $chon_hien_tai === 'moi' ? 'style="display:none"' : '' ?>>
                      <div class="alert alert-light border py-2 px-3" style="font-size:14px;">
                        <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                        <?= htmlspecialchars($kh_dia_chi) ?>
                        <?= $kh_phuong_xa  ? ', ' . htmlspecialchars($kh_phuong_xa)  : '' ?>
                        <?= $kh_quan_huyen ? ', ' . htmlspecialchars($kh_quan_huyen) : '' ?>
                        <?= $kh_tinh_tp    ? ', ' . htmlspecialchars($kh_tinh_tp)    : '' ?>
                      </div>
                      <!-- Hidden fields – server sẽ đọc khi chon_dia_chi = tk -->
                      <input type="hidden" name="dia_chi_hidden"    value="<?= htmlspecialchars($kh_dia_chi)    ?>">
                      <input type="hidden" name="phuong_xa_hidden"  value="<?= htmlspecialchars($kh_phuong_xa)  ?>">
                      <input type="hidden" name="quan_huyen_hidden" value="<?= htmlspecialchars($kh_quan_huyen) ?>">
                      <input type="hidden" name="tinh_tp_hidden"    value="<?= htmlspecialchars($kh_tinh_tp)    ?>">
                    </div>
                  </div>
                  <?php else: ?>
                  <!-- Không có địa chỉ tài khoản – ẩn radio, hiện luôn form mới -->
                  <input type="hidden" name="chon_dia_chi" value="moi">
                  <?php endif; ?>

                  <!-- Form nhập địa chỉ mới -->
                  <div id="dia_chi_moi_box"
                       <?= ($co_dia_chi_tk && $chon_hien_tai !== 'moi') ? 'style="display:none"' : '' ?>>
                    <div class="form-group">
                      <label>Địa chỉ chi tiết (Số nhà, tên đường) <span class="text-danger">*</span></label>
                      <input type="text" name="dia_chi" id="dia_chi_input" class="form-control"
                             value="<?= htmlspecialchars($_POST['dia_chi'] ?? '') ?>"
                             placeholder="VD: 123 Nguyễn Trãi">
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <!-- FIX: Phường/Xã giờ bắt buộc (required + dấu *) -->
                          <label>Phường/Xã <span class="text-danger">*</span></label>
                          <input type="text" name="phuong_xa" id="phuong_xa_input" class="form-control"
                                 value="<?= htmlspecialchars($_POST['phuong_xa'] ?? '') ?>"
                                 placeholder="VD: Phường 1">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Quận/Huyện <span class="text-danger">*</span></label>
                          <input type="text" name="quan_huyen" id="quan_huyen_input" class="form-control"
                                 value="<?= htmlspecialchars($_POST['quan_huyen'] ?? '') ?>"
                                 placeholder="VD: Quận 1">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label>Tỉnh/Thành phố <span class="text-danger">*</span></label>
                          <input type="text" name="tinh_tp" id="tinh_tp_input" class="form-control"
                                 value="<?= htmlspecialchars($_POST['tinh_tp'] ?? '') ?>"
                                 placeholder="VD: TP. Hồ Chí Minh">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- ---- 3. Phương thức thanh toán ---- -->
              <div class="checkout-section" id="payment-method">
                <div class="section-header">
                  <div class="section-number">3</div>
                  <h3>Phương thức thanh toán</h3>
                </div>
                <div class="section-content">
                  <div class="payment-options">
                    <div class="payment-option <?= ($_POST['phuong_thuc_tt'] ?? 'COD') === 'COD' ? 'active' : '' ?>">
                      <input type="radio" name="phuong_thuc_tt" id="cod" value="COD"
                             <?= ($_POST['phuong_thuc_tt'] ?? 'COD') === 'COD' ? 'checked' : '' ?>
                             onchange="showPaymentInfo('COD')">
                      <label for="cod">
                        <span class="payment-icon"><i class="bi bi-cash-coin"></i></span>
                        <span class="payment-label">Thanh toán khi nhận hàng (COD)</span>
                      </label>
                    </div>
                    <div class="payment-option <?= ($_POST['phuong_thuc_tt'] ?? '') === 'Chuyển khoản' ? 'active' : '' ?>">
                      <input type="radio" name="phuong_thuc_tt" id="bank" value="Chuyển khoản"
                             <?= ($_POST['phuong_thuc_tt'] ?? '') === 'Chuyển khoản' ? 'checked' : '' ?>
                             onchange="showPaymentInfo('bank')">
                      <label for="bank">
                        <span class="payment-icon"><i class="bi bi-bank"></i></span>
                        <span class="payment-label">Chuyển khoản ngân hàng</span>
                      </label>
                    </div>
                    <div class="payment-option <?= ($_POST['phuong_thuc_tt'] ?? '') === 'Trực tuyến' ? 'active' : '' ?>">
                      <input type="radio" name="phuong_thuc_tt" id="online" value="Trực tuyến"
                             <?= ($_POST['phuong_thuc_tt'] ?? '') === 'Trực tuyến' ? 'checked' : '' ?>
                             onchange="showPaymentInfo('online')">
                      <label for="online">
                        <span class="payment-icon"><i class="bi bi-credit-card"></i></span>
                        <span class="payment-label">Thanh toán trực tuyến</span>
                      </label>
                    </div>
                  </div>

                  <!-- Thông tin chuyển khoản -->
                  <div id="bank-info" class="mt-3 p-3 border rounded bg-light"
                       style="<?= ($_POST['phuong_thuc_tt'] ?? '') === 'Chuyển khoản' ? '' : 'display:none' ?>">
                    <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-primary me-1"></i>Thông tin chuyển khoản</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size:14px;">
                      <tr><td class="text-muted" style="width:40%">Ngân hàng:</td><td><strong>Vietcombank</strong></td></tr>
                      <tr><td class="text-muted">Số tài khoản:</td><td><strong>1234 5678 9012</strong></td></tr>
                      <tr><td class="text-muted">Chủ tài khoản:</td><td><strong>CÔNG TY TNHH ABC</strong></td></tr>
                      <tr><td class="text-muted">Số tiền:</td>
                          <td><strong class="text-danger"><?= number_format($tong_thanh_toan, 0, ',', '.') ?> VNĐ</strong></td></tr>
                      <tr><td class="text-muted">Nội dung CK:</td><td><strong>Thanh toan don hang [Họ tên] [SĐT]</strong></td></tr>
                    </table>
                    <p class="text-muted mb-0 mt-2" style="font-size:12px;">
                      <i class="bi bi-exclamation-circle me-1"></i>Đơn hàng sẽ được xử lý sau khi xác nhận thanh toán.
                    </p>
                  </div>

                                          <!-- Thông báo thanh toán trực tuyến (Yêu cầu của bạn) -->
                          <div id="online-info" class="mt-3 p-3 border rounded bg-warning bg-opacity-10" style="display:none">
                              <div class="d-flex align-items-center">
                                  <div class="spinner-grow spinner-grow-sm text-warning me-2"></div>
                                  <p class="mb-0 text-dark" style="font-size:14px;">
                                      <strong>Thông báo:</strong> Cổng thanh toán trực tuyến đang được bảo trì. 
                                      Bạn vẫn có thể đặt hàng, nhân viên sẽ gọi xác nhận hình thức thanh toán sau.
                                  </p>
                              </div>
                          </div>
                </div>
              </div>

              <!-- ---- 4. Hoàn tất ---- -->
              <div class="checkout-section">
                <div class="section-header">
                  <div class="section-number">4</div>
                  <h3>Hoàn tất đơn hàng</h3>
                </div>
                <div class="section-content">
                  <div class="place-order-container">
                    <button type="submit" class="btn btn-primary place-order-btn">
                      <span class="btn-text">Đặt Hàng</span>
                      <span class="btn-price"><?= number_format($tong_thanh_toan, 0, ',', '.') ?> VNĐ</span>
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- ============ TÓM TẮT ĐƠN HÀNG ============ -->
        <div class="col-lg-5">
          <div class="order-summary" data-aos="fade-left" data-aos-delay="200">
            <div class="order-summary-header">
              <h3>Tóm tắt đơn hàng</h3>
              <span class="item-count"><?= count($cart) ?> sản phẩm</span>
            </div>
            <div class="order-summary-content">
              <div class="order-items">
                <?php foreach ($cart as $item): ?>
                <div class="order-item-static"
                     style="display:flex;align-items:center;margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #eee;">
                  <?php if (!empty($item['sp']['hinh_anh'])): ?>
                  <img src="<?= htmlspecialchars($item['sp']['hinh_anh']) ?>"
                       alt="<?= htmlspecialchars($item['sp']['ten_sp']) ?>"
                       style="width:60px;height:60px;object-fit:cover;border-radius:8px;margin-right:15px;border:1px solid #eee;">
                  <?php else: ?>
                  <div style="width:60px;height:60px;background:#f0f0f0;border-radius:8px;margin-right:15px;
                              display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-phone" style="color:#ccc;"></i>
                  </div>
                  <?php endif; ?>
                  <div>
                    <h6 style="font-size:14px;font-weight:600;margin-bottom:5px;">
                      <?= htmlspecialchars($item['sp']['ten_sp']) ?>
                    </h6>
                    <span style="font-size:14px;color:#777;">
                      <?= $item['so_luong'] ?> × <?= number_format($item['sp']['gia_ban'], 0, ',', '.') ?> VNĐ
                    </span>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              <div class="order-totals">
                <div class="order-subtotal d-flex justify-content-between">
                  <span>Tạm tính</span>
                  <span><?= number_format($tong_tien, 0, ',', '.') ?> VNĐ</span>
                </div>
                <div class="order-shipping d-flex justify-content-between">
                  <span>Vận chuyển</span>
                  <span><?= $phi_ship === 0 ? 'Miễn phí' : number_format($phi_ship, 0, ',', '.') . ' VNĐ' ?></span>
                </div>
                <div class="order-total d-flex justify-content-between fw-bold">
                  <span>Tổng cộng</span>
                  <span><?= number_format($tong_thanh_toan, 0, ',', '.') ?> VNĐ</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /row -->
    </div>
  </section>
</main>

<script>
/* Toggle hiển thị địa chỉ tài khoản / địa chỉ mới */
function toggleDiaChi(val) {
    const tkBox          = document.getElementById('dia_chi_tk_box');
    const moiBox         = document.getElementById('dia_chi_moi_box');
    const diaChiInput    = document.getElementById('dia_chi_input');
    const phuongXaInput  = document.getElementById('phuong_xa_input');
    const quanHuyenInput = document.getElementById('quan_huyen_input');
    const tinhTpInput    = document.getElementById('tinh_tp_input');

    if (val === 'tk') {
        if (tkBox)  tkBox.style.display  = '';
        if (moiBox) moiBox.style.display = 'none';
        // Không cần HTML required khi dùng địa chỉ tài khoản
        [diaChiInput, phuongXaInput, quanHuyenInput, tinhTpInput].forEach(function(el) {
            if (el) el.removeAttribute('required');
        });
    } else {
        if (tkBox)  tkBox.style.display  = 'none';
        if (moiBox) moiBox.style.display = '';
        // Bắt buộc nhập khi chọn địa chỉ mới
        [diaChiInput, phuongXaInput, quanHuyenInput, tinhTpInput].forEach(function(el) {
            if (el) el.setAttribute('required', 'required');
        });
    }
}

/* Hiển thị thông tin phương thức thanh toán */
function showPaymentInfo(type) {
    const bankInfo = document.getElementById('bank-info');
    const onlineInfo = document.getElementById('online-info');
    
    // Ẩn tất cả trước
    if(bankInfo) bankInfo.style.display = 'none';
    if(onlineInfo) onlineInfo.style.display = 'none';

    // Hiện đúng cái cần thiết dựa trên value của radio
    if (type === 'Chuyển khoản') bankInfo.style.display = 'block';
    if (type === 'Trực tuyến') onlineInfo.style.display = 'block';
}

/* Khởi tạo trạng thái khi tải trang */
document.addEventListener('DOMContentLoaded', function () {
    // Địa chỉ
    var chonDiaChi = document.querySelector('input[name="chon_dia_chi"]:checked');
    if (chonDiaChi) toggleDiaChi(chonDiaChi.value);

    // Phương thức thanh toán
    var chonTT = document.querySelector('input[name="phuong_thuc_tt"]:checked');
    if (chonTT) {
        var mapVal = { 'COD': 'COD', 'Chuyển khoản': 'bank', 'Trực tuyến': 'online' };
        var bankInfo   = document.getElementById('bank-info');
        var onlineInfo = document.getElementById('online-info');
        if (bankInfo)   bankInfo.style.display   = chonTT.value === 'Chuyển khoản' ? '' : 'none';
        if (onlineInfo) onlineInfo.style.display = chonTT.value === 'Trực tuyến'   ? '' : 'none';
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>