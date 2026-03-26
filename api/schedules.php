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
        SELECT pc.MaNV, nv.TenNV,
               pc.SoPhong, p.Loai,
               pc.CaLamViec, pc.NgayPhanCong
        FROM PHANCONG_NV pc
        JOIN NHANVIEN nv ON nv.MaNV = pc.MaNV
        JOIN PHONG p ON p.SoPhong = pc.SoPhong
        ORDER BY pc.NgayPhanCong, pc.MaNV, pc.SoPhong
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownEmployees': {
      $stmt = $pdo->query('SELECT MaNV, TenNV, ChucVu FROM NHANVIEN ORDER BY MaNV');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownRooms': {
      $stmt = $pdo->query('SELECT SoPhong, Loai FROM PHONG ORDER BY SoPhong');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      $CaLamViec = trim((string)($data['CaLamViec'] ?? ''));
      $NgayPhanCong = $data['NgayPhanCong'] ?? null;

      if ($MaNV === '' || $SoPhong === '' || $CaLamViec === '' || $NgayPhanCong === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        INSERT INTO PHANCONG_NV (MaNV, SoPhong, CaLamViec, NgayPhanCong)
        VALUES (:MaNV, :SoPhong, :CaLamViec, :NgayPhanCong)
      ');
      $stmt->execute([
        ':MaNV' => $MaNV,
        ':SoPhong' => $SoPhong,
        ':CaLamViec' => $CaLamViec,
        ':NgayPhanCong' => $NgayPhanCong,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      $NgayPhanCong = $data['NgayPhanCong'] ?? null;
      $CaLamViec = trim((string)($data['CaLamViec'] ?? ''));

      if ($MaNV === '' || $SoPhong === '' || $NgayPhanCong === null || $CaLamViec === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE PHANCONG_NV
        SET CaLamViec=:CaLamViec
        WHERE MaNV=:MaNV AND SoPhong=:SoPhong AND NgayPhanCong=:NgayPhanCong
      ');
      $stmt->execute([
        ':MaNV' => $MaNV,
        ':SoPhong' => $SoPhong,
        ':NgayPhanCong' => $NgayPhanCong,
        ':CaLamViec' => $CaLamViec,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      $NgayPhanCong = $data['NgayPhanCong'] ?? null;
      if ($MaNV === '' || $SoPhong === '' || $NgayPhanCong === null) {
        respond(400, ['ok' => false, 'error' => 'Missing keys']);
      }

      $stmt = $pdo->prepare('
        DELETE FROM PHANCONG_NV
        WHERE MaNV=:MaNV AND SoPhong=:SoPhong AND NgayPhanCong=:NgayPhanCong
      ');
      $stmt->execute([
        ':MaNV' => $MaNV,
        ':SoPhong' => $SoPhong,
        ':NgayPhanCong' => $NgayPhanCong,
      ]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

