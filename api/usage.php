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

function respond(int $code, array $payload): void
{
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_GET['action'] ?? '';
$MaDV = trim((string)($_GET['MaDV'] ?? ''));

if (!$action) {
    respond(400, ['ok' => false, 'error' => 'Missing action']);
}
if (($action === 'usageByService' || $action === 'billsByService') && $MaDV === '') {
    respond(400, ['ok' => false, 'error' => 'Missing MaDV']);
}

try {
    $pdo = db();

    switch ($action) {
        case 'usageByService': {
            $stmt = $pdo->prepare(
                'SELECT ud.MaDV, dv.TenDV, ud.MaKH, kh.TenKH, ud.NgaySuDung, ud.SoLuong
                 FROM SUDUNG_DV ud
                 JOIN DICHVU dv ON dv.MaDV = ud.MaDV
                 JOIN KHACHHANG kh ON kh.MaKH = ud.MaKH
                 WHERE ud.MaDV = :MaDV
                 ORDER BY ud.NgaySuDung, ud.MaKH'
            );
            $stmt->execute([':MaDV' => $MaDV]);
            respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
        }

        case 'billsByService': {
            $stmt = $pdo->prepare(
                'SELECT ci.MaHD, hd.NgayIn, ci.MaDV, dv.TenDV,
                         hd.MaKH, kh.TenKH,
                         hd.MaNV, nv.TenNV,
                         ci.SoLuong
                 FROM CHITIET_HD_DV ci
                 JOIN HOADON hd ON hd.MaHD = ci.MaHD
                 JOIN DICHVU dv ON dv.MaDV = ci.MaDV
                 JOIN KHACHHANG kh ON kh.MaKH = hd.MaKH
                 JOIN NHANVIEN nv ON nv.MaNV = hd.MaNV
                 WHERE ci.MaDV = :MaDV
                 ORDER BY hd.NgayIn, ci.MaHD'
            );
            $stmt->execute([':MaDV' => $MaDV]);
            respond(200, ['ok' => true, 'data' => $stmt->fetchAll()]);
        }

        default:
            respond(400, ['ok' => false, 'error' => 'Unknown action']);
    }
} catch (Throwable $e) {
    respond(500, ['ok' => false, 'error' => $e->getMessage()]);
}

