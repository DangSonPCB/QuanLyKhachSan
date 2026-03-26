<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý lương</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý lương</h1>
      <p class="sub">CRUD bảng `LUONG`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách lương + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách lương">
            <thead>
              <tr>
                <th>MaLuong</th>
                <th>MaNV</th>
                <th>TenNV</th>
                <th>ChucVu</th>
                <th>ThanhToan</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="wagesTbody">
              <tr><td colspan="6">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm lương</div>
          <form id="wageForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>MaLuong</span>
              <input id="MaLuong" required placeholder="VD: L11" />
            </label>

            <label>
              <span>MaNV</span>
              <select id="MaNVSelect" required></select>
            </label>

            <label>
              <span>ThanhToan</span>
              <input id="ThanhToan" type="number" required min="0" step="0.01" />
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
  <script src="assets/wages.js"></script>
</body>
</html>

