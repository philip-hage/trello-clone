<?php
$user = 'root';
$pass = '';

try {
    $dbh = new PDO('mysql:host=localhost;dbname=kanban', $user, $pass);
    $system['dbconn'] = $dbh;
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
