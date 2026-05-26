<?php
require __DIR__ . '/config/database.php';
$db = new Database();
$c = $db->getConnection();
$r = $c->query("SELECT d.id, u.email, u.name, d.is_head FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.is_head = 1");
$heads = $r->fetchAll();
if (empty($heads)) {
    echo "NO HEAD DOCTOR FOUND\n";
    // Let's make the first doctor a head
    $c->exec("UPDATE doctors SET is_head = 1 LIMIT 1");
    $r = $c->query("SELECT d.id, u.email, u.name, d.is_head FROM doctors d JOIN users u ON d.user_id = u.id WHERE d.is_head = 1");
    $heads = $r->fetchAll();
    echo "MADE A DOCTOR HEAD\n";
}
print_r($heads);
