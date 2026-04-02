<?php
$page_title = 'Tất cả sản phẩm';
$body_class = 'page-all';
require_once 'includes/header.php';

// Lấy tham số lọc
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 9;
$offset = ($page - 1) * $per_page;

$ma_loai = isset($_GET['ma_loai']) ? (int)$_GET['ma_loai'] : 0;
$gia_min = isset($_GET['gia_min']) ? (int)$_GET['gia_min'] : 0;
$gia_max = isset($_GET['gia_max']) ? (int)$_GET['gia_max'] : 100000000;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'moi_nhat';

// Xây dựng điều kiện WHERE
$where = "WHERE sp.an_hien = 1 AND sp.so_luong_ton > 0";
$params = [];
$types = '';

if ($ma_loai > 0) {
    $where .= " AND sp.ma_loai = ?";
    $params[] = $ma_loai;
    $types .= 'i';
}

// Lọc theo giá bán
$where .= " AND ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) BETWEEN ? AND ?";
$params[] = $gia_min;
$params[] = $gia_max;
$types .= 'ii';

// Sắp xếp
$order = match($sort) {
    'gia_tang' => "ORDER BY gia_ban ASC",
    'gia_giam' => "ORDER BY gia_ban DESC",
    'ten_az' => "ORDER BY sp.ten_sp ASC",
    default => "ORDER BY sp.ngay_tao DESC"
};

// Đếm tổng
$sql_count = "SELECT COUNT(*) as total
              FROM san_pham sp
              JOIN loai_sp l ON sp.ma_loai = l.ma_loai
              $where";
$stmt = $db->prepare($sql_count);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Lấy sản phẩm
$sql = "SELECT sp.ma_sp, sp.ten_sp, sp.hinh_anh, sp.so_luong_ton, l.ten_loai,
               ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) AS gia_ban
        FROM san_pham sp
        JOIN loai_sp l ON sp.ma_loai = l.ma_loai
        $where
        $order
        LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;
$types .= 'ii';
$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

// Lấy danh sách loại để sidebar
$loai_sidebar = $db->query("SELECT ma_loai, ten_loai FROM loai_sp WHERE an_hien = 1 ORDER BY ten_loai");
?>

<main class="main">
  <div class="page-title light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
      <h1 class="mb-2 mb-lg-0">Tất cả sản phẩm</h1>
      <nav class="breadcrumbs">
        <ol>
          <li><a href="index.php">Trang chủ</a></li>
          <li class="current">Tất cả</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <!-- SIDEBAR -->
      <div class="col-lg-4 sidebar">
        <div class="widgets-container">
          <!-- Lọc theo loại -->
          <div class="product-categories-widget widget-item">
            <h3 class="widget-title">Phân loại</h3>
            <ul class="category-tree list-unstyled mb-0">
              <?php while ($loai = $loai_sidebar->fetch_assoc()): ?>
              <li class="category-item">
                <div class="d-flex justify-content-between align-items-center category-header">
                  <a href="?ma_loai=<?= $loai['ma_loai'] ?><?= $gia_min ? '&gia_min='.$gia_min : '' ?><?= $gia_max < 100000000 ? '&gia_max='.$gia_max : '' ?>"
                     class="category-link <?= $ma_loai == $loai['ma_loai'] ? 'fw-bold text-danger' : '' ?>">
                    <?= htmlspecialchars($loai['ten_loai']) ?>
                  </a>
                </div>
              </li>
              <?php endwhile; ?>
              <?php if ($ma_loai > 0): ?>
              <li class="category-item mt-2">
                <a href="all.php" class="text-muted small">✕ Bỏ lọc loại</a>
              </li>
              <?php endif; ?>
            </ul>
          </div>

          <!-- Lọc giá -->
          <div class="pricing-range-widget widget-item">
            <h3 class="widget-title">Khoảng giá</h3>
            <form method="GET" action="all.php" id="price-filter-form">
              <?php if ($ma_loai > 0): ?><input type="hidden" name="ma_loai" value="<?= $ma_loai ?>"><?php endif; ?>
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

          <!-- Sắp xếp -->
          <div class="brand-filter-widget widget-item">
            <h3 class="widget-title">Sắp xếp</h3>
            <div class="brand-filter-content">
              <div class="brand-list">
                <?php
                $sorts = ['moi_nhat' => 'Mới nhất', 'gia_tang' => 'Giá tăng dần', 'gia_giam' => 'Giá giảm dần', 'ten_az' => 'Tên A-Z'];
                foreach ($sorts as $k => $v):
                  $url = "all.php?sort=$k" . ($ma_loai ? "&ma_loai=$ma_loai" : '') . ($gia_min ? "&gia_min=$gia_min" : '') . ($gia_max < 100000000 ? "&gia_max=$gia_max" : '');
                ?>
                <div class="brand-item">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" id="sort_<?= $k ?>" <?= $sort == $k ? 'checked' : '' ?>
                           onchange="window.location='<?= $url ?>'">
                    <label class="form-check-label" for="sort_<?= $k ?>"><?= $v ?></label>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- DANH SÁCH SẢN PHẨM -->
      <div class="col-lg-8">
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
                             alt="<?= htmlspecialchars($sp['ten_sp']) ?>"
                             style="width:100%;height:220px;object-fit:cover;" />
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
                <p class="text-muted">Không tìm thấy sản phẩm phù hợp.</p>
                <a href="all.php" class="btn btn-primary mt-2">Xem tất cả sản phẩm</a>
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
                  <a href="?page=<?= max(1, $page-1) ?>&ma_loai=<?= $ma_loai ?>&gia_min=<?= $gia_min ?>&gia_max=<?= $gia_max ?>&sort=<?= $sort ?>"
                     <?= $page <= 1 ? 'style="opacity:.4;pointer-events:none"' : '' ?>>
                    <i class="bi bi-arrow-left"></i>
                    <span class="d-none d-sm-inline">Trước</span>
                  </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li>
                  <a href="?page=<?= $i ?>&ma_loai=<?= $ma_loai ?>&gia_min=<?= $gia_min ?>&gia_max=<?= $gia_max ?>&sort=<?= $sort ?>"
                     <?= $i == $page ? 'class="active"' : '' ?>>
                    <?= $i ?>
                  </a>
                </li>
                <?php endfor; ?>
                <li>
                  <a href="?page=<?= min($total_pages, $page+1) ?>&ma_loai=<?= $ma_loai ?>&gia_min=<?= $gia_min ?>&gia_max=<?= $gia_max ?>&sort=<?= $sort ?>"
                     <?= $page >= $total_pages ? 'style="opacity:.4;pointer-events:none"' : '' ?>>
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