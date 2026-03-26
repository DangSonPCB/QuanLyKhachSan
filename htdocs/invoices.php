<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý hóa đơn</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>

  <div class="container">
    <header class="header">
      <h1>Quản lý hóa đơn</h1>
      <p class="sub">CRUD bảng `HOADON` và quản lý chi tiết `CHITIET_HD_DV`.</p>
    </header>

    <section class="card">
      <div class="card-title">Hóa đơn + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách hóa đơn">
            <thead>
              <tr>
                <th>MaHD</th>
                <th>NgayIn</th>
                <th>MaKH</th>
                <th>TenKH</th>
                <th>MaNV</th>
                <th>NV</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="invoicesTbody">
              <tr><td colspan="7">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="invoiceFormTitle">Thêm hóa đơn</div>
          <form id="invoiceForm" autocomplete="off">
            <input type="hidden" id="invoiceFormMode" value="create" />

            <label>
              <span>MaHD</span>
              <input id="MaHD" required placeholder="VD: HD11" />
            </label>

            <label>
              <span>MaNV</span>
              <select id="MaNVSelect" required></select>
            </label>

            <label>
              <span>MaKH</span>
              <select id="MaKHSelect" required></select>
            </label>

            <label>
              <span>NgayIn</span>
              <input id="NgayIn" type="date" required />
            </label>

            <div class="actions">
              <button type="submit" class="btn primary">Lưu</button>
              <button type="button" class="btn" id="invoiceResetBtn">Làm mới</button>
            </div>
            <div id="invoiceFormMsg" class="msg"></div>
          </form>
        </div>
      </div>
    </section>

    <section class="card">
      <div class="card-title">Chi tiết hóa đơn dịch vụ</div>
      <div class="split">
        <div class="pane">
          <div class="pane-title">Danh sách chi tiết</div>
          <table class="table" aria-label="Chi tiết hóa đơn">
            <thead>
              <tr>
                <th>MaDV</th>
                <th>TenDV</th>
                <th>SoLuong</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="invoiceDetailsTbody">
              <tr><td colspan="4">Chưa chọn hóa đơn</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="detailFormTitle">Thêm chi tiết</div>

          <form id="detailForm" autocomplete="off">
            <input type="hidden" id="detailFormMode" value="create" />
            <input type="hidden" id="detailKeyMaHD" value="" />
            <input type="hidden" id="detailKeyMaDV" value="" />

            <label>
              <span>MaHD</span>
              <select id="detailMaHDSelect" required></select>
            </label>

            <label>
              <span>MaDV</span>
              <select id="detailMaDVSelect" required></select>
            </label>

            <label>
              <span>SoLuong</span>
              <input id="detailSoLuong" type="number" min="0" step="1" required />
            </label>

            <div class="actions">
              <button type="submit" class="btn primary">Lưu</button>
              <button type="button" class="btn" id="detailResetBtn">Làm mới</button>
            </div>
            <div id="detailFormMsg" class="msg"></div>
          </form>
        </div>
      </div>
    </section>
  </div>

  <script src="assets/common.js"></script>
  <script src="assets/invoices.js"></script>
</body>
</html>

