<?php

$dsn = 'mysql:host=127.0.0.1;dbname=crm_nv;charset=utf8mb4';
$user = 'root';
$pass = '';

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$stmt = $pdo->query("SELECT password FROM users WHERE email='admin@cceconsultoria.com' LIMIT 1");
$hash = $stmt->fetchColumn();

if (!$hash) {
    echo "NO_USER\n";
    exit(1);
}

$ok = password_verify('password123', $hash);
echo $ok ? "OK\n" : "FAIL\n";

