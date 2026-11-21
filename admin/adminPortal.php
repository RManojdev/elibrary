<?php
require '../includes/admin_boot.php';
date_default_timezone_set('Asia/Kolkata');
$pageTitle='Dashboard'; $active='home';

function db_rows($conn,$sql){ try{ $r=mysqli_query($conn,$sql); return $r?mysqli_fetch_all($r,MYSQLI_ASSOC):[]; }catch(Throwable $x){ return []; } }
function db_one($conn,$sql){ $r=db_rows($conn,$sql); return $r?(int)array_values($r[0])[0]:0; }
function status_split($rows){ $a=0;$i=0; foreach($rows as $r){ if(strtolower($r['status'])==='active') $a+=(int)$r['c']; else $i+=(int)$r['c']; } return [$a,$i]; }
function pct($n,$t){ return $t>0?(int)round($n*100/$t):0; }
$palette=['#4f46e5','#0d9488','#f59e0b','#ec4899','#64748b','#8b5cf6','#06b6d4','#84cc16'];

/* ---- data ---- */
[$booksActive,$booksInactive]=status_split(db_rows($conn,"SELECT status, COUNT(*) c FROM books GROUP BY status"));
[$usersActive,$usersInactive]=status_split(db_rows($conn,"SELECT status, COUNT(*) c FROM users GROUP BY status"));
[$adminsActive,$adminsInactive]=status_split(db_rows($conn,"SELECT status, COUNT(*) c FROM admins GROUP BY status"));
$typeCounts=array_column(db_rows($conn,"SELECT media_type AS t, COUNT(*) c FROM books GROUP BY media_type"),'c','t');
$totalBooks=$booksActive+$booksInactive; $totalUsers=$usersActive+$usersInactive; $totalAdmins=$adminsActive+$adminsInactive;
$totalSaves=db_one($conn,"SELECT COUNT(*) FROM saved_books");
$savers=db_one($conn,"SELECT COUNT(DISTINCT user_name) FROM saved_books");

$byCategory=db_rows($conn,"SELECT category AS label, COUNT(*) c FROM books GROUP BY category ORDER BY c DESC");
$byLanguage=db_rows($conn,"SELECT language AS label, COUNT(*) c FROM books GROUP BY language ORDER BY c DESC");
$byDept=db_rows($conn,"SELECT department AS label, COUNT(*) c FROM user_details GROUP BY department ORDER BY c DESC");
$byPos=db_rows($conn,"SELECT position AS label, COUNT(*) c FROM user_details GROUP BY position ORDER BY c DESC");
$topSaved=db_rows($conn,"SELECT b.book_id, b.book_title, b.author_name, b.book_image, COUNT(*) c FROM saved_books sb JOIN books b ON b.book_id=sb.saved_book_id GROUP BY b.book_id, b.book_title, b.author_name, b.book_image ORDER BY c DESC, b.book_title ASC LIMIT 5");
$inactiveUsers=db_rows($conn,"SELECT t1.user_reg_no AS reg, t1.name AS name FROM user_details t1 INNER JOIN users t2 ON t1.user_reg_no=t2.user_name WHERE LOWER(t2.status)='inactive' ORDER BY t1.name LIMIT 5");
$inactiveAdmins=db_rows($conn,"SELECT t1.admin_reg_no AS reg, t1.name AS name FROM admin_details t1 INNER JOIN admins t2 ON t1.admin_reg_no=t2.admin_username WHERE LOWER(t2.status)='inactive' ORDER BY t1.name LIMIT 5");
$accounts=[];
foreach($inactiveUsers as $r){ $accounts[]=$r+['type'=>'User','action'=>'listUsers.php']; }
foreach($inactiveAdmins as $r){ $accounts[]=$r+['type'=>'Admin','action'=>'listAdmins.php']; }
$accounts=array_slice($accounts,0,6);
$inactiveTotal=$usersInactive+$adminsInactive;

$cAudio=(int)($typeCounts['audio']??0); $cVideo=(int)($typeCounts['video']??0); $cPdf=max(0,$totalBooks-$cAudio-$cVideo);
$typeSub='<b>'.$cPdf.'</b> PDF · <b>'.$cAudio.'</b> audio · <b>'.$cVideo.'</b> video';
$hr=(int)date('G'); $greet=$hr<12?'Good morning':($hr<17?'Good afternoon':'Good evening');

