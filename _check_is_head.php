<?php
require __DIR__ . '/config/database.php';
$db = new Database();
$c = $db->getConnection();
try {
    $r = $c->query("SHOW COLUMNS FROM doctors LIKE 'is_head'");
    if ($r->rowCount() > 0) {
        echo "doctors.is_head exists\n";
    } else {
        echo "doctors.is_head MISSING\n";
    }

    $r2 = $c->query("SHOW COLUMNS FROM nurses LIKE 'is_head'");
    if ($r2->rowCount() > 0) {
        echo "nurses.is_head exists\n";
    } else {
        echo "nurses.is_head MISSING\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
