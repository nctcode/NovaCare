<?php
try {
    $conn = new PDO("mysql:host=127.0.0.1;port=3308;dbname=hospital_management;charset=utf8mb4", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully\n";
    $stmt = $conn->query("SELECT id, name, email, role FROM users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($users);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
