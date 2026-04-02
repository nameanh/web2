<?php
$page_title = 'Đăng nhập';
$body_class = 'login-page';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (isset($_SESSION['khach_hang'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';

    if (!$email || !$mat_khau) {
        $error = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM khach_hang WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $kh = $result->fetch_assoc();

        if (!$kh) {
            $error = 'Email không tồn tại.';
        } elseif ($kh['trang_thai'] == 0) {
            $error = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ.';
        } elseif (!password_verify($mat_khau, $kh['mat_khau'])) {
            $error = 'Mật khẩu không đúng.';
        } else {
            // Đăng nhập thành công
            $_SESSION['khach_hang'] = [
                'ma_kh'  => $kh['ma_kh'],
                'ho_ten' => $kh['ho_ten'],
                'email'  => $kh['email'],
            ];
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . $redirect);
            exit;
        }
    }
}
require_once 'includes/header.php';
?>
<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Đăng nhập</h1>
      <nav class="breadcrumbs"><ol>
        <li><a href="index.php">Trang chủ</a></li>
        <li class="current">Đăng nhập</li>
      </ol></nav>
    </div>
  </div>
  <section id="login" class="login section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
          <div class="auth-container" data-aos="fade-in" data-aos-delay="200">
            <div class="auth-form login-form active">
              <div class="form-header">
                <h3>Chào mừng bạn đã trở lại!</h3>
                <p>Đăng nhập tài khoản của bạn</p>
              </div>
              <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
              <?php endif; ?>
              <form class="auth-form-content" method="POST" action="login.php<?= isset($_GET['redirect']) ? '?redirect='.urlencode($_GET['redirect']) : '' ?>">
                <div class="input-group mb-3">
                  <span class="input-icon"><i class="bi bi-envelope"></i></span>
                  <input type="email" class="form-control" placeholder="Địa chỉ Email"
                    name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                </div>
                <div class="input-group mb-3">
                  <span class="input-icon"><i class="bi bi-lock"></i></span>
                  <input type="password" class="form-control" placeholder="Mật khẩu"
                    name="mat_khau" required />
                  <span class="password-toggle"><i class="bi bi-eye"></i></span>
                </div>
                <button type="submit" class="auth-btn primary-btn mb-3">
                  Đăng nhập <i class="bi bi-arrow-right"></i>
                </button>
                <div class="switch-form">
                  <span>Bạn chưa có tài khoản?</span>
                  <a href="register.php" class="switch-btn">Tạo tài khoản</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php require_once 'includes/footer.php'; ?>
