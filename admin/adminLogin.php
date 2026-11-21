<?php
require_once '../files/dbConnect.php';
if (isset($_SESSION['admin_username'])) {
  header("location:adminPortal.php");
  exit;
}
require_once '../includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $username = esc($conn, $_POST['admin_username']);
  $password = $_POST['admin_password'];
  $res = mysqli_query($conn, "SELECT * FROM admins WHERE ADMIN_USERNAME='$username';");
  if ($res) {
    if (mysqli_num_rows($res) > 0) {
      while ($r = mysqli_fetch_row($res)) {
        if ($password == $r[1]) {
          if ($r[2] == "active" || $r[2] == "ACTIVE") {
            $_SESSION['admin_username'] = $r[0];
            $_SESSION['admin_password'] = $r[1];
            header("location:adminPortal.php");
            exit;
          } else {
            $error = "This admin account is not active.";
          }
        } else {
          $error = "Wrong password. Please try again.";
        }
      }
    } else {
      $error = "We couldn't find that admin username.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../design images/favicon-192.png">
  <title>Admin sign in · E-Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu"
    crossorigin="anonymous"></script>
</head>

<body style="background:linear-gradient(135deg,#312e81 0%,#4f46e5 55%,#7c3aed 100%);min-height:100vh">

  <!-- <div class="bg-white border-bottom py-2 shadow-sm">
    <div class="container"><img class="img-fluid d-block mx-auto" src="../design images/college.png"
        alt="Sri Kaliswari College"></div>
  </div> -->

  <div class="d-flex align-items-center justify-content-center p-3" style="min-height:calc(100vh - 50px)">
    <div class="card border-0 shadow-lg rounded-4 w-100" style="max-width:430px">
      <div class="card-body p-4 p-md-5">

        <div class="text-center mb-4">
          <div class="d-inline-flex align-items-center justify-content-center rounded-3 text-white fw-bold fs-4 mb-3"
            style="width:56px;height:56px;background:linear-gradient(135deg,#6366f1,#4338ca)">E</div>
          <h1 class="h3 fw-bold mb-1">Admin sign in</h1>
          <p class="text-secondary mb-0">Authorised staff only.</p>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger py-2 small" role="alert"><?= e($error) ?></div><?php endif; ?>

        <form method="post" autocomplete="off">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="uname" name="admin_username" placeholder="Username"
              value="<?= e($_POST['admin_username'] ?? '') ?>" required autofocus>
            <label for="uname">Username</label>
          </div>
          <div class="form-floating mb-2">
            <input type="password" class="form-control" id="pwd" name="admin_password" placeholder="Password" required>
            <label for="pwd">Password</label>
          </div>
          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="toggle">
            <label class="form-check-label small text-secondary" for="toggle">Show password</label>
          </div>
          <button type="submit" class="btn btn-lg w-100 text-white fw-semibold" style="background:#4f46e5">Sign
            in</button>
        </form>

        <p class="text-center text-secondary small mt-4 mb-0"><a href="../index.php"
            class="fw-semibold text-decoration-none">&larr; Back to student sign in</a></p>
      </div>
    </div>
  </div>

  <footer class="d-flex align-items-center justify-content-center flex-wrap gap-2 small text-white-50 pb-3">
    <span>Created and owned by</span>
    <img src="../design%20images/dev-logo-transparent.png" alt="Manoj kumar R" class="bg-white rounded-2 p-1"
      style="height:26px;width:auto">
    <span>&middot; &copy; <?= date('Y') ?></span>
  </footer>

  <script>
    document.getElementById('toggle').addEventListener('change', function () {
      document.getElementById('pwd').type = this.checked ? 'text' : 'password';
    });
  </script>
</body>

</html>