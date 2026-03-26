<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Quản lý phòng</title>
  <link rel="stylesheet" href="assets/styles.css" />
</head>
<body>
  <?php require_once __DIR__ . '/includes/nav.php'; ?>
  <div class="container">
    <header class="header">
      <h1>Quản lý phòng</h1>
      <p class="sub">CRUD bảng `PHONG`.</p>
    </header>

    <section class="card">
      <div class="card-title">Danh sách phòng + CRUD</div>
      <div class="grid">
        <div>
          <table class="table" aria-label="Danh sách phòng">
            <thead>
              <tr>
                <th>SoPhong</th>
                <th>Loai</th>
                <th>GiaThue</th>
                <th>TrangThaiThue</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="roomsTbody">
              <tr><td colspan="5">Đang tải...</td></tr>
            </tbody>
          </table>
        </div>

        <div class="form-wrap">
          <div class="form-title" id="formTitle">Thêm phòng</div>
          <form id="roomForm" autocomplete="off">
            <input type="hidden" id="formMode" value="create" />

            <label>
              <span>SoPhong</span>
              <input id="SoPhong" name="SoPhong" required placeholder="VD: P101" />
            </label>
            <label>
              <span>Loai</span>
              <input id="Loai" name="Loai" required placeholder="Standard/Deluxe/VIP" />
            </label>
            <label>
              <span>GiaThue</span>
              <input id="GiaThue" name="GiaThue" type="number" required min="0" step="0.01" />
            </label>
            <label>
              <span>TrangThaiThue</span>
              <input id="TrangThaiThue" name="TrangThaiThue" required placeholder="Trong/DangThue" />
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
  <script src="assets/rooms.js"></script>
</body>
</html>

