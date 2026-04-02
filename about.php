<?php
$page_title = 'Về chúng tôi';
$body_class = 'about-page';
require_once 'includes/header.php';
?>

<main id="main">
  <div class="page-title dark-background">
    <div class="container">
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Về chúng tôi</li>
        </ol>
      </nav>
      <h1>Về chúng tôi</h1>
    </div>
  </div>

  <section id="about-us" class="about-us section">
    <div class="container">
      <div class="row gy-4">
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
          <!-- Ảnh về chúng tôi -->
        </div>
        <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
          <div class="content">
            <h3 class="sitename">Cắm Là Cháy</h3>
            <h2>Nắm bắt công nghệ, làm chủ phong cách của bạn</h2>
            <p class="mt-4">
              Chúng tôi cung cấp thiết bị công nghệ giúp bạn kết nối và thể hiện cá tính, vì chiếc điện thoại sẽ luôn bên bạn. Với Cắm Là Cháy, bạn có thể hoàn toàn yên tâm tìm được chiếc smartphone chính hãng, phù hợp nhất với mọi nhu cầu.
            </p>

            <div class="stats row">
              <div class="col-lg-6 col-md-6">
                <div class="stats-item">
                  <i class="bi bi-person-check-fill"></i>
                  <div class="stats-content">
                    <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
                    <p>Khách hàng hài lòng</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="stats-item">
                  <i class="bi bi-bag-check-fill"></i>
                  <div class="stats-content">
                    <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
                    <p>Đơn hàng được hoàn thành</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="stats-item">
                  <i class="bi bi-headset"></i>
                  <div class="stats-content">
                    <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
                    <p>Hỗ trợ khách hàng thành công</p>
                  </div>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="stats-item">
                  <i class="bi bi-people-fill"></i>
                  <div class="stats-content">
                    <span data-purecounter-start="0" data-purecounter-end="6" data-purecounter-duration="1" class="purecounter"></span>
                    <p>Nhân sự</p>
                  </div>
                </div>
              </div>
            </div>

            <p class="mt-4">
              Chúng tôi tin rằng, mỗi chiếc điện thoại không chỉ là công cụ, mà còn là một phần mở rộng cá tính của bạn.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="testimonials" class="testimonials section light-background">
    <div class="container section-title" data-aos="fade-up">
      <h2>Chiến dịch: "Sạc Là Full Pin!!!"</h2>
      <p>Nhấn mạnh tầm quan trọng của thiết bị di động chính hãng và tăng doanh số sản phẩm cao cấp.</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="swiper init-swiper">
        <script type="application/json" class="swiper-config">
        {
          "loop": true,
          "speed": 600,
          "autoplay": { "delay": 5000 },
          "slidesPerView": "auto",
          "pagination": { "el": ".swiper-pagination", "type": "bullets", "clickable": true },
          "breakpoints": {
            "320": { "slidesPerView": 1, "spaceBetween": 40 },
            "1200": { "slidesPerView": 3, "spaceBetween": 20 }
          }
        }
        </script>
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="stars">
                <i class="bi bi-battery-full"></i><i class="bi bi-battery-full"></i>
                <i class="bi bi-battery-full"></i><i class="bi bi-battery-full"></i>
                <i class="bi bi-battery-full"></i>
              </div>
              <p>"Hãy là chính bạn, **chọn điện thoại theo cách bạn muốn**! Đừng ngại ngần thể hiện phong cách qua chiếc điện thoại của bạn."</p>
              <div class="profile mt-auto"><h3>Khẩu hiệu 1</h3></div>
            </div>
          </div>
          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="stars">
                <i class="bi bi-phone-fill"></i><i class="bi bi-phone-fill"></i>
                <i class="bi bi-phone-fill"></i><i class="bi bi-phone-fill"></i>
                <i class="bi bi-phone-fill"></i>
              </div>
              <p>"Hãy phá hủy nó! **Sử dụng hết công suất** (chơi game, xem phim, chụp ảnh không ngừng) để lan tỏa chiến dịch và cảm nhận sức mạnh thiết bị."</p>
              <div class="profile mt-auto"><h3>Khẩu hiệu 2</h3></div>
            </div>
          </div>
          <div class="swiper-slide">
            <div class="testimonial-item">
              <div class="stars">
                <i class="bi bi-cash-stack"></i><i class="bi bi-cash-stack"></i>
                <i class="bi bi-cash-stack"></i><i class="bi bi-cash-stack"></i>
                <i class="bi bi-cash-stack"></i>
              </div>
              <p>"Gia tăng doanh số! Đừng quên **nâng cấp ngay một chiếc điện thoại mới** khi bạn đã 'phá hủy' chiếc cũ với cường độ cao!"</p>
              <div class="profile mt-auto"><h3>Khẩu hiệu 3</h3></div>
            </div>
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </section>

  <section id="team" class="team section">
    <div class="container section-title" data-aos="fade-up">
      <h2>Đội ngũ nhân sự</h2>
      <p>Những con người 'Cháy' nhất của chúng tôi!</p>
    </div>

    <div class="container">
      <div class="row gy-4">
        <?php
        $team = [
          ['Ma Vương Tulen', 'Nhà cung cấp', 'Tôi luôn cung cấp smartphone/linh kiện chính hãng có chất lượng tốt nhất, hàng lỗi chắc chắn không phải do khâu kiểm tra của chúng tôi.'],
          ['Kim Trong Un', 'Giám đốc chiến lược', "Chiến lược 'Sạc Là Full Pin!!!' sẽ giúp tăng doanh số nhanh chóng và khuyến khích khách hàng trải nghiệm công nghệ mới bằng chính ngân sách của mình."],
          ['Đỗ Nam Trung', 'Nhân viên bán hàng của năm', 'Tôi tự tin là người tư vấn điện thoại tận tâm nhất miền Nam, bất kỳ thắc mắc nào về cấu hình hãy hỏi tôi!'],
          ['Ác Quỉ Phai Phai', 'Kỹ thuật viên', "Tôi sẽ sửa chữa điện thoại lỗi thành đồ tái chế và nhắc khách hàng mua ngay điện thoại mới. Khỏi cần chờ đợi!"],
          ['Con chim vô ơn', 'Người kiểm tra', "Bất kỳ sản phẩm nào có vết trầy xước nhẹ sẽ được bán với giá đặc biệt và coi đó là 'Phiên bản giới hạn - Độc quyền'."],
          ['Thầy Huấn', 'Nhân viên giao hàng', "Những chiếc điện thoại được giao tới tay khách hàng sẽ nguyên seal, chính hãng và nhanh đến mức 'cháy'."],
        ];
        foreach ($team as $i => $member):
          $delay = ($i + 1) * 100;
        ?>
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
          <div class="team-member">
            <div class="member-img">
              <div class="social">
                <a href=""><i class="bi bi-twitter-x"></i></a>
                <a href=""><i class="bi bi-facebook"></i></a>
                <a href=""><i class="bi bi-instagram"></i></a>
                <a href=""><i class="bi bi-linkedin"></i></a>
              </div>
            </div>
            <div class="member-info">
              <h4><?= htmlspecialchars($member[0]) ?></h4>
              <span><?= htmlspecialchars($member[1]) ?></span>
              <p><?= htmlspecialchars($member[2]) ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>