<?php
$page_title = 'Kết quả tìm kiếm';
$body_class = 'search-results-page';
require_once 'includes/header.php';

$db = get_db();
$q = trim($_GET['q'] ?? '');
$ma_loai = intval($_GET['ma_loai'] ?? 0);
$gia_min = intval($_GET['gia_min'] ?? 0);
$gia_max = intval($_GET['gia_max'] ?? 0);

$per_page = 12;
$current_page = max(1, intval($_GET['page'] ?? 1));
$offset = ($current_page - 1) * $per_page;

// Xây dựng điều kiện tìm kiếm
$where = "sp.an_hien = 1";
$params = [];
$types = "";

if ($q) {
    $where .= " AND sp.ten_sp LIKE ?";
    $params[] = "%$q%";
    $types .= "s";
}
if ($ma_loai > 0) {
    $where .= " AND sp.ma_loai = ?";
    $params[] = $ma_loai;
    $types .= "i";
}
if ($gia_min > 0) {
    $where .= " AND ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) >= ?";
    $params[] = $gia_min;
    $types .= "i";
}
if ($gia_max > 0) {
    $where .= " AND ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) <= ?";
    $params[] = $gia_max;
    $types .= "i";
}

// Đếm tổng
$count_stmt = $db->prepare("SELECT COUNT(*) as total FROM san_pham sp WHERE $where");
if ($params) $count_stmt->bind_param($types, ...$params);
$count_stmt->execute();
$total = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $per_page);

// Lấy sản phẩm
$params[] = $per_page;
$params[] = $offset;
$types .= "ii";
$stmt = $db->prepare("SELECT sp.ma_sp, sp.ten_sp, sp.hinh_anh, sp.so_luong_ton, l.ten_loai,
                             ROUND(sp.gia_nhap_bq * (1 + sp.ty_le_loi_nhuan/100), 0) AS gia_ban
                      FROM san_pham sp
                      JOIN loai_sp l ON sp.ma_loai = l.ma_loai
                      WHERE $where ORDER BY sp.ma_sp DESC LIMIT ? OFFSET ?");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();

// Danh mục
$all_loai = $db->query("SELECT ma_loai, ten_loai FROM loai_sp WHERE an_hien = 1 ORDER BY ten_loai");
?>
<main class="main">
  <section id="search-results-header" class="search-results-header section" style="padding-bottom:0">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="search-results-header">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="results-count">
              <h2>Kết quả tìm kiếm</h2>
              <p>Tìm thấy <span class="results-number"><?= $total ?></span> kết quả
                <?php if ($q): ?> cho <span class="search-term">"<?= htmlspecialchars($q) ?>"</span><?php endif; ?>
              </p>
            </div>
          </div>
          <div class="col-lg-6">
            <form method="GET" action="search-results.php" class="search-form">
              <div class="input-group">
                <input type="text" class="form-control" name="q"
                       value="<?= htmlspecialchars($q) ?>" placeholder="Tìm kiếm..." required>
                <button class="btn search-btn" type="submit">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="container mt-4">
    <div class="row">
      <!-- Sidebar lọc nâng cao -->
      <div class="col-lg-4 sidebar">
        <div class="widgets-container">
          <form method="GET" action="search-results.php">
            <input type="hidden" name="q" value="<?= htmlspecialchars($q) ?>">

            <div class="product-categories-widget widget-item">
              <h3 class="widget-title">Phân loại</h3>
              <ul class="list-unstyled">
                <li class="mb-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="ma_loai" value="0"
                           id="loai_all" <?= $ma_loai == 0 ? 'checked' : '' ?>>
                    <label class="form-check-label" for="loai_all">Tất cả</label>
                  </div>
                </li>
                <?php while ($loai = $all_loai->fetch_assoc()): ?>
                <li class="mb-2">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="ma_loai"
                           value="<?= $loai['ma_loai'] ?>" id="loai_<?= $loai['ma_loai'] ?>"
                           <?= $ma_loai == $loai['ma_loai'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="loai_<?= $loai['ma_loai'] ?>">
                      <?= htmlspecialchars($loai['ten_loai']) ?>
                    </label>
                  </div>
                </li>
                <?php endwhile; ?>
              </ul>
            </div>

            <div class="pricing-range-widget widget-item">
              <h3 class="widget-title">Khoảng giá</h3>
              <div class="row g-2">
                <div class="col-6">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text">VND</span>
                    <input type="number" class="form-control" name="gia_min"
                           placeholder="Từ" value="<?= $gia_min ?: '' ?>">
                  </div>
                </div>
                <div class="col-6">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text">VND</span>
                    <input type="number" class="form-control" name="gia_max"
                           placeholder="Đến" value="<?= $gia_max ?: '' ?>">
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-sm btn-primary w-100 mt-3">Áp dụng bộ lọc</button>
              <a href="search-results.php<?= $q ? '?q='.urlencode($q) : '' ?>"
                 class="btn btn-sm btn-link w-100 mt-1">Xóa bộ lọc</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Danh sách kết quả -->
      <div class="col-lg-8">
        <section id="category-product-list" class="category-product-list section" style="padding-top:0">
          <div class="container">
            <div class="row">
              <?php if ($products && $products->num_rows > 0):
                while ($sp = $products->fetch_assoc()): ?>
              <div class="col-6 col-xl-4 mb-4">
                <div class="product-card" data-aos="zoom-in" data-aos-delay="500">
                  <div class="product-image">
                    <a href="product-details.php?ma_sp=<?= $sp['ma_sp'] ?>">
                      <?php if ($sp['hinh_anh']): ?>
                        <img src="<?= htmlspecialchars($sp['hinh_anh']) ?>"
                             alt="<?= htmlspecialchars($sp['ten_sp']) ?>"
                             style="width:100%;height:200px;object-fit:contain;">
                      <?php else: ?>
                        <div style="width:100%;height:200px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;">
                          <i class="bi bi-image" style="font-size:3rem;color:#ccc;"></i>
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
                <p class="text-muted mt-3">Không tìm thấy sản phẩm phù hợp.</p>
                <a href="all.php" class="btn btn-outline-primary mt-2">Xem tất cả sản phẩm</a>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </section>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
        <section class="category-pagination section">
          <div class="container">
            <nav class="d-flex justify-content-center">
              <ul>
                <?php if ($current_page > 1): ?>
                <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page - 1])) ?>">
                  <i class="bi bi-arrow-left"></i> <span class="d-none d-sm-inline">Trước</span>
                </a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                       class="<?= $i == $current_page ? 'active' : '' ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <?php if ($current_page < $total_pages): ?>
                <li><a href="?<?= http_build_query(array_merge($_GET, ['page' => $current_page + 1])) ?>">
                  <span class="d-none d-sm-inline">Sau</span> <i class="bi bi-arrow-right"></i>
                </a></li>
                <?php endif; ?>
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
