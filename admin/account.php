<?php
$page_title = 'Tài khoản Admin';
$body_class = 'account-page';
$extra_css  = ['account_admin.css'];
require_once 'includes/header.php';
$admin = $_SESSION['admin'];
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Tài khoản</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Tài khoản</li>
        </ol>
      </nav>
    </div>
  </div>

  <section id="account" class="account section">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="mobile-menu d-lg-none mb-4">
        <button class="mobile-menu-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#profileMenu">
          <i class="bi bi-grid"></i> <span>Menu</span>
        </button>
      </div>

      <div class="row g-4">
        <div class="col-lg-3">
          <div class="profile-menu collapse d-lg-block" id="profileMenu">
            <div class="user-info" data-aos="fade-right">
              <div class="user-avatar">
                <span class="status-badge"><i class="bi bi-shield-check"></i></span>
              </div>
              <h4><?= htmlspecialchars($admin['ten_admin']) ?></h4>
              <h6 style="color:rgb(129,129,128)">user: <?= htmlspecialchars($admin['ten_dang_nhap']) ?></h6>
              <div class="user-status">
                <i class="bi bi-award"></i>
                <span>Quản trị viên hệ thống</span>
              </div>
            </div>
            <nav class="menu-nav">
              <ul class="nav flex-column" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" data-bs-toggle="tab" href="#info">
                    <i class="bi bi-box-seam"></i> <span>Hồ sơ admin</span>
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" data-bs-toggle="tab" href="#settings">
                    <i class="bi bi-gear"></i> <span>Cài đặt</span>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>

        <div class="col-lg-9">
          <div class="content-area">
            <div class="tab-content">
              <div class="tab-pane fade show active" id="info">
                <h2 style="font-size:24px;font-weight:600;">Thông tin người quản trị</h2>
                <hr>
                <div class="admin-info-detail" style="margin:20px">
                  <p><strong>Username:</strong> <?= htmlspecialchars($admin['ten_dang_nhap']) ?></p>
                  <p><strong>Họ và tên:</strong> <?= htmlspecialchars($admin['ten_admin']) ?></p>
                  <p><strong>Quyền Hạn:</strong> <span class="role-tag">Admin</span></p>
                  <p><strong>Trạng Thái:</strong> <span class="status-active">Hoạt Động</span></p>
                </div>
              </div>

              <div class="tab-pane fade" id="settings">
                <div class="section-header" data-aos="fade-up">
                  <h2>Cài đặt tài khoản</h2>
                </div>
                <div class="settings-content">
                  <div class="settings-section" data-aos="fade-up">
                    <h3>Bảo mật</h3>
                    <form class="settings-form" method="POST" action="account.php">
                      <div class="row g-3">
                        <div class="col-md-12">
                          <label class="form-label">Mật khẩu hiện tại</label>
                          <input type="password" class="form-control" name="current_password">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Mật khẩu mới</label>
                          <input type="password" class="form-control" name="new_password">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Xác nhận mật khẩu mới</label>
                          <input type="password" class="form-control" name="confirm_password">
                        </div>
                      </div>
                      <div class="form-buttons mt-3">
                        <button type="submit" class="btn-save">Cập nhật mật khẩu</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once 'includes/footer.php'; ?>