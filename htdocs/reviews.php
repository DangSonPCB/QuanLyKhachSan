<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý đánh giá</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý đánh giá</h1>
      <p class="sub">CRUD bảng `DANHGIA`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách đánh giá + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách đánh giá">
            <thead>
              <tr>
                <th>MaDG</th>
                <th>MaKH</th>
                <th>TenKH</th>
                <th>DiemDG</th>
                <th>NgayDG</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="reviewsTbody">
              <tr><td colspan="6">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm đánh giá</div>
          <form id="reviewForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>MaDG</span>
              <input id="MaDG" required placeholder="VD: DG11" />
            </label>

            <label>
              <span>MaKH</span>
              <select id="MaKHSelect" required></select>
            </label>

            <label>
              <span>DiemDG</span>
              <input id="DiemDG" type="number" required min="0" step="1" />
            </label>

            <label>
              <span>NoiDungDG</span>
              <textarea id="NoiDungDG" required rows="4" style="resize:vertical;"></textarea>
            </label>

            <label>
              <span>NgayDG</span>
              <input id="NgayDG" type="date" required />
            </label>

            <div class="actions">
              <button type="submit" class="btn primary">Lưu</button>
              <button type="button" class="btn" id="resetBtn">Làm mới</button>
            </div>
            <div id="formMsg" class="msg"></div>
          </form>
        </div>
      </div>
    </section>
  </div>

  <script src="assets/common.js"></script>
  <script src="assets/reviews.js"></script>
</body>
</html>

