<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý dùng dịch vụ</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý dùng dịch vụ</h1>
      <p class="sub">CRUD bảng `SUDUNG_DV`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách dùng dịch vụ + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách dùng dịch vụ">
            <thead>
              <tr>
                <th>MaDV</th>
                <th>TenDV</th>
                <th>MaKH</th>
                <th>TenKH</th>
                <th>NgaySuDung</th>
                <th>SoLuong</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="usageTbody">
              <tr><td colspan="7">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm dùng dịch vụ</div>
          <form id="usageForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />
            <input type="hidden" id="keyMaDV" value="" />
            <input type="hidden" id="keyMaKH" value="" />
            <input type="hidden" id="keyNgaySuDung" value="" />

            <label>
              <span>MaDV</span>
              <select id="MaDVSelect" required></select>
            </label>

            <label>
              <span>MaKH</span>
              <select id="MaKHSelect" required></select>
            </label>

            <label>
              <span>NgaySuDung</span>
              <input id="NgaySuDung" type="date" required />
            </label>

            <label>
              <span>SoLuong</span>
              <input id="SoLuong" type="number" min="0" step="1" required />
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
  <script src="assets/usages.js"></script>
</body>
</html>

