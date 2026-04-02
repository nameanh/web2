<?php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';

// Nếu chưa đăng nhập thì redirect
if (!isset($_SESSION['khach_hang'])) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

$db = get_db();

// Xử lý thêm/xóa/cập nhật giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $ma_sp  = (int)($_POST['ma_sp'] ?? 0);

    /* ---- THÊM SẢN PHẨM ---- */
    if ($action === 'add' && $ma_sp > 0) {
        $sl = max(1, (int)($_POST['so_luong'] ?? 1));
        if (!isset($_SESSION['cart'][$ma_sp])) {
            $stmt = $db->prepare("SELECT ma_sp, ten_sp, hinh_anh, so_luong_ton,
                                         ROUND(gia_nhap_bq*(1+ty_le_loi_nhuan/100),0) AS gia_ban
                                  FROM san_pham WHERE ma_sp = ? AND an_hien = 1");
            $stmt->bind_param('i', $ma_sp);
            $stmt->execute();
            $sp = $stmt->get_result()->fetch_assoc();
            if ($sp) {
                $_SESSION['cart'][$ma_sp] = ['so_luong' => $sl, 'sp' => $sp];
            }
        } else {
            $_SESSION['cart'][$ma_sp]['so_luong'] += $sl;
        }
        session_write_close();
        echo json_encode(['success' => true]);
        exit;
    }

    /* ---- CẬP NHẬT SỐ LƯỢNG ---- */
    if ($action === 'update' && $ma_sp > 0) {
        $sl = (int)($_POST['so_luong'] ?? 1);
        if (isset($_SESSION['cart'][$ma_sp])) {
            if ($sl <= 0) {
                unset($_SESSION['cart'][$ma_sp]);
            } else {
                // Giới hạn theo tồn kho
                $ton = (int)($_SESSION['cart'][$ma_sp]['sp']['so_luong_ton'] ?? 9999);
                $_SESSION['cart'][$ma_sp]['so_luong'] = min($sl, $ton);
            }
        }
        session_write_close();
        header('Location: cart.php');
        exit;
    }

/* ---- XÓA SẢN PHẨM ---- */
if ($action === 'remove' && $ma_sp > 0) {
    if (isset($_SESSION['cart'][$ma_sp])) {
        unset($_SESSION['cart'][$ma_sp]);
    }
    session_write_close(); 
    header('Location: cart.php');
    exit(); // Cực kỳ quan trọng để trình duyệt nhảy trang ngay lập tức
}
}

// Đọc giỏ hàng từ session
$cart = $_SESSION['cart'] ?? [];

// Tính tổng tiền
$tong_tien = 0;
foreach ($cart as $item) {
    $tong_tien += $item['sp']['gia_ban'] * $item['so_luong'];
}

