<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý tài khoản</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>

  <div class="container">
    <header class="header">
      <h1>Quản lý tài khoản</h1>
      <p class="sub">CRUD bảng `TAIKHOAN` (và có nút login demo).</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách tài khoản + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách tài khoản">
            <thead>
              <tr>
                <th>ID</th>
                <th>TenDangNhap</th>
                <th>TrangThai</th>
                <th>Thuộc NV</th>
                <th>Thuộc KH</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="accountsTbody">
              <tr><td colspan="6">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm tài khoản</div>
          <form id="accountForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>ID</span>
              <input id="ID" required placeholder="VD: ID21" />
            </label>

            <label>
              <span>TenDangNhap</span>
              <input id="TenDangNhap" required placeholder="Tên đăng nhập" />
            </label>

            <label>
              <span>MatKhau</span>
              <input id="MatKhau" required placeholder="Mật khẩu" />
            </label>

            <label>
              <span>TrangThai</span>
              <input id="TrangThai" required placeholder="HoatDong/KhongHoatDong" />
            </label>

            <label>
              <span>MaNV (nếu là nhân viên)</span>
              <select id="MaNVSelect">
                <option value="">(NULL)</option>
              </select>
            </label>

            <label>
              <span>MaKH (nếu là khách hàng)</span>
              <select id="MaKHSelect">
                <option value="">(NULL)</option>
              </select>
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
      <div class="card-title">Login demo</div>
      <div class="grid" style="grid-template-columns: 1fr 0.8fr;">
        <div>
          <p class="sub">Chỉ demo login; website hiện tại không chặn trang theo quyền.</p>
          <div class="msg" id="loginMsg"></div>
        </div>
        <div class="form-wrap">
          <form id="loginForm" autocomplete="off">
            <label>
              <span>TenDangNhap</span>
              <input id="loginTen" required />
            </label>
            <label>
              <span>MatKhau</span>
              <input id="loginPass" type="password" required />
            </label>
            <div class="actions">
              <button type="submit" class="btn primary">Đăng nhập</button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>

  <script src="assets/common.js"></script>
  <script src="assets/accounts.js"></script>
</body>
</html>

