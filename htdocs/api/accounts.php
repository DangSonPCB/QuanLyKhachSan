<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

require_once __DIR__ . '/../config/db.php';

function jsonInput(): array
{
  $raw = file_get_contents('php://input');
  if (!$raw) return [];
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function respond(int $code, array $payload): void
{
  http_response_code($code);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE);
  exit;
}

$data = jsonInput();
$action = $_GET['action'] ?? ($_POST['action'] ?? $data['action'] ?? '');
if (!$action) respond(400, ['ok' => false, 'error' => 'Missing action']);

try {
  $pdo = db();

  switch ($action) {
    case 'list': {
      $stmt = $pdo->query('
        SELECT t.ID, t.TenDangNhap, t.TrangThai,
               t.MaNV, nv.TenNV,
               t.MaKH, kh.TenKH
        FROM TAIKHOAN t
        LEFT JOIN NHANVIEN nv ON nv.MaNV = t.MaNV
        LEFT JOIN KHACHHANG kh ON kh.MaKH = t.MaKH
        ORDER BY t.ID
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownEmployees': {
      $stmt = $pdo->query('SELECT MaNV, TenNV FROM NHANVIEN ORDER BY MaNV');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownCustomers': {
      $stmt = $pdo->query('SELECT MaKH, TenKH FROM KHACHHANG ORDER BY MaKH');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      session_start();

      $ID = trim((string)($data['ID'] ?? ''));
      $TenDangNhap = trim((string)($data['TenDangNhap'] ?? ''));
      $MatKhau = trim((string)($data['MatKhau'] ?? ''));
      $TrangThai = trim((string)($data['TrangThai'] ?? ''));

      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));

      $MaNV = $MaNV === '' ? null : $MaNV;
      $MaKH = $MaKH === '' ? null : $MaKH;

      if ($ID === '' || $TenDangNhap === '' || $MatKhau === '' || $TrangThai === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }
      if (($MaNV === null && $MaKH === null) || ($MaNV !== null && $MaKH !== null)) {
        respond(400, ['ok' => false, 'error' => 'Chỉ chọn 1 trong MaNV hoặc MaKH']);
      }

      $stmt = $pdo->prepare('
        INSERT INTO TAIKHOAN (ID, MaNV, MaKH, TenDangNhap, MatKhau, TrangThai)
        VALUES (:ID, :MaNV, :MaKH, :TenDangNhap, :MatKhau, :TrangThai)
      ');
      $stmt->execute([
        ':ID' => $ID,
        ':MaNV' => $MaNV,
        ':MaKH' => $MaKH,
        ':TenDangNhap' => $TenDangNhap,
        ':MatKhau' => $MatKhau,
        ':TrangThai' => $TrangThai,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $ID = trim((string)($data['ID'] ?? ''));
      $TenDangNhap = trim((string)($data['TenDangNhap'] ?? ''));
      $MatKhau = trim((string)($data['MatKhau'] ?? ''));
      $TrangThai = trim((string)($data['TrangThai'] ?? ''));

      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));

      $MaNV = $MaNV === '' ? null : $MaNV;
      $MaKH = $MaKH === '' ? null : $MaKH;

      if ($ID === '' || $TenDangNhap === '' || $MatKhau === '' || $TrangThai === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }
      if (($MaNV === null && $MaKH === null) || ($MaNV !== null && $MaKH !== null)) {
        respond(400, ['ok' => false, 'error' => 'Chỉ chọn 1 trong MaNV hoặc MaKH']);
      }

      $stmt = $pdo->prepare('
        UPDATE TAIKHOAN
        SET MaNV=:MaNV, MaKH=:MaKH, TenDangNhap=:TenDangNhap, MatKhau=:MatKhau, TrangThai=:TrangThai
        WHERE ID=:ID
      ');
      $stmt->execute([
        ':ID' => $ID,
        ':MaNV' => $MaNV,
        ':MaKH' => $MaKH,
        ':TenDangNhap' => $TenDangNhap,
        ':MatKhau' => $MatKhau,
        ':TrangThai' => $TrangThai,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $ID = trim((string)($data['ID'] ?? ''));
      if ($ID === '') respond(400, ['ok' => false, 'error' => 'Missing ID']);

      $stmt = $pdo->prepare('DELETE FROM TAIKHOAN WHERE ID=:ID');
      $stmt->execute([':ID' => $ID]);
      respond(200, ['ok' => true]);
    }

    case 'login': {
      session_start();

      $TenDangNhap = trim((string)($data['TenDangNhap'] ?? ''));
      $MatKhau = trim((string)($data['MatKhau'] ?? ''));

      if ($TenDangNhap === '' || $MatKhau === '') {
        respond(400, ['ok' => false, 'error' => 'Missing credentials']);
      }

      $stmt = $pdo->prepare('
        SELECT ID, TrangThai
        FROM TAIKHOAN
        WHERE TenDangNhap=:TenDangNhap AND MatKhau=:MatKhau
      ');
      $stmt->execute([
        ':TenDangNhap' => $TenDangNhap,
        ':MatKhau' => $MatKhau,
      ]);
      $row = $stmt->fetch();
      if (!$row) {
        respond(401, ['ok' => false, 'error' => 'Sai tài khoản hoặc mật khẩu']);
      }
      if (($row['TrangThai'] ?? '') !== 'HoatDong') {
        respond(403, ['ok' => false, 'error' => 'Tài khoản đang không hoạt động']);
      }

      $_SESSION['account_id'] = $row['ID'];
      respond(200, ['ok' => true, 'account_id' => $row['ID']]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

