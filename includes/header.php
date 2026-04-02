<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/db.php';

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['so_luong'];
    }
}

$db = get_db();
$loai_list = $db->query("SELECT ma_loai, ten_loai FROM loai_sp WHERE an_hien = 1 ORDER BY ten_loai");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title><?= isset($page_title) ? htmlspecialchars($page_title).' - Cắm Là Cháy' : 'Cắm Là Cháy' ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />
  <link href="assets/vendor/aos/aos.css" rel="stylesheet" />
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet" />
  <link href="assets/vendor/drift-zoom/drift-basic.css" rel="stylesheet" />
  <link href="assets/css/main.css" rel="stylesheet" />
</head>
<body class="<?= isset($body_class) ? htmlspecialchars($body_class) : 'index-page' ?>">

<header id="header" class="header sticky-top">
  <div class="top-bar py-2">
    <div class="container-fluid container-xl">
      <div class="row align-items-center">
        <div class="col-lg-4 d-none d-lg-flex">
          <div class="top-bar-item">
            <i class="bi bi-telephone-fill me-2"></i>
            <span>Cần hỗ trợ? Liên hệ chúng tôi: </span>
            <a href="tel:+1234567890">0123456789</a>
          </div>
        </div>
        <div class="col-lg-4 col-md-12 text-center">
          <div class="announcement-slider swiper init-swiper">
            <script type="application/json" class="swiper-config">
              {"loop":true,"speed":600,"autoplay":{"delay":5000},"slidesPerView":1,"direction":"vertical","effect":"slide"}
            </script>
            <div class="swiper-wrapper">
              <div class="swiper-slide">🚚 Miễn phí giao hàng toàn quốc cho đơn hàng trên 5.000.000VND</div>
              <div class="swiper-slide">💰 1 đổi 1 trong 30 ngày nếu có lỗi từ nhà sản xuất.</div>
              <div class="swiper-slide">🎁 Tặng kèm sạc nhanh 20W cho tất cả điện thoại.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="main-header">
    <div class="container-fluid container-xl">
      <div class="d-flex py-3 align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
          <h1 class="sitename">Cắm Là Cháy</h1>
        </a>
        <form class="search-form desktop-search-form" action="search-results.php" method="GET">
          <div class="input-group">
            <input type="text" name="q" class="form-control"
              placeholder="Tìm kiếm điện thoại, phụ kiện"
              value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" />
            <button class="btn" type="submit"><i class="bi bi-search"></i></button>
          </div>
        </form>
        <div class="header-actions d-flex align-items-center justify-content-end">
          <button class="header-action-btn mobile-search-toggle d-xl-none" type="button"
            data-bs-toggle="collapse" data-bs-target="#mobileSearch">
            <i class="bi bi-search"></i>
          </button>
          <div class="dropdown account-dropdown">
            <button class="header-action-btn" data-bs-toggle="dropdown">
              <i class="bi bi-person"></i>
            </button>
            <div class="dropdown-menu">
              <div class="dropdown-header">
                <h6>Chào mừng đến với <span class="sitename">Cắm Là Cháy</span></h6>
                <p class="mb-0">Truy cập tài khoản &amp; quản lý đơn hàng</p>
              </div>
              <div class="dropdown-body">
                <?php if (isset($_SESSION['khach_hang'])): ?>
                  <span class="dropdown-item text-muted">
                    <i class="bi bi-person-circle me-2"></i>
                    <?= htmlspecialchars($_SESSION['khach_hang']['ho_ten']) ?>
                  </span>
                  <a class="dropdown-item d-flex align-items-center" href="account.php">
                    <i class="bi bi-person-circle me-2"></i><span>Hồ sơ của tôi</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="account.php#orders">
                    <i class="bi bi-bag-check me-2"></i><span>Đơn hàng của tôi</span>
                  </a>
                <?php else: ?>
                  <a class="dropdown-item d-flex align-items-center" href="login.php">
                    <i class="bi bi-box-arrow-in-right me-2"></i><span>Đăng nhập</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="register.php">
                    <i class="bi bi-person-plus me-2"></i><span>Đăng ký</span>
                  </a>
                <?php endif; ?>
              </div>
              <?php if (isset($_SESSION['khach_hang'])): ?>
              <div class="dropdown-footer">
                <a class="dropdown-item d-flex align-items-center text-danger" href="logout.php">
                  <i class="bi bi-box-arrow-right me-2"></i><span>Đăng xuất</span>
                </a>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <a href="cart.php" class="header-action-btn">
            <i class="bi bi-cart3"></i>
            <span class="badge"><?= $cart_count ?></span>
          </a>
          <i class="mobile-nav-toggle d-xl-none bi bi-list me-0"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="header-nav">
    <div class="container-fluid container-xl position-relative">
      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php">Trang chủ</a></li>
          <li><a href="all.php">Tất cả sản phẩm</a></li>
          <li class="dropdown">
            <a href="#"><span>Thương hiệu</span><i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <?php if ($loai_list && $loai_list->num_rows > 0):
                while ($loai = $loai_list->fetch_assoc()): ?>
                <li>
                  <a href="brand.php?ma_loai=<?= $loai['ma_loai'] ?>">
                    <?= htmlspecialchars($loai['ten_loai']) ?>
                  </a>
                </li>
              <?php endwhile; endif; ?>
            </ul>
          </li>
          <li><a href="cart.php">Giỏ Hàng</a></li>
          <li><a href="checkout.php">Thanh toán</a></li>
          <li><a href="about.php">Về chúng tôi</a></li>
        </ul>
      </nav>
    </div>
  </div>

  <div class="collapse" id="mobileSearch">
    <div class="container">
      <form class="search-form" action="search-results.php" method="GET">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Tìm kiếm điện thoại, phụ kiện" />
          <button class="btn" type="submit"><i class="bi bi-search"></i></button>
        </div>
      </form>
    </div>
  </div>
</header>