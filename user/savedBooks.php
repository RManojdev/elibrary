<?php
require '../includes/user_boot.php';
$pageTitle = 'Saved content';
$active = 'saved';
$u = esc($conn, $_SESSION['user_name']);

if (isset($_POST['remove_book_btn'])) {
  $b = esc($conn, $_POST['remove_bk_id']);
  $noun = ucfirst(item_noun($conn, $b));
  if (mysqli_query($conn, "DELETE FROM saved_books WHERE saved_book_id='$b' AND user_name='$u';"))
    flash('success', $noun . ' removed from your saved list.');
}

$res = mysqli_query($conn, "SELECT * FROM books as b JOIN saved_books as sb ON b.book_id = sb.saved_book_id WHERE sb.user_name='$u';");
$total = $res ? mysqli_num_rows($res) : 0;

include '../includes/user_header.php';
?>
<?php if ($total > 0): ?>
  <div class="row g-4"><?php while ($r = mysqli_fetch_assoc($res)) {
    book_card($r, 'saved');
  } ?></div>
<?php else: ?>
  <div class="empty">
    <h3>Nothing saved yet</h3>
    <p>PDFs, audio and videos you save will appear here.</p>
    <a href="userPage.php" class="btn btn-primary">Browse content</a>
  </div>
<?php endif; ?>
<?php include '../includes/user_footer.php'; ?>