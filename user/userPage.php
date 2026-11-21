<?php
require '../includes/user_boot.php';
$pageTitle = 'Browse content';
$active = 'home';

if (isset($_POST['save_book_btn'])) {
  $u = esc($conn, $_SESSION['user_name']);
  $b = esc($conn, $_POST['save_bk_id']);
  $chk = mysqli_query($conn, "SELECT 1 FROM saved_books WHERE user_name='$u' AND saved_book_id='$b' LIMIT 1;");
  if ($chk && mysqli_num_rows($chk) > 0) {
    flash('info', 'This ' . item_noun($conn, $b) . ' is already in your saved list.');
  } else {
    try {
      if (mysqli_query($conn, "INSERT INTO saved_books VALUES ('$u','$b');"))
        flash('success', ucfirst(item_noun($conn, $b)) . ' saved to your list.');
    } catch (Throwable $ex) {
      flash('info', 'This item is already in your saved list.');
    }
  }
}

$f_title = trim($_GET['book_title'] ?? '');
$f_author = trim($_GET['author'] ?? '');
$f_edition = trim($_GET['edition'] ?? '');
$f_cat = trim($_GET['category'] ?? '');
$f_lang = trim($_GET['language'] ?? '');
$f_type = trim($_GET['type'] ?? '');
$sql = "SELECT * FROM books WHERE status='active'";
if ($f_title !== '')
  $sql .= " AND book_title LIKE '%" . esc($conn, $f_title) . "%'";
if ($f_author !== '')
  $sql .= " AND author_name LIKE '%" . esc($conn, $f_author) . "%'";
if ($f_edition !== '')
  $sql .= " AND edition LIKE '%" . esc($conn, $f_edition) . "%'";
if ($f_cat !== '')
  $sql .= " AND category = '" . esc($conn, $f_cat) . "'";
if ($f_lang !== '')
  $sql .= " AND language = '" . esc($conn, $f_lang) . "'";
if (isset(media_types()[$f_type]))
  $sql .= " AND media_type = '" . $f_type . "'";


$res = mysqli_query($conn, $sql);
$total = $res ? mysqli_num_rows($res) : 0;

include '../includes/user_header.php';
?>

<form method="get" class="filter-bar" id="filterForm">
  <input id="t" type="text" name="book_title" class="form-control grow" placeholder="Title" aria-label="Title"
    value="<?= e($f_title) ?>">
  <input id="a" type="text" name="author" class="form-control grow" placeholder="Author" aria-label="Author"
    value="<?= e($f_author) ?>">
  <input id="ed" type="text" name="edition" class="form-control" style="flex:0 1 110px;min-width:90px"
    placeholder="Edition" aria-label="Edition" value="<?= e($f_edition) ?>">
  <select id="c" name="category" class="form-select" aria-label="Category">
    <option value="">All categories</option><?php category_options($f_cat); ?>
  </select>
  <select id="l" name="language" class="form-select" aria-label="Language">
    <option value="">All languages</option><?php foreach (['Tamil', 'English'] as $l): ?>
      <option<?= $f_lang === $l ? ' selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
  </select>
  <?php type_filter_select($f_type); ?>
  <a href="userPage.php" class="btn btn-soft btn-sm">Reset</a>
</form>

<?php if ($total > 0): ?>
  <p class="text-muted small mb-3 ms-2"><?= (int) $total ?> item<?= $total == 1 ? '' : 's' ?> found</p>
  <div class="row g-4"><?php while ($r = $res->fetch_assoc()) {
    book_card($r, 'user');
  } ?></div>
<?php else: ?>
  <div class="empty">
    <h3>No content found</h3>
    <p class="mb-0">Try a different title, author or category.</p>
  </div>
<?php endif; ?>
<script>
  (function () {
    var f = document.getElementById('filterForm'); if (!f) return;
    var timer;
    function go(id) { if (id) sessionStorage.setItem('refocus', id); f.submit(); }
    f.querySelectorAll('select').forEach(function (s) { s.addEventListener('change', function () { go(); }); });
    f.querySelectorAll('input[type=text],input[type=search]').forEach(function (i) {
      i.addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(function () { go(i.id); }, 600); });
      i.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); clearTimeout(timer); go(i.id); } });
    });
    var id = sessionStorage.getItem('refocus');
    if (id) {
      sessionStorage.removeItem('refocus'); var el = document.getElementById(id);
      if (el) { el.focus(); var n = el.value.length; try { el.setSelectionRange(n, n); } catch (e) { } }
    }
  })();
</script>
<?php include '../includes/user_footer.php'; ?>