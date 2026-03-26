<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý đặt phòng</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý đặt phòng</h1>
      <p class="sub">CRUD bảng `DATPHONG`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách đặt phòng + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách đặt phòng">
            <thead>
              <tr>
                <th>MaDatPhong</th>
                <th>SoPhong</th>
                <th>Loai</th>
                <th>MaKH</th>
                <th>TenKH</th>
                <th>NgayNhan</th>
                <th>NgayTra</th>
                <th>SoKhanhhang</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="bookingsTbody">
              <tr><td colspan="9">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm đặt phòng</div>
          <form id="bookingForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>MaDatPhong</span>
              <input id="MaDatPhong" name="MaDatPhong" required placeholder="VD: DP11" />
            </label>

            <label>
              <span>SoPhong</span>
              <select id="SoPhongSelect" required></select>
            </label>

            <label>
              <span>MaKH</span>
              <select id="MaKHSelect" required></select>
            </label>

            <label>
              <span>NgayNhan</span>
              <input id="NgayNhan" name="NgayNhan" type="date" required />
            </label>
            <label>
              <span>NgayTra</span>
              <input id="NgayTra" name="NgayTra" type="date" required />
            </label>
            <label>
              <span>SoKhanhhang</span>
              <input id="SoKhanhhang" name="SoKhanhhang" type="number" required min="1" step="1" />
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
  <script src="assets/bookings.js"></script>
</body>
</html>

