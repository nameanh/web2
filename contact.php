<?php
$page_title = 'Liên hệ';
$body_class = 'contact-page';
require_once 'includes/header.php';
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Liên hệ</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Liên hệ</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="contact-2" class="contact-2 section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4 mb-5">
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
          <div class="contact-info-box">
            <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
            <div class="info-content">
              <h4>Địa chỉ của chúng tôi</h4>
              <p>123 Sao Hỏa</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
          <div class="contact-info-box">
            <div class="icon-box"><i class="bi bi-envelope"></i></div>
            <div class="info-content">
              <h4>Thông tin liên hệ</h4>
              <p>camlachay@gmail.com</p>
              <p>0123456789</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
          <div class="contact-info-box">
            <div class="icon-box"><i class="bi bi-headset"></i></div>
            <div class="info-content">
              <h4>Giờ hoạt động</h4>
              <p>Thứ 2 - Chủ Nhật: 8:00 - 22:00</p>
              <p>Ngày lễ: Đóng cửa</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="map-section" data-aos="fade-up" data-aos-delay="200">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.664467007328!2d106.68748497500589!3d10.760161489396264!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f2052f5313d%3A0x1d473489e2245b0a!2zMjczIEFuIETGsMahbmcgVsawxqFuZywgUGh14buduZyAzLCBRdeG6rW4gNSwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5oLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1700000000000!5m2!1svi!2s"
        width="100%" height="500" style="border:0" allowfullscreen="" loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <div class="container form-container-overlap">
      <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="300">
        <div class="col-lg-10">
          <div class="contact-form-wrapper">
            <h2 class="text-center mb-4">Gửi lời nhắn cho chúng tôi</h2>
            <form action="forms/contact.php" method="post" class="php-email-form">
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-with-icon">
                      <i class="bi bi-person"></i>
                      <input type="text" class="form-control" name="name" placeholder="Tên của bạn" required>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="input-with-icon">
                      <i class="bi bi-envelope"></i>
                      <input type="email" class="form-control" name="email" placeholder="Địa chỉ Email" required>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <div class="input-with-icon">
                      <i class="bi bi-text-left"></i>
                      <input type="text" class="form-control" name="subject" placeholder="Tiêu đề" required>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group">
                    <div class="input-with-icon">
                      <i class="bi bi-chat-dots message-icon"></i>
                      <textarea class="form-control" name="message" placeholder="Nội dung lời nhắn..."
                                style="height:180px" required></textarea>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="loading">Đang tải</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Lời nhắn của bạn đã được gửi. Cảm ơn bạn!</div>
                </div>
                <div class="col-12 text-center">
                  <button type="submit" class="btn btn-primary btn-submit">GỬI LỜI NHẮN</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>