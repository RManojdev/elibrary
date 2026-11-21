<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../design images/favicon-192.png">
  <title><?= e($pageTitle ?? 'E-Library') ?> · E-Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
  <?php include __DIR__ . '/theme.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu"
    crossorigin="anonymous"></script>
</head>

<body class="user-layout">
  <header class="topbar">
    <nav class="navbar navbar-expand-md container-fluid px-3 px-lg-4">
      <a class="navbar-brand d-flex align-items-center gap-2 me-4" href="userPage.php">
        <img src="../design images/elibrary-logo.png" alt="elibrary-logo" class="brand-mark-user"/>
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#userNav"
        aria-controls="userNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="userNav">
        <ul class="navbar-nav me-auto gap-1">
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'home' ? 'active' : '' ?>"
              href="userPage.php">Browse</a></li>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'saved' ? 'active' : '' ?>"
              href="savedBooks.php">Saved Content</a></li>
        </ul>
        <div class="dropdown">
          <button class="user-chip dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="avatar"><?= e(mb_substr($display_name, 0, 1)) ?></span><span><?= e($display_name) ?></span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <form method="post"><button class="dropdown-item" name="change-password-btn">Change password</button>
              </form>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <form method="post"><button class="dropdown-item text-danger" name="logout-btn">Log out</button></form>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <main class="container-page">