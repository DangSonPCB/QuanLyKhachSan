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
        SELECT dp.MaDatPhong, dp.SoPhong, p.Loai,
               dp.MaKH, k.TenKH,
               dp.NgayNhan, dp.NgayTra, dp.SoKhanhhang
        FROM DATPHONG dp
        JOIN PHONG p ON p.SoPhong = dp.SoPhong
        JOIN KHACHHANG k ON k.MaKH = dp.MaKH
        ORDER BY dp.MaDatPhong
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownRooms': {
      $stmt = $pdo->query('SELECT SoPhong, Loai FROM PHONG ORDER BY SoPhong');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownCustomers': {
      $stmt = $pdo->query('SELECT MaKH, TenKH FROM KHACHHANG ORDER BY MaKH');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaDatPhong = trim((string)($data['MaDatPhong'] ?? ''));
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $NgayNhan = $data['NgayNhan'] ?? null;
      $NgayTra = $data['NgayTra'] ?? null;
      $SoKhanhhang = $data['SoKhanhhang'] ?? null;

      if ($MaDatPhong === '' || $SoPhong === '' || $MaKH === '' || $NgayNhan === null || $NgayTra === null || $SoKhanhhang === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        INSERT INTO DATPHONG (MaDatPhong, SoPhong, MaKH, NgayNhan, NgayTra, SoKhanhhang)
        VALUES (:MaDatPhong, :SoPhong, :MaKH, :NgayNhan, :NgayTra, :SoKhanhhang)
      ');
      $stmt->execute([
        ':MaDatPhong' => $MaDatPhong,
        ':SoPhong' => $SoPhong,
        ':MaKH' => $MaKH,
        ':NgayNhan' => $NgayNhan,
        ':NgayTra' => $NgayTra,
        ':SoKhanhhang' => $SoKhanhhang,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaDatPhong = trim((string)($data['MaDatPhong'] ?? ''));
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $NgayNhan = $data['NgayNhan'] ?? null;
      $NgayTra = $data['NgayTra'] ?? null;
      $SoKhanhhang = $data['SoKhanhhang'] ?? null;

      if ($MaDatPhong === '' || $SoPhong === '' || $MaKH === '' || $NgayNhan === null || $NgayTra === null || $SoKhanhhang === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE DATPHONG
        SET SoPhong=:SoPhong, MaKH=:MaKH, NgayNhan=:NgayNhan, NgayTra=:NgayTra, SoKhanhhang=:SoKhanhhang
        WHERE MaDatPhong=:MaDatPhong
      ');
      $stmt->execute([
        ':MaDatPhong' => $MaDatPhong,
        ':SoPhong' => $SoPhong,
        ':MaKH' => $MaKH,
        ':NgayNhan' => $NgayNhan,
        ':NgayTra' => $NgayTra,
        ':SoKhanhhang' => $SoKhanhhang,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaDatPhong = trim((string)($data['MaDatPhong'] ?? ''));
      if ($MaDatPhong === '') respond(400, ['ok' => false, 'error' => 'Missing MaDatPhong']);
      $stmt = $pdo->prepare('DELETE FROM DATPHONG WHERE MaDatPhong=:MaDatPhong');
      $stmt->execute([':MaDatPhong' => $MaDatPhong]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

