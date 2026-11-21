<?php
require '../includes/admin_boot.php';
$pageTitle = 'Manage admins';
$active = 'admins';
$limit = 10;

if (isset($_POST['chng-user-status'])) {
  $no = esc($conn, $_POST['chng-no']);
  $res_2 = mysqli_query($conn, "SELECT status from admins where admin_username='$no';");
  $s = $res_2 ? mysqli_fetch_array($res_2) : null;
  $value = $s ? strtolower($s[0]) : '';
  $change = $value === 'active' ? 'inactive' : ($value === 'inactive' ? 'active' : '');
  if ($change !== '') {
    mysqli_query($conn, "UPDATE admins SET status='$change' WHERE admin_username='$no';");
    flash('success', 'Admin status changed to ' . $change . '.');
  } else {
    flash('warning', 'No records were updated.');
  }
}
if (isset($_POST['delete-user'])) {
  $no = esc($conn, $_POST['delete-no']);
  if ($no === esc($conn, $admin_name)) {
    flash('warning', 'You cannot delete the account you are signed in with.');
  } else {
    $a = mysqli_query($conn, "DELETE FROM admin_details WHERE admin_reg_no='$no';");
    $b = mysqli_query($conn, "DELETE FROM admins WHERE admin_username='$no';");
    if ($a && $b)
      flash('success', 'Admin was deleted.');
    else
      flash('danger', 'Could not delete the admin.');
  }
}

$f = ['reg' => trim($_GET['reg'] ?? ''), 'name' => trim($_GET['name'] ?? ''), 'status' => trim($_GET['status'] ?? '')];
$where = " WHERE 1";
if ($f['reg'] !== '')
  $where .= " AND t1.admin_reg_no LIKE '%" . esc($conn, $f['reg']) . "%'";
if ($f['name'] !== '')
  $where .= " AND t1.name LIKE '%" . esc($conn, $f['name']) . "%'";
if ($f['status'] !== '')
  $where .= " AND t2.status = '" . esc($conn, $f['status']) . "'";
$from = " FROM admin_details as t1 INNER JOIN admins as t2 ON t1.admin_reg_no=t2.admin_username";

$cr = mysqli_query($conn, "SELECT COUNT(*) AS total" . $from . $where);
$total = $cr ? (int) mysqli_fetch_assoc($cr)['total'] : 0;
$pages = max(1, (int) ceil($total / $limit));
$page = min(max(1, (int) ($_GET['page'] ?? 1)), $pages);
$start = ($page - 1) * $limit;
$res = mysqli_query($conn, "SELECT t1.admin_reg_no, t1.name, t2.status" . $from . $where . " LIMIT $start,$limit;");

include '../includes/admin_header.php';
?>
<form method="get" class="surface mb-4">
  <div class="row g-3 align-items-end">
    <div class="col-md-3"><label class="form-label">Reg. no</label><input type="search" class="form-control" name="reg"
        value="<?= e($f['reg']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Name</label><input type="search" class="form-control" name="name"
        value="<?= e($f['name']) ?>"></div>
    <div class="col-md-2"><label class="form-label">Status</label><select name="status" class="form-select">
        <option value="">All</option><?php foreach (['active', 'inactive'] as $p): ?>
          <option value="<?= $p ?>" <?= $f['status'] === $p ? ' selected' : '' ?>><?= ucfirst($p) ?></option><?php endforeach; ?>
      </select></div>
    <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary flex-fill">Search</button><a href="listAdmins.php"
        class="btn btn-soft">Reset</a></div>
  </div>
</form>

<div class="surface p-0 overflow-hidden">
  <div class="table-toolbar">
    <div class="fw-semibold"><?= $total ?> admin<?= $total == 1 ? '' : 's' ?></div>
    <div class="d-flex gap-2">
      <a href="createAdmin.php" class="btn btn-primary btn-sm">Add admin</a>
      <form action="printAdminData.php" target="_blank" method="post"><button type="submit" name="print"
          class="btn btn-soft btn-sm">Print list</button></form>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-modern">
      <thead>
        <tr>
          <th>Reg. no</th>
          <th>Name</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($res && mysqli_num_rows($res) > 0):
          while ($r = $res->fetch_assoc()): ?>
            <tr>
              <td class="fw-semibold"><?= e($r['admin_reg_no']) ?></td>
              <td><?= e($r['name']) ?></td>
              <td><?= status_badge($r['status']) ?></td>
              <td>
                <div class="row-actions">
                  <form method="post"><input type="hidden" name="chng-no" value="<?= e($r['admin_reg_no']) ?>">
                    <button name="chng-user-status"
                      class="btn btn-xs btn-outline-primary"><?= strtolower($r['status']) === 'active' ? 'Deactivate' : 'Activate' ?></button>
                  </form>
                  <form method="post" action="editAdminDetails.php"><input type="hidden" name="edit-no"
                      value="<?= e($r['admin_reg_no']) ?>">
                    <button class="btn btn-xs btn-soft">Edit</button>
                  </form>
                  <form method="post" onsubmit="return confirm('Delete this admin permanently?')"><input type="hidden"
                      name="delete-no" value="<?= e($r['admin_reg_no']) ?>">
                    <button name="delete-user" class="btn btn-xs btn-outline-danger">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="4">
              <div class="empty border-0 py-5">
                <h3>No admins found</h3>
                <p class="mb-0">Try different filters or add a new admin.</p>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php paginate($page, $pages, $f); ?>
<?php include '../includes/admin_footer.php'; ?>