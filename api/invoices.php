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
        SELECT hd.MaHD, hd.NgayIn, hd.MaNV, nv.TenNV, nv.ChucVu,
               hd.MaKH, kh.TenKH
        FROM HOADON hd
        JOIN NHANVIEN nv ON nv.MaNV = hd.MaNV
        JOIN KHACHHANG kh ON kh.MaKH = hd.MaKH
        ORDER BY hd.MaHD
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownEmployees': {
      $stmt = $pdo->query('SELECT MaNV, TenNV, ChucVu FROM NHANVIEN ORDER BY MaNV');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownCustomers': {
      $stmt = $pdo->query('SELECT MaKH, TenKH FROM KHACHHANG ORDER BY MaKH');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownInvoices': {
      $stmt = $pdo->query('SELECT MaHD, NgayIn FROM HOADON ORDER BY MaHD');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaHD = trim((string)($data['MaHD'] ?? ''));
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $NgayIn = $data['NgayIn'] ?? null;

      if ($MaHD === '' || $MaNV === '' || $MaKH === '' || $NgayIn === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('INSERT INTO HOADON (MaHD, MaNV, MaKH, NgayIn) VALUES (:MaHD, :MaNV, :MaKH, :NgayIn)');
      $stmt->execute([
        ':MaHD' => $MaHD,
        ':MaNV' => $MaNV,
        ':MaKH' => $MaKH,
        ':NgayIn' => $NgayIn,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaHD = trim((string)($data['MaHD'] ?? ''));
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $NgayIn = $data['NgayIn'] ?? null;

      if ($MaHD === '' || $MaNV === '' || $MaKH === '' || $NgayIn === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE HOADON
        SET MaNV=:MaNV, MaKH=:MaKH, NgayIn=:NgayIn
        WHERE MaHD=:MaHD
      ');
      $stmt->execute([
        ':MaHD' => $MaHD,
        ':MaNV' => $MaNV,
        ':MaKH' => $MaKH,
        ':NgayIn' => $NgayIn,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaHD = trim((string)($data['MaHD'] ?? ''));
      if ($MaHD === '') respond(400, ['ok' => false, 'error' => 'Missing MaHD']);

      // Có FK tới CHITIET_HD_DV nên DELETE có thể fail.
      $stmt = $pdo->prepare('DELETE FROM HOADON WHERE MaHD=:MaHD');
      $stmt->execute([':MaHD' => $MaHD]);

      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

