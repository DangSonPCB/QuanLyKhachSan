<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý phân công</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý phân công</h1>
      <p class="sub">CRUD bảng `PHANCONG_NV`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách phân công + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách phân công">
            <thead>
              <tr>
                <th>MaNV</th>
                <th>TenNV</th>
                <th>SoPhong</th>
                <th>Loai</th>
                <th>CaLamViec</th>
                <th>NgayPhanCong</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="schedulesTbody">
              <tr><td colspan="7">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm phân công</div>
          <form id="scheduleForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />
            <input type="hidden" id="keyMaNV" value="" />
            <input type="hidden" id="keySoPhong" value="" />
            <input type="hidden" id="keyNgayPhanCong" value="" />

            <label>
              <span>MaNV</span>
              <select id="MaNVSelect" required></select>
            </label>

            <label>
              <span>SoPhong</span>
              <select id="SoPhongSelect" required></select>
            </label>

            <label>
              <span>CaLamViec</span>
              <input id="CaLamViec" required placeholder="Sang/Chieu/Dem" />
            </label>

            <label>
              <span>NgayPhanCong</span>
              <input id="NgayPhanCong" type="date" required />
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
  <script src="assets/schedules.js"></script>
</body>
</html>

