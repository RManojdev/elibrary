<?php
require_once '../files/dbConnect.php';
if(!isset($_SESSION['user_name']) && !isset($_SESSION['admin_username'])){ header('location:../index.php'); exit; }
require_once '../includes/functions.php';
if($_SERVER['REQUEST_METHOD']!="POST" || !isset($_POST['bk_id'])){ header('location:userPage.php'); exit; }

$bk_id=esc($conn,$_POST['bk_id']);
$title='Untitled'; $author=''; $cover=''; $type='pdf'; $src=''; $mime=''; $found=false;
$res=mysqli_query($conn,"SELECT * FROM books WHERE book_id = '$bk_id';");
if($res && ($d=mysqli_fetch_assoc($res))){
    $title=$d['book_title']; $author=$d['author_name']; $cover=cover_src($d['book_image']);
    $type=$d['media_type']??'pdf'; if(!isset(media_types()[$type])) $type='pdf';
    $stored=$d['book_file'];
    $src=content_src($stored);
    $found=is_storage_key($stored) ? ($src!=='') : file_exists("../books/".basename($stored));
    $mime=media_mime(pathinfo($stored,PATHINFO_EXTENSION));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" sizes="192x192" href="../design%20images/favicon-192.png?v=2">
  <title><?= e($title) ?> · E-Library</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-yEPZ7jP9S0QKiUQ0m3jjsO4ProIW7wvR9gXkI7JOGa2XVsw53Yke2bsdkBbjmmiX" crossorigin="anonymous">
  <style>
    html,body{height:100%;margin:0;background:#111827}
    .viewer-bar{height:56px;background:#1e1b4b;color:#fff;display:flex;align-items:center;gap:12px;padding:0 16px}
    .viewer-bar h1{font-size:1rem;font-weight:600;margin:0;flex:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .viewer-frame{height:calc(100% - 56px)}
    .viewer-frame embed{width:100%;height:100%;display:block}
    .stage{height:100%;display:flex;align-items:center;justify-content:center;padding:16px}
    .stage video{max-width:100%;max-height:100%;border-radius:12px;background:#000;box-shadow:0 20px 50px rgba(0,0,0,.5)}
    .player-card{width:100%;max-width:440px;background:#fff;border-radius:20px;padding:1.5rem;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,.45)}
    .player-card img{width:200px;max-width:60%;aspect-ratio:3/4;object-fit:cover;border-radius:14px;box-shadow:0 10px 24px rgba(15,23,42,.3);margin-bottom:1rem}
    .player-card h2{font-size:1.2rem;font-weight:700;margin:0}
    .player-card p{color:#64748b;margin:.2rem 0 1.1rem}
  </style>
</head>
<body>
  <div class="viewer-bar">
    <h1><?= e($title) ?></h1>
    <button class="btn btn-light btn-sm fw-semibold" onclick="window.close()">Close</button>
  </div>
  <div class="viewer-frame">
  <?php if(!$found): ?>
    <div class="h-100 d-flex align-items-center justify-content-center text-white-50">File not found.</div>
  <?php elseif($type==='audio'): ?>
    <div class="stage"><div class="player-card">
      <?php if($cover!==''): ?><img src="<?= e($cover) ?>" alt="" onerror="this.style.display='none'"><?php endif; ?>
      <h2><?= e($title) ?></h2><p><?= e($author) ?></p>
      <audio id="m" class="w-100" controls controlsList="nodownload" preload="metadata"><source src="<?= e($src) ?>" type="<?= e($mime) ?>"></audio>
      <div id="fb" class="alert alert-warning small text-start mt-3 d-none mb-0">Your browser can't play this audio format. <a href="<?= e($src) ?>" download class="alert-link">Download the file</a> to play it in another app.</div>
    </div></div>
  <?php elseif($type==='video'): ?>
    <div class="stage" style="flex-direction:column;gap:12px">
      <video id="m" controls controlsList="nodownload" playsinline preload="metadata"><source src="<?= e($src) ?>" type="<?= e($mime) ?>"></video>
      <div id="fb" class="alert alert-warning small d-none mb-0">Your browser can't play this video format. <a href="<?= e($src) ?>" download class="alert-link">Download the file</a> to watch it in another player.</div>
    </div>
  <?php else: ?>
    <embed src="<?= e($src) ?>#toolbar=0" type="application/pdf">
  <?php endif; ?>
  </div>
  <script>
    var m=document.getElementById('m'), fb=document.getElementById('fb');
    if(m && fb){ var s=m.querySelector('source'); function bad(){ fb.classList.remove('d-none'); } if(s) s.addEventListener('error',bad); m.addEventListener('error',bad); }
  </script>
</body>
</html>
