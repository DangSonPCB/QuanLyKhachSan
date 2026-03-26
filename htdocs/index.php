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
      <h1>Bảng điều khiển quản lý</h1>
      <p class="sub">Chọn module bên dưới để quản lý toàn bộ hệ thống.</p>
    </header>

    <section class="card">
      <div class="card-title">Chọn chức năng</div>
      <div
        style="
          display:grid;
          grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
          gap: 12px;
          align-items: stretch;
        "
      >
        <a class="btn primary" href="service.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Dịch vụ (`DICHVU`)
        </a>
        <a class="btn" href="rooms.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Phòng (`PHONG`)
        </a>
        <a class="btn" href="customers.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Khách hàng (`KHACHHANG`)
        </a>
        <a class="btn" href="employees.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Nhân viên (`NHANVIEN`)
        </a>
        <a class="btn" href="bookings.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Đặt phòng (`DATPHONG`)
        </a>
        <a class="btn" href="invoices.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Hóa đơn (`HOADON`, `CHITIET_HD_DV`)
        </a>
        <a class="btn" href="usage.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Dùng dịch vụ (`SUDUNG_DV`)
        </a>
        <a class="btn" href="reviews.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Đánh giá (`DANHGIA`)
        </a>
        <a class="btn" href="wages.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Lương (`LUONG`)
        </a>
        <a class="btn" href="schedules.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Phân công (`PHANCONG_NV`)
        </a>
        <a class="btn" href="accounts.php" style="text-decoration:none; display:flex; justify-content:center; align-items:center;">
          Tài khoản (`TAIKHOAN`)
        </a>
      </div>

      <p class="sub" style="margin-top: 12px;">
        Lưu ý: Nếu bạn muốn trang chủ không tự nhảy lại “Dịch vụ”, hãy đổi link đầu tiên thành trang riêng `services.php`.
      </p>
    </section>
  </div>
</body>
</html>
