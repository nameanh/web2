<?php
$page_title = 'Phân loại sản phẩm';
$body_class = 'page-all';
require_once 'includes/header.php';

// Lấy ma_loai từ URL (dùng chung với brand.php nhưng hiển thị theo danh mục)
$ma_loai = isset($_GET['ma_loai']) ? (int)$_GET['ma_loai'] : 0;
$page    = isset($_GET['page'])    ? max(1, (int)$_GET['page']) : 1;
$per_page = 9;
$offset   = ($page - 1) * $per_page;

$gia_min = isset($_GET['gia_min']) ? (int)$_GET['gia_min'] : 0;
$gia_max = isset($_GET['gia_max']) ? (int)$_GET['gia_max'] : 100000000;
$sort    = isset($_GET['sort'])    ? $_GET['sort']           : 'moi_nhat';

// Tên loại hiện tại
$ten_loai = 'Tất cả';
if ($ma_loai > 0) {
    $stmt_loai = $db->prepare("SELECT ten_loai FROM loai_sp WHERE ma_loai = ?");
    $stmt_loai->bind_param('i', $ma_loai);
    $stmt_loai->execute();
    $row_loai = $stmt_loai->get_result()->fetch_assoc();
    if ($row_loai) $ten_loai = $row_loai['ten_loai'];
}
$page_title = 'Phân loại: ' . $ten_loai;

// WHERE
$where  = "WHERE sp.an_hien = 1 AND sp.so_luong_ton > 0";
$params = [];
$types  = '';

if ($ma_loai > 0) {
    $where  .= " AND sp.ma_loai = ?";
    $params[] = $ma_loai;
    $types   .= 'i';
}
$where  .= " AND ROUND(sp.gia_nhap_bq*(1+sp.ty_le_loi_nhuan/100),0) BETWEEN ? AND ?";
$params[] = $gia_min;
$params[] = $gia_max;
$types   .= 'ii';

$order = match($sort) {
    'gia_tang' => "ORDER BY gia_ban ASC",
    'gia_giam' => "ORDER BY gia_ban DESC",
    'ten_az'   => "ORDER BY sp.ten_sp ASC",
    default    => "ORDER BY sp.ngay_tao DESC"
};

