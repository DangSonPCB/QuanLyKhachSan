<?php
declare(strict_types=1);

/**
 * Kết nối SQL Server bằng PDO (driver sqlsrv / pdo_sqlsrv).
 * Cấu hình bằng biến môi trường hoặc sửa trực tiếp các giá trị mặc định bên dưới.
 *
 * Ví dụ biến môi trường (trong PowerShell):
 *   setx DB_SERVER "localhost"
 *   setx DB_USER "sa"
 *   setx DB_PASS "your_password"
 *   setx DB_NAME "QuanLyKhachSan"
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Mặc định dùng MySQL vì bạn đang dùng MySQL.
    // Bạn có thể đổi sang SQL Server bằng cách set DB_DRIVER="sqlsrv".
    $driver = strtolower((string)(getenv('DB_DRIVER') ?: 'mysql'));

    $server = (string)(getenv('DB_SERVER') ?: 'localhost');
    $user = (string)(getenv('DB_USER') ?: 'root');
    $pass = (string)(getenv('DB_PASS') ?: '');
    $database = (string)(getenv('DB_NAME') ?: 'test1');

    if ($driver === 'sqlsrv' || $driver === 'mssql') {
        $dsn = "sqlsrv:Server={$server};Database={$database};";
    } else {
        // MySQL
        $dsn = "mysql:host={$server};dbname={$database};charset=utf8mb4";
    }
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

