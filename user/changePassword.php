<?php
require '../includes/user_boot.php';
$pageTitle = 'Change password';
$active = '';

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['cur-password'])) {
  $username = esc($conn, $_SESSION['user_name']);
  $cur = $_POST['cur-password'];
  $p1 = $_POST['chng-password-1'];
  $p2 = $_POST['chng-password-2'];
  $res = mysqli_query($conn, "SELECT * FROM users WHERE user_name='$username'");
  $r = $res ? mysqli_fetch_row($res) : null;
  if ($cur === '' || $p1 === '' || $p2 === '') {
    flash('danger', 'Please fill in all the fields.');
  } elseif (!$r || $cur != $r[1]) {
    flash('danger', 'Your current password is incorrect.');
  } elseif ($p1 !== $p2) {
    flash('danger', 'The new passwords do not match.');
  } else {
    if (mysqli_query($conn, "UPDATE users SET user_password = '" . esc($conn, $p1) . "' WHERE user_name = '$username'")) {
      $_SESSION['user_name'] = null;
      $_SESSION['user_password'] = null;
      header("location:../index.php");
      exit;
    }
    flash('danger', 'Could not update the password. Please try again.');
  }
}
include '../includes/user_header.php';
?>
<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-5 col-xl-4">
    <div class="page-head text-center">
      <h1>Change password</h1>
      <p>You will be signed out after changing it.</p>
    </div>
    <form method="post" class="surface">
      <div class="mb-3">
        <label class="form-label req" for="cur">Current password</label>
        <input type="password" class="form-control" id="cur" name="cur-password" required>
      </div>
      <div class="mb-3">
        <label class="form-label req" for="p1">New password</label>
        <input type="password" class="form-control" id="p1" name="chng-password-1" required>
      </div>
      <div class="mb-3">
        <label class="form-label req" for="p2">Re-type new password</label>
        <input type="password" class="form-control" id="p2" name="chng-password-2" required>
      </div>
      <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="show">
        <label class="form-check-label small text-muted" for="show">Show passwords</label>
      </div>
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary flex-fill">Update password</button>
        <a href="userPage.php" class="btn btn-soft">Cancel</a>
      </div>
    </form>
  </div>
</div>
<script>
  document.getElementById('show').addEventListener('change', function () {
    var t = this.checked ? 'text' : 'password';
    ['cur', 'p1', 'p2'].forEach(function (i) { document.getElementById(i).type = t; });
  });
</script>
<?php include '../includes/user_footer.php'; ?>