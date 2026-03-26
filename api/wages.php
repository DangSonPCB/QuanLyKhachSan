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
        SELECT l.MaLuong, l.MaNV, nv.TenNV, nv.ChucVu, l.ThanhToan
        FROM LUONG l
        JOIN NHANVIEN nv ON nv.MaNV = l.MaNV
        ORDER BY l.MaLuong
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownEmployees': {
      $stmt = $pdo->query('SELECT MaNV, TenNV, ChucVu FROM NHANVIEN ORDER BY MaNV');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaLuong = trim((string)($data['MaLuong'] ?? ''));
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $ThanhToan = $data['ThanhToan'] ?? null;

      if ($MaLuong === '' || $MaNV === '' || $ThanhToan === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('INSERT INTO LUONG (MaLuong, MaNV, ThanhToan) VALUES (:MaLuong, :MaNV, :ThanhToan)');
      $stmt->execute([
        ':MaLuong' => $MaLuong,
        ':MaNV' => $MaNV,
        ':ThanhToan' => $ThanhToan,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaLuong = trim((string)($data['MaLuong'] ?? ''));
      $MaNV = trim((string)($data['MaNV'] ?? ''));
      $ThanhToan = $data['ThanhToan'] ?? null;

      if ($MaLuong === '' || $MaNV === '' || $ThanhToan === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE LUONG SET MaNV=:MaNV, ThanhToan=:ThanhToan
        WHERE MaLuong=:MaLuong
      ');
      $stmt->execute([
        ':MaLuong' => $MaLuong,
        ':MaNV' => $MaNV,
        ':ThanhToan' => $ThanhToan,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaLuong = trim((string)($data['MaLuong'] ?? ''));
      if ($MaLuong === '') respond(400, ['ok' => false, 'error' => 'Missing MaLuong']);

      $stmt = $pdo->prepare('DELETE FROM LUONG WHERE MaLuong=:MaLuong');
      $stmt->execute([':MaLuong' => $MaLuong]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

