<?php
$conn = new PDO("mysql:host=127.0.0.1;port=3307;dbname=hospital_management;charset=utf8mb4", "root", "root");
$stmt = $conn->query("SHOW CREATE TABLE medical_records");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
echo $res['Create Table'] . "\n";
