<?php
declare(strict_types=1);
?>
<nav class="nav">
  <div class="nav-inner">
    <a class="nav-brand" href="index.php">Quản lý</a>
    <div class="nav-links">
      <a href="index.php" class="nav-link">Trang chủ</a>
      <a href="service.php" class="nav-link">Dịch vụ</a>
      <a href="rooms.php" class="nav-link">Phòng</a>
      <a href="customers.php" class="nav-link">Khách hàng</a>
      <a href="employees.php" class="nav-link">Nhân viên</a>
      <a href="bookings.php" class="nav-link">Đặt phòng</a>
      <a href="invoices.php" class="nav-link">Hóa đơn</a>
      <a href="usage.php" class="nav-link">Dùng dịch vụ</a>
      <a href="reviews.php" class="nav-link">Đánh giá</a>
      <a href="wages.php" class="nav-link">Lương</a>
      <a href="schedules.php" class="nav-link">Phân công</a>
      <a href="accounts.php" class="nav-link">Tài khoản</a>
    </div>
  </div>
</nav>
<style>
/* Reset nhẹ */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', sans-serif;
}

/* Navbar */
.nav {
  position: sticky;
  top: 0;
  z-index: 1000;
  background: linear-gradient(90deg, #1e293b, #0f172a);
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

/* Container */
.nav-inner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 30px;
}

/* Logo */
.nav-brand {
  color: #38bdf8;
  font-size: 22px;
  font-weight: bold;
  text-decoration: none;
  letter-spacing: 1px;
}

/* Links */
.nav-links {
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
}

/* Link item */
.nav-link {
  color: #e2e8f0;
  text-decoration: none;
  font-size: 14px;
  padding: 6px 10px;
  border-radius: 6px;
  transition: all 0.3s ease;
}

/* Hover effect */
.nav-link:hover {
  background: #38bdf8;
  color: #0f172a;
  transform: translateY(-2px);
}

/* Active (tự set class active nếu cần) */
.nav-link.active {
  background: #38bdf8;
  color: #0f172a;
}

/* Responsive */
@media (max-width: 768px) {
  .nav-inner {
    flex-direction: column;
    align-items: flex-start;
  }

  .nav-links {
    margin-top: 10px;
    flex-direction: column;
    width: 100%;
  }

  .nav-link {
    width: 100%;
  }
}
</style>