/* ---- small renderers ---- */
function kpi($label,$num,$chip,$color,$activeN,$total,$subA,$subB){
    $p=pct($activeN,$total);
    echo '<div class="col-sm-6 col-xl-3"><div class="kpi" style="--c:'.$color.'"><div class="kpi-top"><span class="kpi-label">'.e($label).'</span><span class="kpi-chip">'.e($chip).'</span></div>';
    echo '<div class="kpi-num">'.(int)$num.'</div>';
    echo '<div class="bar-track mt-3"><div class="bar-fill" style="width:'.$p.'%;background:'.$color.'"></div></div>';
    echo '<div class="kpi-sub">'.$subA.'</div>';
    if($subB!=='') echo '<div class="kpi-sub mt-1">'.$subB.'</div>';
    echo '</div></div>';
}
function bars($items){
    if(!$items){ echo '<p class="text-muted mb-0">No data yet.</p>'; return; }
    $max=max(array_map(function($i){ return (int)$i['c']; },$items)); if($max<1) $max=1;
    foreach($items as $i){
        echo '<div class="bar-row"><div class="bar-meta"><span>'.e($i['label']!==''?$i['label']:'Uncategorised').'</span><span>'.(int)$i['c'].'</span></div>';
        echo '<div class="bar-track"><div class="bar-fill" style="width:'.pct($i['c'],$max).'%"></div></div></div>';
    }
}
function donut($items,$palette,$label){
    $total=array_sum(array_map(function($i){ return (int)$i['c']; },$items));
    if($total<=0){ echo '<p class="text-muted mb-0">No data yet.</p>'; return; }
    $stops=[]; $acc=0; $k=0;
    foreach($items as $i){ $from=$acc; $acc+=$i['c']*100/$total; $stops[]=$palette[$k%count($palette)].' '.number_format($from,2,'.','').'% '.number_format($acc,2,'.','').'%'; $k++; }
    echo '<div class="donut-wrap"><div class="donut" style="background:conic-gradient('.implode(',',$stops).')"><div class="donut-center"><strong>'.$total.'</strong><span>'.e($label).'</span></div></div><ul class="legend">';
    $k=0;
    foreach($items as $i){
        echo '<li><span class="dot" style="background:'.$palette[$k%count($palette)].'"></span><span>'.e($i['label']!==''?$i['label']:'Other').'</span><span class="cnt">'.(int)$i['c'].'</span><span class="pc">'.pct($i['c'],$total).'%</span></li>'; $k++;
    }
    echo '</ul></div>';
}

