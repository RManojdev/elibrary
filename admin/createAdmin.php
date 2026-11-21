<?php
require '../includes/admin_boot.php';
$pageTitle = 'Create admin';
$active = 'addadmin';
$reg = '';
$name = '';
if (isset($_POST['s_user']) && (trim($_POST['admin_reg_no'] ?? '') === '' || trim($_POST['a_name'] ?? '') === '')) {
  flash('danger', 'Please fill in all the fields.');
  unset($_POST['s_user']);
}
if (isset($_POST['s_user'])) {
  $reg = trim($_POST['admin_reg_no']);
  $name = trim($_POST['a_name']);
  $r = esc($conn, $reg);
  $n = esc($conn, $name);
  try {
    if (mysqli_query($conn, "INSERT INTO admin_details VALUES ('$r','$n');") && mysqli_query($conn, "INSERT INTO admins VALUES ('$r','$r','active');")) {
      flash('success', 'Admin created. The default password is the register number.');
      $reg = '';
      $name = '';
    }
  } catch (Throwable $ex) {
    flash('danger', 'Could not create the admin. Is this register number already used?');
  }
}
include '../includes/admin_header.php';
?>
<div class="row justify-content-center">
  <div class="col-lg-7 col-xl-6">
    <div class="page-head">
      <h1>Create a new admin</h1>
      <p>The register number is also used as the first password.</p>
    </div>
    <form method="post" class="surface">
      <div class="mb-3"><label class="form-label req" for="r">Admin reg. no</label><input id="r" type="text"
          class="form-control" name="admin_reg_no" value="<?= e($reg) ?>" required></div>
      <div class="mb-4"><label class="form-label req" for="n">Name</label><input id="n" type="text" class="form-control"
          name="a_name" value="<?= e($name) ?>" required></div>
      <div class="d-flex gap-2"><button type="submit" name="s_user" class="btn btn-primary px-4">Create admin</button><a
          href="listAdmins.php" class="btn btn-soft">View admins</a></div>
    </form>
  </div>
</div>
<?php include '../includes/admin_footer.php'; ?>