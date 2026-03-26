<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý dịch vụ khách sạn</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
          <?php require_once __DIR__ . '/includes/nav.php'; ?>

  <div class="container">
    <header class="header">
      <h1>Quản lý dịch vụ khách sạn</h1>
      <p class="sub">Dữ liệu lấy từ CSDL `QuanLyKhachSan`: bảng `DICHVU`, `SUDUNG_DV`, `CHITIET_HD_DV`.</p>
    </header>

    <section class="card">
      <div class="card-title">1) Danh sách dịch vụ + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách dịch vụ">
            <thead>
              <tr>
                <th>MaDV</th>
                <th>TenDV</th>
                <th>GiaDV</th>
                <th>BatDau</th>
                <th>KetThuc</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="servicesTbody">
              <tr><td colspan="6">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm dịch vụ</div>
          <form id="serviceForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>MaDV</span>
              <input id="MaDV" name="MaDV" required placeholder="VD: DV11" />
            </label>
            <label>
              <span>TenDV</span>
              <input id="TenDV" name="TenDV" required placeholder="VD: Sauna" />
            </label>
            <label>
              <span>GiaDV</span>
              <input id="GiaDV" name="GiaDV" type="number" required min="0" step="0.01" />
            </label>
            <label>
              <span>BatDau</span>
              <input id="BatDau" name="BatDau" type="time" required step="1" />
            </label>
            <label>
              <span>KetThuc</span>
              <input id="KetThuc" name="KetThuc" type="time" required step="1" />
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

    <section class="card">
      <div class="card-title">2) Tra cứu: Khách dùng dịch vụ & Dịch vụ trong hóa đơn</div>

      <div class="row">
        <label class="select-label">
          <span>Chọn dịch vụ</span>
          <select id="serviceSelect"></select>
        </label>
        <button class="btn" id="reloadBtn" type="button">Tải lại</button>
      </div>

      <div class="split">
        <div class="pane">
          <div class="pane-title">Khách đã sử dụng</div>
          <table class="table" aria-label="Khách sử dụng dịch vụ">
            <thead>
              <tr>
                <th>MaKH</th>
                <th>TenKH</th>
                <th>NgaySuDung</th>
                <th>SoLuong</th>
              </tr>
            </thead>
            <tbody id="usageTbody">
              <tr><td colspan="4">Chưa chọn dịch vụ</td></tr>
            </tbody>
          </table>
        </div>

        <div class="pane">
          <div class="pane-title">Dịch vụ trong hóa đơn</div>
          <table class="table" aria-label="Dịch vụ trong hóa đơn">
            <thead>
              <tr>
                <th>MaHD</th>
                <th>NgayIn</th>
                <th>TenKH</th>
                <th>NV</th>
                <th>SoLuong</th>
              </tr>
            </thead>
            <tbody id="billsTbody">
              <tr><td colspan="5">Chưa chọn dịch vụ</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>

  <script src="assets/app.js"></script>
</body>
</html>

