<?php
require_once __DIR__.'/storage.php';
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function esc($conn,$v){ return mysqli_real_escape_string($conn,(string)$v); }
function flash($type=null,$msg=null){ static $f=[]; if($type!==null){ $f[]=[$type,$msg]; return; } return $f; }
/* message that survives a redirect (shown on the next page) */
function flash_next($type,$msg){ if(session_status()===PHP_SESSION_ACTIVE){ $_SESSION['flash_queue'][]=[$type,$msg]; } }
function categories(){ return ['Tamil','English','Maths','Computer Science','Commerce','Novell','Botany','Zoology','Chemistry','Physics']; }
function count_of($conn,$table){ $r=mysqli_query($conn,"SELECT count(*) FROM $table"); return $r?mysqli_fetch_row($r)[0]:0; }

function render_toasts(){
    $f=flash();
    if(session_status()===PHP_SESSION_ACTIVE && !empty($_SESSION['flash_queue'])){ $f=array_merge($f,$_SESSION['flash_queue']); unset($_SESSION['flash_queue']); }
    if(!$f) return;
    $map=['success'=>'text-bg-success','danger'=>'text-bg-danger','warning'=>'text-bg-warning','info'=>'text-bg-primary'];
    echo '<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100">';
    foreach($f as $t){
        echo '<div class="toast align-items-center border-0 '.($map[$t[0]]??'text-bg-primary').'" role="alert" data-bs-delay="3500"><div class="d-flex"><div class="toast-body">'.e($t[1]).'</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>';
    }
    echo '</div>';
}

function category_options($selected=''){
    foreach(categories() as $c){ echo '<option value="'.e($c).'"'.($c===$selected?' selected':'').'>'.e($c).'</option>'; }
}

/* $mode: user | saved | admin */
function book_card($r,$mode='user'){
    $id=e($r['book_id']);
    $t=$r['media_type']??'pdf'; if(!isset(media_types()[$t])) $t='pdf';
    $cta=['pdf'=>'Read PDF','audio'=>'Play audio','video'=>'Watch video'][$t]; $noun=type_noun($t); $img=trim($r['book_image']??''); $coverUrl=cover_src($img); ?>
    <div class="col-sm-6 col-lg-4 col-xxl-3">
      <div class="book-card h-100">
        <div class="book-cover">
          <img src="<?= $coverUrl!=='' ? e($coverUrl) : cover_placeholder($t) ?>" alt="<?= e($r['book_title']) ?>" loading="lazy" onerror="this.onerror=null;this.src='<?= cover_placeholder($t) ?>'">
          <span class="badge book-type type-<?= $t ?>"><?= e(media_types()[$t]) ?></span>
          <span class="badge book-cat"><?= e($r['category']) ?></span>
          <?php if(($r['status']??'')==='inactive'): ?><span class="badge text-bg-danger book-flag">Disabled</span><?php endif; ?>
        </div>
        <div class="book-body">
          <h3 class="book-title"><?= e($r['book_title']) ?></h3>
          <p class="book-author"><?= e($r['author_name']) ?></p>
          <dl class="book-meta">
            <div><dt>ID</dt><dd><?= $id ?></dd></div>
            <div><dt>Edition</dt><dd><?= e($r['edition']) ?></dd></div>
            <div><dt>Language</dt><dd><?= e($r['language']) ?></dd></div>
          </dl>
          <div class="book-actions">
            <form method="post" target="_blank" action="pdfViewer.php">
              <input type="hidden" name="bk_id" value="<?= $id ?>">
              <button type="submit" name="bk-btn" class="btn btn-primary w-100"><?= $cta ?></button>
            </form>
            <?php if($mode==='user'): ?>
            <form method="post"><input type="hidden" name="save_bk_id" value="<?= $id ?>">
              <button type="submit" name="save_book_btn" class="btn btn-soft" title="Save this <?= e($noun) ?>">Save</button></form>
            <?php elseif($mode==='saved'): ?>
            <form method="post" onsubmit="return confirm('Remove this <?= e($noun) ?> from your saved list?')"><input type="hidden" name="remove_bk_id" value="<?= $id ?>">
              <button type="submit" name="remove_book_btn" class="btn btn-outline-danger" title="Remove">Remove</button></form>
            <?php elseif($mode==='admin'): ?>
            <form method="post" action="deleteContent.php" onsubmit="return confirm('Delete this <?= e($noun) ?> permanently? Its file will also be removed.')">
              <input type="hidden" name="delete_bk_id" value="<?= $id ?>">
              <button type="submit" name="delete_book_btn" class="btn btn-outline-danger" title="Delete">Delete</button></form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
<?php }

