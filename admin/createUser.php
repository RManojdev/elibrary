<?php
require '../includes/admin_boot.php';
$pageTitle = 'Create user';
$active = 'adduser';
$v = ['user_reg_no' => '', 'u_name' => '', 'user_dept' => '', 'user_pos' => ''];

if (isset($_POST['s_user'])) {
  foreach (array_keys($v) as $k) {
    if (trim($_POST[$k] ?? '') === '') {
      flash('danger', 'Please fill in all the fields.');
      unset($_POST['s_user']);
      break;
    }
  }
}

if (isset($_POST['s_user'])) {
  foreach ($v as $k => $_) {
    $v[$k] = trim($_POST[$k] ?? '');
  }
  $q = array_map(function ($x) use ($conn) {
    return esc($conn, $x);
  }, $v);
  try {
    if (
      mysqli_query($conn, "INSERT INTO user_details VALUES ('{$q['user_reg_no']}','{$q['u_name']}','{$q['user_dept']}','{$q['user_pos']}');")
      && mysqli_query($conn, "INSERT INTO users VALUES ('{$q['user_reg_no']}','{$q['user_reg_no']}','active');")
    ) {
      flash('success', 'User created. The default password is the register number.');
      $v = array_map(function () {
        return '';
      }, $v);
    }
  } catch (Throwable $ex) {
    flash('danger', 'Could not create the user. Is this register number already used?');
  }
}

if (isset($_POST['m_user']) && isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
  $ok = 0;
  $skipped = 0;
  $fh = fopen($_FILES['csv_file']['tmp_name'], 'r');
  fgetcsv($fh); // header row
  while (($d = fgetcsv($fh)) !== false) {
    if (count($d) < 4 || trim($d[0]) === '') {
      $skipped++;
      continue;
    }
    $a = esc($conn, trim($d[0]));
    $b = esc($conn, trim($d[1]));
    $c = esc($conn, trim($d[2]));
    $p = esc($conn, trim($d[3]));
    try {
      mysqli_query($conn, "INSERT INTO user_details VALUES ('$a','$b','$c','$p')");
      mysqli_query($conn, "INSERT INTO users VALUES ('$a','$a','active')");
      $ok++;
    } catch (Throwable $ex) {
      $skipped++;
    }
  }
  fclose($fh);
  flash($ok > 0 ? 'success' : 'warning', "CSV processed: $ok user(s) created, $skipped row(s) skipped.");
} elseif (isset($_POST['m_user'])) {
  flash('danger', 'Please choose a CSV file.');
}
include '../includes/admin_header.php';
?>
<div class="page-head">
  <h1>Create users</h1>
  <p>Add one user, or upload a CSV to add many at once. The register number is also used as the first password.</p>
</div>
<div class="row g-4">
  <div class="col-lg-7">
    <form method="post" class="surface h-100">
      <h2 class="h5 fw-bold mb-3">Single user</h2>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label req" for="r">User reg. no</label><input id="r"
            class="form-control" name="user_reg_no" value="<?= e($v['user_reg_no']) ?>" required></div>
        <div class="col-md-6"><label class="form-label req" for="n">Name</label><input id="n" class="form-control"
            name="u_name" value="<?= e($v['u_name']) ?>" required></div>
        <div class="col-md-6"><label class="form-label req" for="d">Department</label>
          <select id="d" name="user_dept" class="form-select" required>
            <option value="">Select</option><?php dept_options($v['user_dept']); ?>
          </select>
        </div>
        <div class="col-md-6"><label class="form-label req" for="p">Position</label>
          <select id="p" name="user_pos" class="form-select" required>
            <option value="">Select</option><?php foreach (['Staff', 'Student'] as $p): ?>
              <option<?= $v['user_pos'] === $p ? ' selected' : '' ?>><?= $p ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="mt-4"><button type="submit" name="s_user" class="btn btn-primary px-4">Create user</button></div>
    </form>
  </div>
  <div class="col-lg-5">
    <form method="post" enctype="multipart/form-data" class="surface h-100">
      <h2 class="h5 fw-bold mb-1">Bulk upload (CSV)</h2>
      <p class="text-muted small">Columns: register number, name, department, position (Student/Staff).</p>
      <button type="button" class="btn btn-soft btn-sm mb-3" onclick="downloadcsv()">Download sample CSV</button>
      <br>
      <label class="form-label req" for="csv">CSV file</label>
      <input id="csv" type="file" class="form-control mb-4" accept=".csv" name="csv_file" required>
      <button type="submit" name="m_user" class="btn btn-primary px-4">Upload CSV</button>
    </form>
  </div>
</div>
<script>
  function downloadcsv() {
    var data = "Register Number,Name,Department,Position (Student/Staff)\n21CS001,Sample Student,Computer Science,Student\n";
    var blob = new Blob([data], { type: 'text/csv' }), url = URL.createObjectURL(blob), a = document.createElement('a');
    a.href = url; a.download = 'sample csv.csv'; document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url);
  }
</script>
<?php include '../includes/admin_footer.php'; ?>