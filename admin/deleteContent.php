<?php
require_once '../files/dbConnect.php';
require_once '../includes/functions.php';
if (!isset($_SESSION['admin_username'])) {
    header('location:adminLogin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book_btn'])) {
    $id = esc($conn, $_POST['delete_bk_id']);
    $r = mysqli_query($conn, "SELECT * FROM books WHERE book_id='$id' LIMIT 1");
    $row = $r ? mysqli_fetch_assoc($r) : null;
    if (!$row) {
        flash_next('warning', 'That item no longer exists.');
    } else {
        mysqli_query($conn, "DELETE FROM saved_books WHERE saved_book_id='$id'");
        if (mysqli_query($conn, "DELETE FROM books WHERE book_id='$id' LIMIT 1")) {
            $fileOk = remove_stored_file($row['book_file'], 'books');
            $cover = trim($row['book_image'] ?? '');
            if ($cover !== '') {
                $used = mysqli_query($conn, "SELECT 1 FROM books WHERE book_image='" . esc($conn, $cover) . "' LIMIT 1");
                if (!$used || mysqli_num_rows($used) === 0)
                    remove_stored_file($cover, 'images');
            }
            if ($fileOk)
                flash_next('success', ucfirst(type_noun($row['media_type'] ?? 'pdf')) . ' deleted.');
            else
                flash_next('warning', 'The record was deleted, but its file could not be removed from storage.');
        } else {
            flash_next('danger', 'Could not delete it.');
        }
    }
}
header('location:listBooks.php');
exit;