include '../includes/admin_header.php';
?>
<div class="dash">

  <section class="hero mb-4">
    <div class="eyebrow"><?= e(date('l, d F Y')) ?></div>
    <h1><?= e($greet) ?>, <?= e($admin_name) ?></h1>
    <p>Here is how your library is doing today: <?= (int)$totalBooks ?> content item<?= $totalBooks==1?'':'s' ?> (<?= $cPdf ?> PDF, <?= $cAudio ?> audio, <?= $cVideo ?> video) and <?= (int)$totalUsers ?> reader<?= $totalUsers==1?'':'s' ?>.</p>
    <div class="d-flex flex-wrap gap-2">
      <a href="addBooks.php" class="btn btn-light">Add content</a>
      <a href="createUser.php" class="btn btn-glass">Create user</a>
      <a href="createAdmin.php" class="btn btn-glass">Create admin</a>
      <a href="listBooks.php" class="btn btn-glass">Browse content</a>
    </div>
  </section>

  <div class="row g-4 mb-4">
    <?php
      kpi('Content',$totalBooks,'C','#4f46e5',$booksActive,$totalBooks,$typeSub,'<b>'.$booksActive.'</b> active · <b>'.$booksInactive.'</b> disabled');
      kpi('Users',$totalUsers,'U','#0d9488',$usersActive,$totalUsers,'<b>'.$usersActive.'</b> active · <b>'.$usersInactive.'</b> inactive','');
      kpi('Admins',$totalAdmins,'A','#d97706',$adminsActive,$totalAdmins,'<b>'.$adminsActive.'</b> active · <b>'.$adminsInactive.'</b> inactive','');
      kpi('Saved content',$totalSaves,'S','#e11d48',$savers,max($totalUsers,1),'Saved by <b>'.$savers.'</b> of '.$totalUsers.' users','');
    ?>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-7">
      <div class="surface h-100">
        <div class="card-title-row"><h2>Content by category</h2><a href="listBooks.php">View all &rarr;</a></div>
        <?php bars($byCategory); ?>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="surface h-100">
        <div class="card-title-row"><h2>Users by department</h2><a href="listUsers.php">Manage &rarr;</a></div>
        <?php donut($byDept,$palette,'users'); ?>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-4">
      <div class="surface h-100">
        <div class="card-title-row"><h2>Content languages</h2></div>
        <?php donut($byLanguage,['#6366f1','#f59e0b','#0d9488','#ec4899'],'items'); ?>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="surface h-100">
        <div class="card-title-row"><h2>Staff and students</h2></div>
        <?php if($byPos): $tp=array_sum(array_map(function($i){ return (int)$i['c']; },$byPos)); ?>
          <div class="seg mb-3">
            <?php foreach($byPos as $k=>$i): ?><span style="width:<?= pct($i['c'],$tp) ?>%;background:<?= $palette[$k%count($palette)] ?>"></span><?php endforeach; ?>
          </div>
          <ul class="legend">
            <?php foreach($byPos as $k=>$i): ?>
              <li><span class="dot" style="background:<?= $palette[$k%count($palette)] ?>"></span><span><?= e($i['label']) ?></span><span class="cnt"><?= (int)$i['c'] ?></span><span class="pc"><?= pct($i['c'],$tp) ?>%</span></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?><p class="text-muted mb-0">No data yet.</p><?php endif; ?>
        <hr class="my-4">
        <div class="card-title-row mb-2"><h2>Active readers</h2></div>
        <div class="bar-track"><div class="bar-fill" style="width:<?= pct($savers,max($totalUsers,1)) ?>%;background:#e11d48"></div></div>
        <div class="kpi-sub"><b><?= pct($savers,max($totalUsers,1)) ?>%</b> of users have saved at least one item</div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="surface h-100 d-flex flex-column">
        <div class="card-title-row"><h2>Inactive accounts</h2><?php if($inactiveTotal>0): ?><span class="badge badge-soft badge-inactive"><?= (int)$inactiveTotal ?></span><?php endif; ?></div>
        <?php if($accounts): foreach($accounts as $a): ?>
          <div class="acct">
            <span class="avatar"><?= e(mb_substr($a['name']!==''?$a['name']:$a['reg'],0,1)) ?></span>
            <div class="acct-body">
              <div class="acct-name"><?= e($a['name']) ?></div>
              <div class="acct-meta"><?= e($a['type']) ?> · <?= e($a['reg']) ?></div>
            </div>
            <form method="post" action="<?= e($a['action']) ?>">
              <input type="hidden" name="chng-no" value="<?= e($a['reg']) ?>">
              <button type="submit" name="chng-user-status" class="btn btn-xs btn-outline-primary">Activate</button>
            </form>
          </div>
        <?php endforeach; ?>
          <?php if($inactiveTotal>count($accounts)): ?><div class="small text-muted mt-2">and <?= (int)($inactiveTotal-count($accounts)) ?> more</div><?php endif; ?>
        <?php else: ?>
          <div class="allclear"><strong>All accounts are active</strong>Nobody is locked out of the library.</div>
        <?php endif; ?>
        <div class="mt-auto pt-3 d-flex flex-wrap gap-2 border-top">
          <form action="printUserData.php" target="_blank" method="post"><button class="btn btn-soft btn-xs" name="print">Print users</button></form>
          <form action="printAdminData.php" target="_blank" method="post"><button class="btn btn-soft btn-xs" name="print">Print admins</button></form>
          <a href="listUsers.php" class="btn btn-soft btn-xs ms-auto">Manage users</a>
        </div>
      </div>
    </div>
  </div>

  <div class="surface">
    <div class="card-title-row"><h2>Most saved content</h2><a href="listBooks.php">All content &rarr;</a></div>
    <?php if($topSaved): $topMax=max(1,(int)$topSaved[0]['c']); $n=1; foreach($topSaved as $b): ?>
      <div class="top-item">
        <span class="rank"><?= $n++ ?></span>
        <img class="thumb" src="<?= cover_src($b['book_image'])!=='' ? e(cover_src($b['book_image'])) : cover_placeholder('pdf') ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='<?= cover_placeholder('pdf') ?>'">
        <div class="top-body">
          <div class="top-title"><?= e($b['book_title']) ?></div>
          <div class="top-author"><?= e($b['author_name']) ?></div>
          <div class="bar-track"><div class="bar-fill" style="width:<?= pct($b['c'],$topMax) ?>%"></div></div>
        </div>
        <div class="top-count"><?= (int)$b['c'] ?><small>saves</small></div>
      </div>
    <?php endforeach; else: ?>
      <p class="text-muted mb-0">Nothing has been saved yet. Once users start saving content, the most popular items appear here.</p>
    <?php endif; ?>
  </div>

</div>
<?php include '../includes/admin_footer.php'; ?>
