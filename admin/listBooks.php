<?php
require '../includes/admin_boot.php';
$pageTitle = 'All Content';
$active = 'books';

if (isset($_POST['delete_book_btn'])) {
  $id = esc($conn, $_POST['delete_bk_id']);
  $r = mysqli_query($conn, "SELECT * FROM books WHERE book_id='$id' LIMIT 1");
  $row = $r ? mysqli_fetch_assoc($r) : null;
  if (!$row) {
    flash('warning', 'That item no longer exists.');
  } else {
    mysqli_query($conn, "DELETE FROM saved_books WHERE saved_book_id='$id'");
    if (mysqli_query($conn, "DELETE FROM books WHERE book_id='$id' LIMIT 1")) {
      $file = basename($row['book_file']);
      $path = __DIR__ . '/../books/' . $file;
      if ($file !== '' && is_file($path) && !@unlink($path)) {
        flash('warning', 'The record was deleted, but the file could not be removed from the books folder.');
      }
      $cover = basename(trim($row['book_image'] ?? ''));
      if ($cover !== '') {
        $used = mysqli_query($conn, "SELECT 1 FROM books WHERE book_image='" . esc($conn, $row['book_image']) . "' LIMIT 1");
        $cp = __DIR__ . '/../images/' . $cover;
        if ((!$used || mysqli_num_rows($used) === 0) && is_file($cp))
          @unlink($cp);
      }
      flash('success', ucfirst(type_noun($row['media_type'] ?? 'pdf')) . ' deleted.');
    } else {
      flash('danger', 'Could not delete it.');
    }
  }
}

$f_title = trim($_GET['book_title'] ?? '');
$f_author = trim($_GET['author'] ?? '');
$f_edition = trim($_GET['edition'] ?? '');
$f_cat = trim($_GET['category'] ?? '');
$f_lang = trim($_GET['language'] ?? '');
$f_type = trim($_GET['type'] ?? '');
$sort = $_GET['sort'] ?? '';
$sql = "SELECT * FROM books WHERE 1";
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
if ($sort === 'active')
  $sql .= " AND status = 'active'";
if ($sort === 'inactive')
  $sql .= " AND status = 'inactive'";
if ($sort === 'asc')
  $sql .= " ORDER BY book_title ASC";
if ($sort === 'desc')
  $sql .= " ORDER BY book_title DESC";


$res = mysqli_query($conn, $sql);
$total = $res ? mysqli_num_rows($res) : 0;

include '../includes/admin_header.php';
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
  <select id="s" name="sort" class="form-select" aria-label="Show">
    <?php foreach (['' => 'Default order', 'asc' => 'Title A–Z', 'desc' => 'Title Z–A', 'active' => 'Active only', 'inactive' => 'Inactive only'] as $k => $v): ?>
      <option value="<?= $k ?>" <?= $sort === (string) $k ? ' selected' : '' ?>><?= $v ?></option>
    <?php endforeach; ?>
  </select>
  <a href="listBooks.php" class="btn btn-soft btn-sm">Reset</a>
</form>
<?php if ($total > 0): ?>
  <p class="text-muted small mb-3 ms-2"><?= (int) $total ?> item<?= $total == 1 ? '' : 's' ?></p>
  <div class="row g-4"><?php while ($r = mysqli_fetch_assoc($res)) {
    book_card($r, 'admin');
  } ?></div>
<?php else: ?>
  <div class="empty">
    <h3>No content found</h3>
    <p class="mb-3">Change the filters or add a new content.</p><a href="addBooks.php" class="btn btn-primary">Add
      content</a>
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
<?php include '../includes/admin_footer.php'; ?>