<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    header("Location: admin/vragen_crud.php");
} else {
    header("Location: speler/teams_crud.php");
}
exit;
