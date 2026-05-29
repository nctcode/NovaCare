<?php
$hosts = ['localhost', '127.0.0.1'];
$ports = ['3306', '3307', '3308'];
$users = ['root'];
$passwords = ['', 'root'];

foreach ($hosts as $host) {
    foreach ($ports as $port) {
        foreach ($users as $user) {
            foreach ($passwords as $pw) {
                try {
                    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $pw, [PDO::ATTR_TIMEOUT => 2]);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    echo "SUCCESS: connected to host=$host, port=$port, user=$user, pw=$pw\n";
                    
                    // List databases
                    $stmt = $pdo->query("SHOW DATABASES");
                    $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    echo "Databases: " . implode(', ', $dbs) . "\n\n";
                } catch (Exception $e) {
                    echo "FAILED: host=$host, port=$port, user=$user, pw=$pw, error=" . $e->getMessage() . "\n";
                }
            }
        }
    }
}
