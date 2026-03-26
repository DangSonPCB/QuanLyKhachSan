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
        SELECT MaNV, TenNV, NgaySinhNV, ChucVu, SDTNV, CCCDNV
        FROM NHANVIEN ORDER BY MaNV
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdown': {
      $stmt = $pdo->query('SELECT MaNV, TenNV, ChucVu FROM NHANVIEN ORDER BY MaNV');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $TenNV = trim((string)($data['TenNV'] ?? ''));
      $NgaySinhNV = $data['NgaySinhNV'] ?? null;
      $ChucVu = trim((string)($data['ChucVu'] ?? ''));
      $SDTNV = trim((string)($data['SDTNV'] ?? ''));
      $CCCDNV = trim((string)($data['CCCDNV'] ?? ''));

      if ($MaNV === '' || $TenNV === '' || $NgaySinhNV === null || $ChucVu === '' || $SDTNV === '' || $CCCDNV === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        INSERT INTO NHANVIEN (MaNV, TenNV, NgaySinhNV, ChucVu, SDTNV, CCCDNV)
        VALUES (:MaNV, :TenNV, :NgaySinhNV, :ChucVu, :SDTNV, :CCCDNV)
      ');
      $stmt->execute([
        ':MaNV' => $MaNV,
        ':TenNV' => $TenNV,
        ':NgaySinhNV' => $NgaySinhNV,
        ':ChucVu' => $ChucVu,
        ':SDTNV' => $SDTNV,
        ':CCCDNV' => $CCCDNV,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $TenNV = trim((string)($data['TenNV'] ?? ''));
      $NgaySinhNV = $data['NgaySinhNV'] ?? null;
      $ChucVu = trim((string)($data['ChucVu'] ?? ''));
      $SDTNV = trim((string)($data['SDTNV'] ?? ''));
      $CCCDNV = trim((string)($data['CCCDNV'] ?? ''));

      if ($MaNV === '' || $TenNV === '' || $NgaySinhNV === null || $ChucVu === '' || $SDTNV === '' || $CCCDNV === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE NHANVIEN
        SET TenNV=:TenNV, NgaySinhNV=:NgaySinhNV, ChucVu=:ChucVu, SDTNV=:SDTNV, CCCDNV=:CCCDNV
        WHERE MaNV=:MaNV
      ');
      $stmt->execute([
        ':MaNV' => $MaNV,
        ':TenNV' => $TenNV,
        ':NgaySinhNV' => $NgaySinhNV,
        ':ChucVu' => $ChucVu,
        ':SDTNV' => $SDTNV,
        ':CCCDNV' => $CCCDNV,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      if ($MaNV === '') respond(400, ['ok' => false, 'error' => 'Missing MaNV']);

      $stmt = $pdo->prepare('DELETE FROM NHANVIEN WHERE MaNV=:MaNV');
      $stmt->execute([':MaNV' => $MaNV]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

