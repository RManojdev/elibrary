<?php
function side_link($href, $key, $label, $active)
{
  echo '<a class="side-link' . ($active === $key ? ' active' : '') . '" href="' . $href . '">' . $label . '</a>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../design images/favicon-192.png">
  <title><?= e($pageTitle ?? 'Admin') ?> · E-Library Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
  <?php include __DIR__ . '/theme.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu"
    crossorigin="anonymous"></script>
</head>

<body>
  <aside class="sidebar offcanvas offcanvas-start" tabindex="-1" id="sidebar" aria-label="Admin menu">
    <div class="sidebar-head">
      <img src="../design images/elibrary.png" alt="elibrary-logo" class="brand-mark"/>
      <button type="button" class="btn-close btn-close-white ms-auto d-lg-none" data-bs-dismiss="offcanvas"
        data-bs-target="#sidebar" aria-label="Close"></button>
    </div>
    <div class="side-label">Overview</div>
    <?php side_link('adminPortal.php', 'home', 'Dashboard', $active ?? ''); ?>
    <div class="side-label">Content</div>
    <?php side_link('addBooks.php', 'addbook', 'Add Content', $active ?? '');
    side_link('listBooks.php', 'books', 'All Content', $active ?? ''); ?>
    <div class="side-label">Admins</div>
    <?php side_link('createAdmin.php', 'addadmin', 'Create admin', $active ?? '');
    side_link('listAdmins.php', 'admins', 'Manage admins', $active ?? ''); ?>
    <div class="side-label">Users</div>
    <?php side_link('createUser.php', 'adduser', 'Create user', $active ?? '');
    side_link('listUsers.php', 'users', 'Manage users', $active ?? ''); ?>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="btn btn-soft d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
        aria-controls="sidebar">Menu</button>
      <h2><?= e($pageTitle ?? 'Admin') ?></h2>
      <div class="dropdown">
        <button class="user-chip dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <span class="avatar"><?= e(mb_substr($admin_name, 0, 1)) ?></span><span
            class="d-none d-sm-inline"><?= e($admin_name) ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <form method="post"><button class="dropdown-item" name="change-password-btn">Change password</button></form>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <form method="post"><button class="dropdown-item text-danger" name="logout-btn">Log out</button></form>
          </li>
        </ul>
      </div>
    </header>
    <main class="container-page">