// Đếm
$stmt_count = $db->prepare("SELECT COUNT(*) as total
    FROM san_pham sp JOIN loai_sp l ON sp.ma_loai=l.ma_loai $where");
if ($params) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total       = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Sản phẩm
$sql = "SELECT sp.ma_sp, sp.ten_sp, sp.hinh_anh, l.ten_loai,
               ROUND(sp.gia_nhap_bq*(1+sp.ty_le_loi_nhuan/100),0) AS gia_ban
        FROM san_pham sp JOIN loai_sp l ON sp.ma_loai=l.ma_loai
        $where $order LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types   .= 'ii';
$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

// Sidebar loại
$loai_sidebar = $db->query("SELECT ma_loai, ten_loai FROM loai_sp WHERE an_hien=1 ORDER BY ten_loai");
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Phân loại: <?= htmlspecialchars($ten_loai) ?></h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current"><?= htmlspecialchars($ten_loai) ?></li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <!-- SIDEBAR -->
      <div class="col-lg-4 sidebar">
        <div class="widgets-container">

          <!-- Lọc khoảng giá -->
          <div class="pricing-range-widget widget-item">
            <h3 class="widget-title">Khoảng giá</h3>
            <form method="GET" action="category_detail.php">
              <?php if ($ma_loai): ?><input type="hidden" name="ma_loai" value="<?= $ma_loai ?>"><?php endif; ?>
              <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
              <div class="price-range-container">
                <div class="current-range mb-3">
                  <span class="min-price"><?= number_format($gia_min, 0, ',', '.') ?> VND</span>
                  <span class="max-price float-end"><?= number_format($gia_max, 0, ',', '.') ?> VND</span>
                </div>
                <div class="price-inputs mt-3">
                  <div class="row g-2">
                    <div class="col-6">
                      <div class="input-group input-group-sm">
                        <span class="input-group-text">VND</span>
                        <input type="number" name="gia_min" class="form-control" placeholder="Min" value="<?= $gia_min ?>">
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="input-group input-group-sm">
                        <span class="input-group-text">VND</span>
                        <input type="number" name="gia_max" class="form-control" placeholder="Max" value="<?= $gia_max < 100000000 ? $gia_max : '' ?>">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="filter-actions mt-3">
                  <button type="submit" class="btn btn-sm btn-primary w-100">Áp dụng bộ lọc</button>
                </div>
              </div>
            </form>
          </div>

          <!-- Lọc theo loại -->
          <div class="brand-filter-widget widget-item">
            <h3 class="widget-title">Lọc theo loại</h3>
            <div class="brand-filter-content">
              <div class="brand-list">
                <?php while ($loai = $loai_sidebar->fetch_assoc()): ?>
                <div class="brand-item">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="loai_<?= $loai['ma_loai'] ?>"
                           <?= $ma_loai == $loai['ma_loai'] ? 'checked' : '' ?>
                           onchange="window.location='category_detail.php?ma_loai=<?= $loai['ma_loai'] ?>&sort=<?= $sort ?>'">
                    <label class="form-check-label" for="loai_<?= $loai['ma_loai'] ?>">
                      <?= htmlspecialchars($loai['ten_loai']) ?>
                    </label>
                  </div>
                </div>
                <?php endwhile; ?>
              </div>
              <?php if ($ma_loai): ?>
              <div class="brand-actions mt-2">
                <a href="category_detail.php" class="btn btn-sm btn-link">Xóa bộ lọc</a>
              </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>

      <!-- DANH SÁCH SẢN PHẨM -->
      <div class="col-lg-8">
        <section id="category-header" class="category-header section">
          <div class="container" data-aos="fade-up"></div>
        </section>

        <section id="category-product-list" class="category-product-list section">
          <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row">
              <?php if ($products->num_rows > 0):
                while ($sp = $products->fetch_assoc()): ?>
              <div class="col-6 col-xl-4">
                <div class="product-card" data-aos="zoom-in" data-aos-delay="0">
                  <div class="product-image">
                    <a href="product-details.php?ma_sp=<?= $sp['ma_sp'] ?>">
                      <?php if ($sp['hinh_anh']): ?>
                        <img src="<?= htmlspecialchars($sp['hinh_anh']) ?>"
                             class="main-image img-fluid"
                             alt="<?= htmlspecialchars($sp['ten_sp']) ?>">
                        <img src="<?= htmlspecialchars($sp['hinh_anh']) ?>"
                             class="hover-image img-fluid"
                             alt="<?= htmlspecialchars($sp['ten_sp']) ?>">
                      <?php else: ?>
                        <div style="width:100%;height:220px;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                          <i class="bi bi-phone" style="font-size:3rem;color:#ccc;"></i>
                        </div>
                      <?php endif; ?>
                    </a>
                  </div>
                  <div class="product-details">
                    <div class="product-category"><?= htmlspecialchars($sp['ten_loai']) ?></div>
                    <h4 class="product-title">
                      <a href="product-details.php?ma_sp=<?= $sp['ma_sp'] ?>">
                        <?= htmlspecialchars($sp['ten_sp']) ?>
                      </a>
                    </h4>
                    <div class="product-meta">
                      <div class="product-price">
                        <?= number_format($sp['gia_ban'], 0, ',', '.') ?> VND
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php endwhile;
              else: ?>
              <div class="col-12 text-center py-5">
                <i class="bi bi-search" style="font-size:3rem;color:#ccc;"></i>
                <p class="mt-3 text-muted">Không tìm thấy sản phẩm phù hợp.</p>
                <a href="category_detail.php" class="btn btn-primary mt-2">Xem tất cả</a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </section>

        <!-- PHÂN TRANG -->
        <?php if ($total_pages > 1): ?>
        <section id="category-pagination" class="category-pagination section">
          <div class="container">
            <nav class="d-flex justify-content-center" aria-label="Page navigation">
              <ul>
                <li>
                  <a href="?page=<?= max(1,$page-1) ?>&ma_loai=<?= $ma_loai ?>&gia_min=<?= $gia_min ?>&gia_max=<?= $gia_max ?>&sort=<?= $sort ?>"
                     <?= $page<=1 ? 'style="opacity:.4;pointer-events:none"' : '' ?>>
                    <i class="bi bi-arrow-left"></i>
                    <span class="d-none d-sm-inline">Trước</span>
                  </a>
                </li>
                <?php for ($i=1; $i<=$total_pages; $i++): ?>
                <li>
                  <a href="?page=<?= $i ?>&ma_loai=<?= $ma_loai ?>&gia_min=<?= $gia_min ?>&gia_max=<?= $gia_max ?>&sort=<?= $sort ?>"
                     <?= $i==$page ? 'class="active"' : '' ?>>
                    <?= $i ?>
                  </a>
                </li>
                <?php endfor; ?>
                <li>
                  <a href="?page=<?= min($total_pages,$page+1) ?>&ma_loai=<?= $ma_loai ?>&gia_min=<?= $gia_min ?>&gia_max=<?= $gia_max ?>&sort=<?= $sort ?>"
                     <?= $page>=$total_pages ? 'style="opacity:.4;pointer-events:none"' : '' ?>>
                    <span class="d-none d-sm-inline">Sau</span>
                    <i class="bi bi-arrow-right"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </section>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>

<?php require_once 'includes/footer.php'; ?>