<?php
/* Works on XAMPP (no environment variables needed) and on Vercel (settings come from environment variables). */

$host     = getenv('DB_HOST')     ?: 'localhost';
$dbname   = getenv('DB_DATABASE') ?: 'elibrary';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD');
if ($password === false) { $password = ''; }          // XAMPP's default root user has no password
$port = (int) (getenv('DB_PORT') ?: 3306);

try {

    // PDO connection
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // MySQLi connection for existing project functions
    $conn = new mysqli(
        $host,
        $username,
        $password,
        $dbname,
        $port
    );

    if ($conn->connect_error) {
        throw new Exception(
            "MySQLi connection failed: " . $conn->connect_error
        );
    }

    $conn->set_charset("utf8mb4");

} catch (Throwable $e) {

    error_log('[eLibrary DB] Connection failed: ' . $e->getMessage());

    http_response_code(500);
    exit('Database connection failed.');
}

/* Sessions: stored in the database on Vercel (no permanent disk there), normal PHP sessions on XAMPP. */
if (session_status() === PHP_SESSION_NONE) {
    $onVercel = getenv('VERCEL') || getenv('VERCEL_ENV') || getenv('SESSION_DRIVER') === 'db';
    if ($onVercel) {
        require_once __DIR__ . '/session_handler.php';
        ini_set('session.gc_maxlifetime', '14400');   // 4 hours of inactivity
        session_set_save_handler(new DbSessionHandler($conn), true);
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
