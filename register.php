<?php
$page_title = 'Đăng ký';
$body_class = 'register-page';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten     = trim($_POST['ho_ten'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $dien_thoai = trim($_POST['dien_thoai'] ?? '');
    $mat_khau   = $_POST['mat_khau'] ?? '';
    $confirm    = $_POST['confirm_mat_khau'] ?? '';

    if (!$ho_ten || !$email || !$mat_khau) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email không hợp lệ.';
    } elseif (strlen($mat_khau) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
    } elseif ($mat_khau !== $confirm) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } else {
        $db = get_db();
        $stmt = $db->prepare("SELECT ma_kh FROM khach_hang WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Email này đã được đăng ký. Vui lòng dùng email khác.';
        } else {
            $hash = password_hash($mat_khau, PASSWORD_DEFAULT);
            $ins = $db->prepare("INSERT INTO khach_hang (ho_ten, email, mat_khau, dien_thoai) VALUES (?,?,?,?)");
            $ins->bind_param("ssss", $ho_ten, $email, $hash, $dien_thoai);
            if ($ins->execute()) {
                $success = 'Đăng ký thành công! Đang chuyển đến trang đăng nhập...';
                header("Refresh: 2; url=login.php");
            } else {
                $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
            }
        }
    }
}
require_once 'includes/header.php';
?>
<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Đăng ký tài khoản Cắm Là Cháy</h1>
      <nav class="breadcrumbs"><ol>
        <li><a href="index.php">Trang chủ</a></li>
        <li class="current">Đăng ký</li>
      </ol></nav>
    </div>
  </div>
  <section id="register" class="register section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="registration-form-wrapper">
            <div class="form-header text-center">
              <h2>Tạo tài khoản Cắm Là Cháy</h2>
              <p>Bắt đầu hành trình công nghệ của bạn ngay hôm nay!</p>
            </div>
            <?php if ($error): ?><div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success text-center"><?= htmlspecialchars($success) ?></div><?php endif; ?>
            <div class="row">
              <div class="col-lg-8 mx-auto">
                <form method="POST" action="register.php">
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="ho_ten" name="ho_ten"
                      placeholder="Họ và Tên" required value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>">
                    <label for="ho_ten">Họ và Tên *</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email"
                      placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <label for="email">Địa chỉ Email *</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="tel" class="form-control" id="dien_thoai" name="dien_thoai"
                      placeholder="Số điện thoại" value="<?= htmlspecialchars($_POST['dien_thoai'] ?? '') ?>">
                    <label for="dien_thoai">Số điện thoại (Không bắt buộc)</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="mat_khau" name="mat_khau"
                      placeholder="Mật khẩu" required minlength="6">
                    <label for="mat_khau">Mật khẩu *</label>
                  </div>
                  <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirm_mat_khau"
                      name="confirm_mat_khau" placeholder="Xác nhận mật khẩu" required>
                    <label for="confirm_mat_khau">Xác nhận lại mật khẩu *</label>
                  </div>
                  <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                    <label class="form-check-label" for="termsCheck">
                      Tôi đồng ý với <a href="tos.php">Điều khoản dịch vụ</a> và <a href="privacy.php">Chính sách bảo mật</a>
                    </label>
                  </div>
                  <div class="d-grid mb-4">
                    <button type="submit" class="btn btn-register">Tạo tài khoản</button>
                  </div>
                  <div class="login-link text-center">
                    <p>Bạn đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require_once 'includes/footer.php'; ?>
