<?php
require_once '../files/dbConnect.php';
if (!isset($_SESSION['admin_username'])) {
  header('location:adminLogin.php');
  exit;
}
require_once '../includes/functions.php';

$res = mysqli_query($conn, "SELECT t1.admin_reg_no, t1.name, t2.status FROM admin_details as t1 INNER JOIN admins as t2 ON t1.admin_reg_no=t2.admin_username");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../design images/favicon-192.png">
  <title>E-Library Admins Data · E-Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-WyOxWvTyr1QaMPfiPJSv76iReVthorl5uWlFtZPyK0IEB0hGzo9y5Bp9mYanr8Bu"
    crossorigin="anonymous"></script>
  <style>
    body {
      background: #fff;
      color: #111
    }

    .sheet {
      max-width: 980px;
      margin: 0 auto;
      padding: 16px
    }

    .sheet table thead th {
      background: #1e1b4b !important;
      color: #fff !important;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact
    }

    @media print {
      .sheet {
        padding: 0
      }
    }
  </style>
</head>

<body>
  <div class="sheet">
    <div class="d-flex justify-content-end gap-2 mb-3 d-print-none">
      <button class="btn btn-primary" onclick="window.print()">Print</button>
      <button class="btn btn-outline-secondary" onclick="window.close()">Close</button>
    </div>
    <!-- <img src="../design images/college.png" alt="College" class="img-fluid d-block mx-auto mb-3"> -->
    <h1 class="h5 fw-bold text-center mb-1">E-Library Admins Data</h1>
    <p class="text-center text-secondary small mb-4"><?= date('d M Y') ?></p>
    <table class="table table-bordered table-sm align-middle">
      <thead>
        <tr>
          <th>Reg. no</th>
          <th>Name</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($res):
          while ($r = $res->fetch_assoc()): ?>
            <tr>
              <td><?= e($r['admin_reg_no']) ?></td>
              <td><?= e($r['name']) ?></td>
              <td><?= e($r['status']) ?></td>
            </tr>
          <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>
</body>

</html>