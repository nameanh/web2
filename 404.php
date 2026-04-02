<?php
$page_title = '404 - Không tìm thấy trang';
$body_class = 'error-404-page';
require_once 'includes/header.php';
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">404</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">404</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="error-404" class="error-404 section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="text-center">
        <div class="error-icon mb-4" data-aos="zoom-in" data-aos-delay="200">
          <i class="bi bi-exclamation-circle"></i>
        </div>
        <h1 class="error-code mb-4" data-aos="fade-up" data-aos-delay="300">404</h1>
        <h2 class="error-title mb-3" data-aos="fade-up" data-aos-delay="400">Xin lỗi! Không tìm thấy trang</h2>
        <p class="error-text mb-4" data-aos="fade-up" data-aos-delay="500">
          Trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không khả dụng
        </p>
        <div class="error-action" data-aos="fade-up" data-aos-delay="700">
          <a href="index.php" class="btn btn-primary">Trở về Trang chủ</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>