$page_title = 'Giỏ hàng';
$body_class = 'cart-page';
require_once 'includes/header.php';
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Giỏ hàng</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Giỏ hàng</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="cart" class="cart section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row">
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
          <div class="cart-items">
            <?php if (empty($cart)): ?>
            <div class="text-center py-5">
              <i class="bi bi-cart-x" style="font-size:4rem;color:#ccc;"></i>
              <p class="mt-3 text-muted">Giỏ hàng của bạn đang trống.</p>
              <a href="all.php" class="btn btn-primary mt-2">Tiếp tục mua sắm</a>
            </div>
            <?php else: ?>
            <div class="cart-header d-none d-lg-block">
              <div class="row align-items-center">
                <div class="col-lg-6"><h5>Sản phẩm</h5></div>
                <div class="col-lg-2 text-center"><h5>Đơn giá</h5></div>
                <div class="col-lg-2 text-center"><h5>Số lượng</h5></div>
                <div class="col-lg-2 text-center"><h5>Thành tiền</h5></div>
              </div>
            </div>

            <?php foreach ($cart as $ma_sp => $item):
              $sp        = $item['sp'];
              $sl        = $item['so_luong'];
              $thanh_tien = $sp['gia_ban'] * $sl;
            ?>
            <div class="cart-item">
              <div class="row align-items-center">
                <!-- Tên + nút xóa -->
                <div class="col-lg-6 col-12 mt-3 mt-lg-0 mb-lg-0 mb-3">
                  <div class="product-info d-flex align-items-center">
                    <div class="product-image">
                      <a href="product-details.php?ma_sp=<?= $sp['ma_sp'] ?>">
                        <?php if ($sp['hinh_anh']): ?>
                          <img src="<?= htmlspecialchars($sp['hinh_anh']) ?>"
                               alt="<?= htmlspecialchars($sp['ten_sp']) ?>"
                               style="width:80px;height:80px;object-fit:cover;border-radius:8px;" />
                        <?php else: ?>
                          <div style="width:80px;height:80px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;border-radius:8px;">
                            <i class="bi bi-phone" style="color:#ccc;font-size:2rem;"></i>
                          </div>
                        <?php endif; ?>
                      </a>
                    </div>
                    <div class="product-details ms-3">
                      <a href="product-details.php?ma_sp=<?= $sp['ma_sp'] ?>">
                        <h6 class="product-title"><?= htmlspecialchars($sp['ten_sp']) ?></h6>
                      </a>
                      <!-- Nút xóa dùng POST riêng, không dùng JS submit trực tiếp -->
                      <form method="POST" action="cart.php" class="d-inline"
                            onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                        <input type="hidden" name="action" value="remove">
                        <input type="hidden" name="ma_sp" value="<?= $ma_sp ?>">
                        <button class="remove-item" type="submit">
                          <i class="bi bi-trash"></i> Xóa
                        </button>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Đơn giá -->
                <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                  <div class="price-tag">
                    <span class="current-price"><?= number_format($sp['gia_ban'], 0, ',', '.') ?> VNĐ</span>
                  </div>
                </div>

                <!-- Số lượng – FIX: chỉ submit khi người dùng THỰC SỰ thay đổi số, tránh nhảy số -->
                <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                  <form method="POST" action="cart.php" id="form_qty_<?= $ma_sp ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="ma_sp"  value="<?= $ma_sp ?>">
                    <div class="quantity-selector d-flex justify-content-center">
                      <button class="quantity-btn decrease" type="button"
                              onclick="changeQty(<?= $ma_sp ?>, -1, <?= (int)$sp['so_luong_ton'] ?>)">
                        <i class="bi bi-dash"></i>
                      </button>
                      <input type="number" name="so_luong" id="qty_<?= $ma_sp ?>"
                             class="quantity-input"
                             value="<?= $sl ?>" min="1" max="<?= (int)$sp['so_luong_ton'] ?>"
                             onchange="submitQty(<?= $ma_sp ?>)">
                      <button class="quantity-btn increase" type="button"
                              onclick="changeQty(<?= $ma_sp ?>, 1, <?= (int)$sp['so_luong_ton'] ?>)">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                  </form>
                </div>

                <!-- Thành tiền -->
                <div class="col-lg-2 col-12 mt-3 mt-lg-0 text-center">
                  <div class="item-total">
                    <strong><?= number_format($thanh_tien, 0, ',', '.') ?> VNĐ</strong>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <?php if (!empty($cart)): ?>
        <div class="col-lg-4 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="300">
          <div class="cart-summary">
            <h4 class="summary-title">Tóm tắt đơn hàng</h4>
            <div class="summary-item">
              <span class="summary-label">Tạm tính</span>
              <span class="summary-value"><?= number_format($tong_tien, 0, ',', '.') ?> VNĐ</span>
            </div>
            <div class="summary-item">
              <span class="summary-label">Phí vận chuyển</span>
              <span class="summary-value"><?= $tong_tien >= 5000000 ? 'Miễn phí' : '20.000 VNĐ' ?></span>
            </div>
            <div class="summary-total">
              <span class="summary-label">Tổng cộng</span>
              <span class="summary-value">
                <?= number_format($tong_tien >= 5000000 ? $tong_tien : $tong_tien + 20000, 0, ',', '.') ?> VNĐ
              </span>
            </div>
            <div class="checkout-button">
              <a href="checkout.php" class="btn btn-accent w-100">
                Tiến hành thanh toán <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>

<script>
/**
 * Thay đổi số lượng bằng nút +/-
 * Chỉ submit form sau khi cập nhật giá trị – tránh việc
 * button click kích hoạt 2 lần (onclick + onchange của input).
 */
function changeQty(ma_sp, delta, max) {
    const input = document.getElementById('qty_' + ma_sp);
    const form = document.getElementById('form_qty_' + ma_sp);
    if (!input || !form) return;

    let val = parseInt(input.value, 10) || 1;
    let newVal = val + delta;

    // Giới hạn trong khoảng cho phép
    if (newVal < 1) newVal = 1;
    if (newVal > max) newVal = max;

    // Chỉ thực hiện nếu giá trị thực sự thay đổi
    if (newVal !== val) {
        input.value = newVal;
        form.submit(); // Gửi form duy nhất 1 lần
    }
}

/**
 * Submit khi người dùng tự gõ giá trị vào ô input
 */
function submitQty(ma_sp) {
    const input = document.getElementById('qty_' + ma_sp);
    const max   = parseInt(input.max, 10) || 9999;
    let val     = parseInt(input.value, 10) || 1;
    if (val < 1)   val = 1;
    if (val > max) val = max;
    input.value = val;
    document.getElementById('form_qty_' + ma_sp).submit();
}
</script>

<?php require_once 'includes/footer.php'; ?>