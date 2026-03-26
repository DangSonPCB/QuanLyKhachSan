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
        SELECT ud.MaDV, dv.TenDV,
               ud.MaKH, kh.TenKH,
               ud.NgaySuDung, ud.SoLuong
        FROM SUDUNG_DV ud
        JOIN DICHVU dv ON dv.MaDV = ud.MaDV
        JOIN KHACHHANG kh ON kh.MaKH = ud.MaKH
        ORDER BY ud.NgaySuDung, ud.MaDV, ud.MaKH
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownServices': {
      $stmt = $pdo->query('SELECT MaDV, TenDV FROM DICHVU ORDER BY MaDV');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownCustomers': {
      $stmt = $pdo->query('SELECT MaKH, TenKH FROM KHACHHANG ORDER BY MaKH');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaDV = trim((string)($data['MaDV'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $NgaySuDung = $data['NgaySuDung'] ?? null;
      $SoLuong = $data['SoLuong'] ?? null;

      if ($MaDV === '' || $MaKH === '' || $NgaySuDung === null || $SoLuong === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('INSERT INTO SUDUNG_DV (MaDV, MaKH, NgaySuDung, SoLuong) VALUES (:MaDV, :MaKH, :NgaySuDung, :SoLuong)');
      $stmt->execute([
        ':MaDV' => $MaDV,
        ':MaKH' => $MaKH,
        ':NgaySuDung' => $NgaySuDung,
        ':SoLuong' => $SoLuong,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaDV = trim((string)($data['MaDV'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $NgaySuDung = $data['NgaySuDung'] ?? null;
      $SoLuong = $data['SoLuong'] ?? null;

      if ($MaDV === '' || $MaKH === '' || $NgaySuDung === null || $SoLuong === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE SUDUNG_DV
        SET SoLuong=:SoLuong
        WHERE MaDV=:MaDV AND MaKH=:MaKH AND NgaySuDung=:NgaySuDung
      ');
      $stmt->execute([
        ':MaDV' => $MaDV,
        ':MaKH' => $MaKH,
        ':NgaySuDung' => $NgaySuDung,
        ':SoLuong' => $SoLuong,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaDV = trim((string)($data['MaDV'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $NgaySuDung = $data['NgaySuDung'] ?? null;

      if ($MaDV === '' || $MaKH === '' || $NgaySuDung === null) {
        respond(400, ['ok' => false, 'error' => 'Missing keys']);
      }

      $stmt = $pdo->prepare('DELETE FROM SUDUNG_DV WHERE MaDV=:MaDV AND MaKH=:MaKH AND NgaySuDung=:NgaySuDung');
      $stmt->execute([
        ':MaDV' => $MaDV,
        ':MaKH' => $MaKH,
        ':NgaySuDung' => $NgaySuDung,
      ]);

      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

