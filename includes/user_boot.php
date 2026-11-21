<?php
require_once __DIR__ . '/../files/dbConnect.php';
if (!isset($_SESSION['user_name'])) {
    header('location:../index.php');
    exit;
}
require_once __DIR__ . '/functions.php';

if (isset($_POST['logout-btn'])) {
    $_SESSION['user_name'] = null;
    $_SESSION['user_password'] = null;
    header('location:../index.php');
    exit;
}
if (isset($_POST['change-password-btn'])) {
    header('location:changePassword.php');
    exit;
}

$reg_no = esc($conn, $_SESSION['user_name']);
$print = ' ';
$res = mysqli_query($conn, "SELECT name from user_details WHERE user_reg_no='$reg_no'");
if ($res) {
    $n = mysqli_fetch_array($res);
    if ($n) {
        $print = $n[0];
    }
}
$display_name = trim($print) !== '' ? $print : $_SESSION['user_name'];
