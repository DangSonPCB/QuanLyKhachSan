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
if (!$action) {
    respond(400, ['ok' => false, 'error' => 'Missing action']);
}

try {
    $pdo = db();

    switch ($action) {
        case 'list': {
            $stmt = $pdo->query('SELECT MaDV, TenDV, GiaDV, BatDau, KetThuc FROM DICHVU ORDER BY MaDV');
            $rows = $stmt->fetchAll();
            respond(200, ['ok' => true, 'data' => $rows]);
        }

        case 'dropdown': {
            $stmt = $pdo->query("SELECT MaDV, TenDV FROM DICHVU ORDER BY MaDV");
            respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
        }

        case 'create': {
            $MaDV = trim((string)($data['MaDV'] ?? ''));
            $TenDV = trim((string)($data['TenDV'] ?? ''));
            $GiaDV = $data['GiaDV'] ?? null;
            $BatDau = trim((string)($data['BatDau'] ?? ''));
            $KetThuc = trim((string)($data['KetThuc'] ?? ''));

            if ($MaDV === '' || $TenDV === '' || $GiaDV === null || $BatDau === '' || $KetThuc === '') {
                respond(400, ['ok' => false, 'error' => 'Missing fields']);
            }

            $stmt = $pdo->prepare('INSERT INTO DICHVU (MaDV, TenDV, GiaDV, BatDau, KetThuc) VALUES (:MaDV, :TenDV, :GiaDV, :BatDau, :KetThuc)');
            $stmt->execute([
                ':MaDV' => $MaDV,
                ':TenDV' => $TenDV,
                ':GiaDV' => $GiaDV,
                ':BatDau' => $BatDau,
                ':KetThuc' => $KetThuc,
            ]);

            respond(200, ['ok' => true]);
        }

        case 'update': {
            $MaDV = trim((string)($data['MaDV'] ?? ''));
            $TenDV = trim((string)($data['TenDV'] ?? ''));
            $GiaDV = $data['GiaDV'] ?? null;
            $BatDau = trim((string)($data['BatDau'] ?? ''));
            $KetThuc = trim((string)($data['KetThuc'] ?? ''));

            if ($MaDV === '' || $TenDV === '' || $GiaDV === null || $BatDau === '' || $KetThuc === '') {
                respond(400, ['ok' => false, 'error' => 'Missing fields']);
            }

            $stmt = $pdo->prepare('UPDATE DICHVU SET TenDV = :TenDV, GiaDV = :GiaDV, BatDau = :BatDau, KetThuc = :KetThuc WHERE MaDV = :MaDV');
            $stmt->execute([
                ':MaDV' => $MaDV,
                ':TenDV' => $TenDV,
                ':GiaDV' => $GiaDV,
                ':BatDau' => $BatDau,
                ':KetThuc' => $KetThuc,
            ]);

            respond(200, ['ok' => true]);
        }

        case 'delete': {
            $MaDV = trim((string)($data['MaDV'] ?? ''));
            if ($MaDV === '') {
                respond(400, ['ok' => false, 'error' => 'Missing MaDV']);
            }

            // Có FK từ SUDUNG_DV / CHITIET_HD_DV tới DICHVU nên DELETE có thể thất bại.
            $stmt = $pdo->prepare('DELETE FROM DICHVU WHERE MaDV = :MaDV');
            $stmt->execute([':MaDV' => $MaDV]);

            respond(200, ['ok' => true]);
        }

        default:
            respond(400, ['ok' => false, 'error' => 'Unknown action']);
    }
} catch (Throwable $e) {
    respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

