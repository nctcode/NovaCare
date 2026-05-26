<?php
session_start();
$_SESSION['user'] = [
    'id' => 2, // user_id for doctor 1
    'role' => 'doctor'
];

require __DIR__ . '/helpers/Security.php';
$head = Security::isHeadOfDepartment();
print_r($head);
