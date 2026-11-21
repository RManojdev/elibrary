<?php
require_once __DIR__ . '/../files/dbConnect.php';
if (!isset($_SESSION['admin_username'])) {
    header('location:adminLogin.php');
    exit;
}
require_once __DIR__ . '/functions.php';

if (isset($_POST['logout-btn'])) {
    $_SESSION['admin_username'] = null;
    $_SESSION['admin_password'] = null;
    header('location:adminLogin.php');
    exit;
}
if (isset($_POST['change-password-btn'])) {
    header('location:changePassword.php');
    exit;
}
$admin_name = $_SESSION['admin_username'];
