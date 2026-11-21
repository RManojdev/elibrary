<?php
require '../includes/admin_boot.php';
$pageTitle = 'Edit admin';
$active = 'admins';
if (isset($_POST['edit-no'])) {
  $_SESSION['edit_admin_reg'] = $_POST['edit-no'];
}
if (!isset($_SESSION['edit_admin_reg'])) {
  header('location:listAdmins.php');
  exit;
}
$e_no = esc($conn, $_SESSION['edit_admin_reg']);

if (isset($_POST['s_user'])) {
  $n = esc($conn, trim($_POST['u_name']));
  if (mysqli_query($conn, "UPDATE admin_details SET name='$n' WHERE admin_reg_no='$e_no';"))
    flash('success', 'Admin details updated.');
  else
    flash('danger', 'Could not update the admin.');
}
$res1 = mysqli_query($conn, "SELECT * FROM admin_details WHERE admin_reg_no='$e_no';");
$e = $res1 ? mysqli_fetch_array($res1) : null;
if (!$e) {
  header('location:listAdmins.php');
  exit;
}
include '../includes/admin_header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-7 col-xl-6">
    <div class="page-head">
      <h1>Edit admin</h1>
      <p>Update the details for <?= e($e[0]) ?>.</p>
    </div>
    <form method="post" class="surface">
      <div class="mb-3"><label class="form-label">Admin reg. no</label><input class="form-control"
          value="<?= e($e[0]) ?>" disabled></div>
      <div class="mb-4"><label class="form-label" for="n">Name</label><input id="n" class="form-control" name="u_name"
          value="<?= e($e[1]) ?>" required></div>
      <div class="d-flex gap-2"><button type="submit" name="s_user" class="btn btn-primary px-4">Save changes</button><a
          href="listAdmins.php" class="btn btn-soft">Back to admins</a></div>
    </form>
  </div>
</div>
<?php include '../includes/admin_footer.php'; ?>