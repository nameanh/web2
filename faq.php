<?php
$page_title = 'Câu hỏi thường gặp';
$body_class = 'faq-page';
require_once 'includes/header.php';
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Câu hỏi thường gặp</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Câu hỏi thường gặp</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="faq" class="faq section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4 justify-content-between">
        <div class="col-lg-8">
          <div class="faq-list">
            <div class="faq-item faq-active" data-aos="fade-up" data-aos-delay="100">
              <h3>Khi mua điện thoại có bảo hành không?</h3>
              <div class="faq-content">
                <p>Khi mua điện thoại tại Cắm Là Cháy, sản phẩm sẽ được bảo hành chính hãng 12 tháng (hoặc 24 tháng tùy hãng) và hỗ trợ 1 đổi 1 trong 30 ngày đầu nếu có lỗi do nhà sản xuất.</p>
              </div>
              <i class="bi bi-plus faq-toggle"></i>
            </div>
            <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
              <h3>Shop có hỗ trợ ship toàn quốc không?</h3>
              <div class="faq-content">
                <p>Nếu quý khách ở xa cửa hàng, Cắm Là Cháy sẽ gửi điện thoại cho quý khách qua những đơn vị vận chuyển uy tín nhất, đảm bảo đóng gói, chống sốc an toàn và đồng kiểm khi nhận hàng.</p>
              </div>
              <i class="bi bi-plus faq-toggle"></i>
            </div>
            <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
              <h3>Shop có chương trình thu cũ đổi mới không?</h3>
              <div class="faq-content">
                <p>Cắm Là Cháy có chương trình thu cũ đổi mới với trợ giá tốt nhất thị trường. Quý khách vui lòng mang máy cũ đến cửa hàng để được định giá và tư vấn chương trình lên đời máy mới tiết kiệm nhất.</p>
              </div>
              <i class="bi bi-plus faq-toggle"></i>
            </div>
            <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
              <h3>Tôi có thể đến trực tiếp cửa hàng để xem và mua máy không?</h3>
              <div class="faq-content">
                <p>Quý khách hoàn toàn có thể đến cửa hàng tại địa chỉ "123 Sao Hỏa" để trải nghiệm, trên tay và mua máy.</p>
              </div>
              <i class="bi bi-plus faq-toggle"></i>
            </div>
          </div>
        </div>

        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
          <div class="faq-card">
            <i class="bi bi-chat-dots-fill"></i>
            <h3>Không thấy câu hỏi của bạn?</h3>
            <p>Đừng lo, bạn có thể gọi trực tiếp đến số điện thoại 0123456789 hoặc gửi mail đến email camlachay@gmail.com để được tư vấn, chúng tôi luôn đón tiếp bạn.</p>
            <a href="contact.php" class="btn btn-primary">Liên hệ chúng tôi</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>