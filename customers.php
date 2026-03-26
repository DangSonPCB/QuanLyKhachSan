<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý khách hàng</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý khách hàng</h1>
      <p class="sub">CRUD bảng `KHACHHANG`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách khách hàng + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách khách hàng">
            <thead>
              <tr>
                <th>MaKH</th>
                <th>TenKH</th>
                <th>SDTKH</th>
                <th>NgaySinhKH</th>
                <th>GioiTinh</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="customersTbody">
              <tr><td colspan="6">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm khách hàng</div>
          <form id="customerForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>MaKH</span>
              <input id="MaKH" name="MaKH" required placeholder="VD: KH11" />
            </label>
            <label>
              <span>TenKH</span>
              <input id="TenKH" name="TenKH" required placeholder="Tên khách" />
            </label>
            <label>
              <span>SDTKH</span>
              <input id="SDTKH" name="SDTKH" required placeholder="VD: 09xxxxxxxx" />
            </label>
            <label>
              <span>NgaySinhKH</span>
              <input id="NgaySinhKH" name="NgaySinhKH" type="date" required />
            </label>
            <label>
              <span>GioiTinh</span>
              <input id="GioiTinh" name="GioiTinh" required placeholder="Nam/Nu" />
            </label>
            <label>
              <span>CCCDKH</span>
              <input id="CCCDKH" name="CCCDKH" required placeholder="Số CCCD" />
            </label>
            <label>
              <span>emailKH</span>
              <input id="emailKH" name="emailKH" required placeholder="email@example.com" />
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
  <script src="assets/customers.js"></script>
</body>
</html>

