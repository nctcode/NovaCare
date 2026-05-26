<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/models/Department.php';
$d = new Department();
$d->setHeadDoctor(1, 1);
echo "Set doctor 1 as head of dept 1\n";

$db = new Database();
$c = $db->getConnection();
$r = $c->query("SELECT * FROM doctors WHERE is_head = 1");
print_r($r->fetchAll());
