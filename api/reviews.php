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
        SELECT dg.MaDG, dg.MaKH, kh.TenKH,
               dg.DiemDG, dg.NoiDungDG, dg.NgayDG
        FROM DANHGIA dg
        JOIN KHACHHANG kh ON kh.MaKH = dg.MaKH
        ORDER BY dg.MaDG
      ');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdownCustomers': {
      $stmt = $pdo->query('SELECT MaKH, TenKH FROM KHACHHANG ORDER BY MaKH');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $MaDG = trim((string)($data['MaDG'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $DiemDG = $data['DiemDG'] ?? null;
      $NoiDungDG = trim((string)($data['NoiDungDG'] ?? ''));
      $NgayDG = $data['NgayDG'] ?? null;

      if ($MaDG === '' || $MaKH === '' || $DiemDG === null || $NoiDungDG === '' || $NgayDG === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        INSERT INTO DANHGIA (MaDG, MaKH, DiemDG, NoiDungDG, NgayDG)
        VALUES (:MaDG, :MaKH, :DiemDG, :NoiDungDG, :NgayDG)
      ');
      $stmt->execute([
        ':MaDG' => $MaDG,
        ':MaKH' => $MaKH,
        ':DiemDG' => $DiemDG,
        ':NoiDungDG' => $NoiDungDG,
        ':NgayDG' => $NgayDG,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $MaDG = trim((string)($data['MaDG'] ?? ''));
      $MaKH = trim((string)($data['MaKH'] ?? ''));
      $DiemDG = $data['DiemDG'] ?? null;
      $NoiDungDG = trim((string)($data['NoiDungDG'] ?? ''));
      $NgayDG = $data['NgayDG'] ?? null;

      if ($MaDG === '' || $MaKH === '' || $DiemDG === null || $NoiDungDG === '' || $NgayDG === null) {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('
        UPDATE DANHGIA
        SET MaKH=:MaKH, DiemDG=:DiemDG, NoiDungDG=:NoiDungDG, NgayDG=:NgayDG
        WHERE MaDG=:MaDG
      ');
      $stmt->execute([
        ':MaDG' => $MaDG,
        ':MaKH' => $MaKH,
        ':DiemDG' => $DiemDG,
        ':NoiDungDG' => $NoiDungDG,
        ':NgayDG' => $NgayDG,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $MaDG = trim((string)($data['MaDG'] ?? ''));
      if ($MaDG === '') respond(400, ['ok' => false, 'error' => 'Missing MaDG']);
      $stmt = $pdo->prepare('DELETE FROM DANHGIA WHERE MaDG=:MaDG');
      $stmt->execute([':MaDG' => $MaDG]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

