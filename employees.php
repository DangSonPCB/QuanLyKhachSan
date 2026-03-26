<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý nhân viên</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý nhân viên</h1>
      <p class="sub">CRUD bảng `NHANVIEN`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách nhân viên + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách nhân viên">
            <thead>
              <tr>
                <th>MaNV</th>
                <th>TenNV</th>
                <th>NgaySinhNV</th>
                <th>ChucVu</th>
                <th>SDTNV</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="employeesTbody">
              <tr><td colspan="6">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm nhân viên</div>
          <form id="employeeForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>MaNV</span>
              <input id="MaNV" name="MaNV" required placeholder="VD: NV11" />
            </label>
            <label>
              <span>TenNV</span>
              <input id="TenNV" name="TenNV" required placeholder="Tên nhân viên" />
            </label>
            <label>
              <span>NgaySinhNV</span>
              <input id="NgaySinhNV" name="NgaySinhNV" type="date" required />
            </label>
            <label>
              <span>ChucVu</span>
              <input id="ChucVu" name="ChucVu" required placeholder="LeTan/ThuNgan/BaoVe/QuanLy..." />
            </label>
            <label>
              <span>SDTNV</span>
              <input id="SDTNV" name="SDTNV" required placeholder="09xxxxxxxx" />
            </label>
            <label>
              <span>CCCDNV</span>
              <input id="CCCDNV" name="CCCDNV" required placeholder="Số CCCD" />
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
  <script src="assets/employees.js"></script>
</body>
</html>

