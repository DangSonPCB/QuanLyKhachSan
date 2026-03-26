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
      $stmt = $pdo->query(
        'SELECT MaKH, TenKH, SDTKH, NgaySinhKH, GioiTinh, CCCDKH, emailKH
         FROM KHACHHANG ORDER BY MaKH'
      );
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdown': {
      $stmt = $pdo->query('SELECT MaKH, TenKH FROM KHACHHANG ORDER BY MaKH');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $TenKH = trim((string)($data['TenKH'] ?? ''));
      $SDTKH = trim((string)($data['SDTKH'] ?? ''));
      $NgaySinhKH = $data['NgaySinhKH'] ?? null;
      $GioiTinh = trim((string)($data['GioiTinh'] ?? ''));
      $CCCDKH = trim((string)($data['CCCDKH'] ?? ''));
      $emailKH = trim((string)($data['emailKH'] ?? ''));

      if ($MaKH === '' || $TenKH === '' || $SDTKH === '' || $NgaySinhKH === null || $GioiTinh === '' || $CCCDKH === '' || $emailKH === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        INSERT INTO KHACHHANG (MaKH, TenKH, SDTKH, NgaySinhKH, GioiTinh, CCCDKH, emailKH)
        VALUES (:MaKH, :TenKH, :SDTKH, :NgaySinhKH, :GioiTinh, :CCCDKH, :emailKH)
      ');
      $stmt->execute([
        ':MaKH' => $MaKH,
        ':TenKH' => $TenKH,
        ':SDTKH' => $SDTKH,
        ':NgaySinhKH' => $NgaySinhKH,
        ':GioiTinh' => $GioiTinh,
        ':CCCDKH' => $CCCDKH,
        ':emailKH' => $emailKH,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $TenKH = trim((string)($data['TenKH'] ?? ''));
      $SDTKH = trim((string)($data['SDTKH'] ?? ''));
      $NgaySinhKH = $data['NgaySinhKH'] ?? null;
      $GioiTinh = trim((string)($data['GioiTinh'] ?? ''));
      $CCCDKH = trim((string)($data['CCCDKH'] ?? ''));
      $emailKH = trim((string)($data['emailKH'] ?? ''));

      if ($MaKH === '' || $TenKH === '' || $SDTKH === '' || $NgaySinhKH === null || $GioiTinh === '' || $CCCDKH === '' || $emailKH === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE KHACHHANG
        SET TenKH=:TenKH, SDTKH=:SDTKH, NgaySinhKH=:NgaySinhKH, GioiTinh=:GioiTinh, CCCDKH=:CCCDKH, emailKH=:emailKH
        WHERE MaKH=:MaKH
      ');
      $stmt->execute([
        ':MaKH' => $MaKH,
        ':TenKH' => $TenKH,
        ':SDTKH' => $SDTKH,
        ':NgaySinhKH' => $NgaySinhKH,
        ':GioiTinh' => $GioiTinh,
        ':CCCDKH' => $CCCDKH,
        ':emailKH' => $emailKH,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      if ($MaKH === '') respond(400, ['ok' => false, 'error' => 'Missing MaKH']);

      $stmt = $pdo->prepare('DELETE FROM KHACHHANG WHERE MaKH=:MaKH');
      $stmt->execute([':MaKH' => $MaKH]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

