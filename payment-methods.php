<?php
$page_title = 'Phương thức thanh toán';
$body_class = 'payment-methods-page';
require_once 'includes/header.php';
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Phương thức thanh toán</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Phương thức thanh toán</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="paymnt-methods" class="paymnt-methods section">
    <div class="container" data-aos="fade-up">
      <div class="payment-header text-center" data-aos="fade-up">
        <h2>Phương thức thanh toán</h2>
        <p>Chọn từ các tùy chọn thanh toán an toàn và tiện lợi của chúng tôi</p>
      </div>

      <div class="payment-options" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-4">
          <div class="col-md-6 col-lg-4">
            <div class="payment-card credit-card">
              <div class="card-content">
                <div class="icon-box"><i class="bi bi-credit-card"></i></div>
                <h4>Thẻ Tín dụng / Ghi nợ</h4>
                <p>Visa, Mastercard, American Express</p>
                <div class="accepted-cards">
                  <span class="card-icon visa">Visa</span>
                  <span class="card-icon mastercard">Mastercard</span>
                  <span class="card-icon amex">Amex</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="payment-card paypal">
              <div class="card-content">
                <div class="icon-box"><i class="bi bi-paypal"></i></div>
                <h4>PayPal</h4>
                <p>Thanh toán trực tuyến nhanh chóng và an toàn</p>
                <div class="accepted-cards">
                  <span class="card-icon paypal">PayPal</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-4">
            <div class="payment-card bank-transfer">
              <div class="card-content">
                <div class="icon-box"><i class="bi bi-bank"></i></div>
                <h4>Chuyển khoản Ngân hàng</h4>
                <p>Chuyển khoản trực tiếp từ ngân hàng</p>
                <div class="accepted-cards">
                  <span class="card-icon bank">Bank</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="security-features" data-aos="fade-up" data-aos-delay="200">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="security-content">
              <h3>Thanh toán An toàn</h3>
              <p class="subtitle">Bảo mật của bạn là ưu tiên hàng đầu của chúng tôi</p>
              <ul class="security-list">
                <li>
                  <i class="bi bi-shield-check"></i>
                  <div class="feature-text">
                    <h5>Mã hóa SSL</h5>
                    <p>Tất cả các giao dịch đều được bảo vệ bằng mã hóa SSL 256-bit</p>
                  </div>
                </li>
                <li>
                  <i class="bi bi-lock"></i>
                  <div class="feature-text">
                    <h5>Tuân thủ PCI</h5>
                    <p>Chúng tôi tuân theo các tiêu chuẩn bảo mật PCI DSS nghiêm ngặt</p>
                  </div>
                </li>
                <li>
                  <i class="bi bi-shield-lock"></i>
                  <div class="feature-text">
                    <h5>Chống gian lận</h5>
                    <p>Hệ thống phát hiện và ngăn chặn gian lận tiên tiến</p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="process-steps">
              <h4>Quy trình Thanh toán</h4>
              <div class="steps-list">
                <div class="step">
                  <div class="step-number">1</div>
                  <div class="step-content">
                    <h5>Chọn Phương thức Thanh toán</h5>
                    <p>Chọn tùy chọn thanh toán ưa thích của bạn khi thanh toán</p>
                  </div>
                </div>
                <div class="step">
                  <div class="step-number">2</div>
                  <div class="step-content">
                    <h5>Nhập Chi tiết</h5>
                    <p>Cung cấp thông tin thanh toán của bạn một cách an toàn</p>
                  </div>
                </div>
                <div class="step">
                  <div class="step-number">3</div>
                  <div class="step-content">
                    <h5>Xác nhận Thanh toán</h5>
                    <p>Xem lại và xác nhận chi tiết thanh toán của bạn</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="payment-faqs" data-aos="fade-up" data-aos-delay="300">
        <h3>Câu hỏi thường gặp về Thanh toán</h3>
        <div class="faq-grid">
          <div class="faq-item">
            <h3>Khi nào tôi sẽ bị tính phí? <i class="bi bi-chevron-down faq-toggle"></i></h3>
            <div class="faq-answer">
              <p>Thanh toán của bạn sẽ được xử lý ngay sau khi bạn đặt hàng. Phí sẽ xuất hiện trên sao kê của bạn trong vòng 1-2 ngày làm việc.</p>
            </div>
          </div>
          <div class="faq-item">
            <h3>Lưu trữ thẻ của tôi có an toàn không? <i class="bi bi-chevron-down faq-toggle"></i></h3>
            <div class="faq-answer">
              <p>Có, chúng tôi sử dụng mã hóa tiêu chuẩn ngành để bảo vệ thông tin thanh toán của bạn. Chi tiết thẻ của bạn không bao giờ được lưu trữ trên máy chủ của chúng tôi.</p>
            </div>
          </div>
          <div class="faq-item">
            <h3>Bạn chấp nhận những loại tiền tệ nào? <i class="bi bi-chevron-down faq-toggle"></i></h3>
            <div class="faq-answer">
              <p>Hiện tại chúng tôi chỉ chấp nhận thanh toán bằng Việt Nam Đồng (VND). Các loại tiền tệ khác sẽ được hỗ trợ trong tương lai.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="payment-support" data-aos="fade-up" data-aos-delay="400">
        <div class="support-content">
          <i class="bi bi-headset"></i>
          <h4>Cần hỗ trợ?</h4>
          <p>Đội ngũ hỗ trợ thanh toán của chúng tôi luôn sẵn sàng 24/7 để hỗ trợ bạn</p>
          <div class="support-actions">
            <a href="#" class="btn-primary"><i class="bi bi-chat-dots"></i> Trò chuyện ngay</a>
            <span class="divider">hoặc</span>
            <a href="#" class="contact-email"><i class="bi bi-envelope"></i> hotro@camlachay.com</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>