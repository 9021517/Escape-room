<?php
$host = 'localhost';
$dbname = 'doctor_room';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Database-verbinding mislukt: " . $e->getMessage());
}
?>
