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
      $stmt = $pdo->query('SELECT SoPhong, Loai, GiaThue, TrangThaiThue FROM PHONG ORDER BY SoPhong');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'dropdown': {
      $stmt = $pdo->query('SELECT SoPhong, Loai FROM PHONG ORDER BY SoPhong');
      respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
    }

    case 'create': {
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      $Loai = trim((string)($data['Loai'] ?? ''));
      $GiaThue = $data['GiaThue'] ?? null;
      $TrangThaiThue = trim((string)($data['TrangThaiThue'] ?? ''));

      if ($SoPhong === '' || $Loai === '' || $GiaThue === null || $TrangThaiThue === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('INSERT INTO PHONG (SoPhong, Loai, GiaThue, TrangThaiThue) VALUES (:SoPhong, :Loai, :GiaThue, :TrangThaiThue)');
      $stmt->execute([
        ':SoPhong' => $SoPhong,
        ':Loai' => $Loai,
        ':GiaThue' => $GiaThue,
        ':TrangThaiThue' => $TrangThaiThue,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'update': {
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      $Loai = trim((string)($data['Loai'] ?? ''));
      $GiaThue = $data['GiaThue'] ?? null;
      $TrangThaiThue = trim((string)($data['TrangThaiThue'] ?? ''));

      if ($SoPhong === '' || $Loai === '' || $GiaThue === null || $TrangThaiThue === '') {
        respond(400, ['ok' => false, 'error' => 'Missing fields']);
      }

      $stmt = $pdo->prepare('UPDATE PHONG SET Loai=:Loai, GiaThue=:GiaThue, TrangThaiThue=:TrangThaiThue WHERE SoPhong=:SoPhong');
      $stmt->execute([
        ':SoPhong' => $SoPhong,
        ':Loai' => $Loai,
        ':GiaThue' => $GiaThue,
        ':TrangThaiThue' => $TrangThaiThue,
      ]);

      respond(200, ['ok' => true]);
    }

    case 'delete': {
      $SoPhong = trim((string)($data['SoPhong'] ?? ''));
      if ($SoPhong === '') respond(400, ['ok' => false, 'error' => 'Missing SoPhong']);

      $stmt = $pdo->prepare('DELETE FROM PHONG WHERE SoPhong=:SoPhong');
      $stmt->execute([':SoPhong' => $SoPhong]);
      respond(200, ['ok' => true]);
    }

    default:
      respond(400, ['ok' => false, 'error' => 'Unknown action']);
  }
} catch (Throwable $e) {
  respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

