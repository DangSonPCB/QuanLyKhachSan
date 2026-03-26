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

$MaHD = trim((string)($_GET['MaHD'] ?? $data['MaHD'] ?? ''));

try {
  $pdo = db();

  switch ($action) {
    case 'listByInvoice': {
      if ($MaHD === '') respond(400, ['ok' => false, 'error' => 'Missing MaHD']);

      $stmt = $pdo->prepare('
        SELECT ci.MaHD, hd.NgayIn, ci.MaDV, dv.TenDV,
               kh.TenKH, nv.TenNV, ci.SoLuong
        FROM CHITIET_HD_DV ci
        JOIN HOADON hd ON hd.MaHD = ci.MaHD
        JOIN DICHVU dv ON dv.MaDV = ci.MaDV
        JOIN KHACHHANG kh ON kh.MaKH = hd.MaKH
        JOIN NHANVIEN nv ON nv.MaNV = hd.MaNV
        WHERE ci.MaHD = :MaHD
        ORDER BY ci.MaDV
      ');
      $stmt->execute([':MaHD' => $MaHD]);
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaHD = trim((string)($data['MaHD'] ?? ''));
      $MaDV = trim((string)($data['MaDV'] ?? ''));
      $SoLuong = $data['SoLuong'] ?? null;

      if ($MaHD === '' || $MaDV === '' || $SoLuong === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('INSERT INTO CHITIET_HD_DV (MaHD, MaDV, SoLuong) VALUES (:MaHD, :MaDV, :SoLuong)');
      $stmt->execute([
        ':MaHD' => $MaHD,
        ':MaDV' => $MaDV,
        ':SoLuong' => $SoLuong,
      ]);
      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaHD = trim((string)($data['MaHD'] ?? ''));
      $MaDV = trim((string)($data['MaDV'] ?? ''));
      $SoLuong = $data['SoLuong'] ?? null;

      if ($MaHD === '' || $MaDV === '' || $SoLuong === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE CHITIET_HD_DV
        SET SoLuong=:SoLuong
        WHERE MaHD=:MaHD AND MaDV=:MaDV
      ');
      $stmt->execute([
        ':MaHD' => $MaHD,
        ':MaDV' => $MaDV,
        ':SoLuong' => $SoLuong,
      ]);
      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaHD = trim((string)($data['MaHD'] ?? ''));
      $MaDV = trim((string)($data['MaDV'] ?? ''));
      if ($MaHD === '' || $MaDV === '') respond(400, ['ok' => false, 'error' => 'Missing keys']);

      $stmt = $pdo->prepare('DELETE FROM CHITIET_HD_DV WHERE MaHD=:MaHD AND MaDV=:MaDV');
      $stmt->execute([':MaHD' => $MaHD, ':MaDV' => $MaDV]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