function departments(){ return ['Tamil','English','Maths','Computer Science']; }
function dept_options($sel=''){ foreach(departments() as $d){ echo '<option value="'.e($d).'"'.($d===$sel?' selected':'').'>'.e($d).'</option>'; } }
function status_badge($s){ $on=(strtolower($s)==='active'); return '<span class="badge badge-soft '.($on?'badge-active':'badge-inactive').'">'.e($on?'Active':'Inactive').'</span>'; }
function paginate($page,$pages,$keep=[]){
    if($pages<=1) return;
    $keep=array_filter($keep,function($v){ return $v!==''; });
    $q=function($p) use($keep){ return '?'.http_build_query(array_merge($keep,['page'=>$p])); };
    $range=5; $s=max($page-intdiv($range,2),1); $en=min($s+$range-1,$pages); $s=max($en-$range+1,1);
    echo '<nav class="d-flex justify-content-center mt-4" aria-label="Pages"><ul class="pagination mb-0">';
    echo '<li class="page-item'.($page<=1?' disabled':'').'"><a class="page-link" href="'.e($q(max($page-1,1))).'">Previous</a></li>';
    for($i=$s;$i<=$en;$i++){ echo '<li class="page-item'.($i==$page?' active':'').'"><a class="page-link" href="'.e($q($i)).'">'.$i.'</a></li>'; }
    echo '<li class="page-item'.($page>=$pages?' disabled':'').'"><a class="page-link" href="'.e($q(min($page+1,$pages))).'">Next</a></li>';
    echo '</ul></nav>';
}

function media_types(){ return ['pdf'=>'PDF','audio'=>'Audio','video'=>'Video']; }
function media_exts($type){
    $m=['pdf'=>['pdf'],
        'audio'=>['mp3','wav','ogg','oga','opus','m4a','aac','flac','wma','aiff','amr','mid'],
        'video'=>['mp4','webm','mkv','mov','avi','m4v','wmv','flv','3gp','mpeg','mpg','ogv','ts']];
    return $m[$type]??[];
}
function media_mime($ext){
    $m=['mp3'=>'audio/mpeg','wav'=>'audio/wav','ogg'=>'audio/ogg','oga'=>'audio/ogg','opus'=>'audio/ogg','m4a'=>'audio/mp4','aac'=>'audio/aac','flac'=>'audio/flac','wma'=>'audio/x-ms-wma','aiff'=>'audio/aiff','amr'=>'audio/amr','mid'=>'audio/midi',
        'mp4'=>'video/mp4','m4v'=>'video/mp4','webm'=>'video/webm','ogv'=>'video/ogg','mov'=>'video/quicktime','mkv'=>'video/x-matroska','avi'=>'video/x-msvideo','wmv'=>'video/x-ms-wmv','flv'=>'video/x-flv','3gp'=>'video/3gpp','mpeg'=>'video/mpeg','mpg'=>'video/mpeg','ts'=>'video/mp2t'];
    return $m[strtolower($ext)]??'application/octet-stream';
}
function type_filter_select($sel,$id='ty'){
    echo '<select id="'.$id.'" name="type" class="form-select"><option value="">All types</option>';
    foreach(media_types() as $k=>$l){ echo '<option value="'.$k.'"'.($sel===$k?' selected':'').'>'.e($l).'</option>'; }
    echo '</select>';
}

function type_noun($t){ return ['pdf'=>'PDF','audio'=>'audio','video'=>'video'][$t]??'content'; }
function item_noun($conn,$id){
    try{ $r=mysqli_query($conn,"SELECT * FROM books WHERE book_id='".esc($conn,$id)."' LIMIT 1"); $d=$r?mysqli_fetch_assoc($r):null; return $d?type_noun($d['media_type']??'pdf'):'content'; }
    catch(Throwable $x){ return 'content'; }
}
function cover_placeholder($t){
    $c=['pdf'=>['#4f46e5','#7c3aed','PDF'],'audio'=>['#0d9488','#06b6d4','AUDIO'],'video'=>['#e11d48','#f59e0b','VIDEO']][$t]??['#4f46e5','#7c3aed','FILE'];
    $svg='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="'.$c[0].'"/><stop offset="1" stop-color="'.$c[1].'"/></linearGradient></defs><rect width="300" height="400" fill="url(#g)"/><text x="150" y="215" font-family="Arial,sans-serif" font-size="46" font-weight="700" fill="#fff" text-anchor="middle">'.$c[2].'</text></svg>';
    return 'data:image/svg+xml;base64,'.base64_encode($svg);
}
