<?php
require '../includes/admin_boot.php';
$pageTitle='Add content'; $active='addbook';

/* make sure the table knows about audio / video (runs once, harmless afterwards) */
try{
    $c=mysqli_query($conn,"SHOW COLUMNS FROM books LIKE 'media_type'");
    if($c && mysqli_num_rows($c)===0){ mysqli_query($conn,"ALTER TABLE books ADD COLUMN media_type VARCHAR(10) NOT NULL DEFAULT 'pdf'"); }
}catch(Throwable $x){}

$v=['book_title'=>'','book_author'=>'','book_edition'=>'','book_category'=>'','book_language'=>'','media_type'=>'pdf'];

function upload_error_text($code){
    if($code==UPLOAD_ERR_INI_SIZE || $code==UPLOAD_ERR_FORM_SIZE) return 'A file is bigger than the server limit ('.ini_get('upload_max_filesize').'). Raise upload_max_filesize and post_max_size in php.ini and restart Apache.';
    if($code==UPLOAD_ERR_PARTIAL) return 'The upload was interrupted. Please try again.';
    if($code==UPLOAD_ERR_NO_FILE) return 'Please choose the content file and the cover image.';
    return 'The upload failed (error code '.$code.').';
}

$useStorage=storage_enabled();                 // true on Vercel (R2), false on XAMPP (local folders)
$onVercel=(bool)(getenv('VERCEL') || getenv('VERCEL_ENV'));

if($_SERVER['REQUEST_METHOD']==="POST"){
    if(empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH']??0)>0){
        flash('danger','That upload is larger than the server allows (post_max_size = '.ini_get('post_max_size').'). Raise post_max_size and upload_max_filesize in php.ini and restart Apache.');
    } elseif(isset($_POST['book_title'])){
        foreach($v as $k=>$_){ $v[$k]=trim($_POST[$k]??''); }
        $type=isset(media_types()[$v['media_type']])?$v['media_type']:'pdf'; $v['media_type']=$type;
        $fileName=''; $coverName=''; $problem='';

        if(in_array('',[$v['book_title'],$v['book_author'],$v['book_edition'],$v['book_category'],$v['book_language']],true)){
            $problem='Please fill in all the fields.';
        } elseif($onVercel && !$useStorage){
            $problem='File storage is not configured on the server.';
        } elseif($useStorage){
            /* Vercel: the browser already uploaded both files straight to storage */
            $fileName=trim($_POST['content_key']??''); $coverName=trim($_POST['cover_key']??'');
            $fext=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
            if(!preg_match('#^books/[A-Za-z0-9._-]{1,90}$#',$fileName) || !in_array($fext,media_exts($type),true))
                $problem='The uploaded file is missing or not allowed. Please upload it again.';
            elseif(!preg_match('#^covers/[A-Za-z0-9._-]{1,40}\.jpg$#',$coverName))
                $problem='The cover image is missing. Please upload it again.';
            elseif(!storage_exists($fileName) || !storage_exists($coverName))
                $problem='The upload did not reach storage. Please try again.';
        } else {
            /* XAMPP: save into the local books/ and images/ folders */
            $file=$_FILES['book_file']??['error'=>UPLOAD_ERR_NO_FILE];
            $img=$_FILES['book_image']??['error'=>UPLOAD_ERR_NO_FILE];
            $fext=strtolower(pathinfo($file['name']??'',PATHINFO_EXTENSION));
            $iext=strtolower(pathinfo($img['name']??'',PATHINFO_EXTENSION));
            if($file['error']!==UPLOAD_ERR_OK)                              $problem=upload_error_text($file['error']);
            elseif($img['error']!==UPLOAD_ERR_OK)                           $problem=upload_error_text($img['error']);
            elseif(!in_array($fext,media_exts($type),true))                 $problem='For '.type_noun($type).' please upload one of: .'.implode(', .',media_exts($type)).'.';
            elseif(!in_array($iext,['jpg','jpeg','png','webp'],true))       $problem='The cover image must be a JPG, JPEG, PNG or WEBP file.';
            else {
                $fileName=uniqid().'_'.preg_replace('/[^A-Za-z0-9._-]+/','_',basename($file['name']));
                if(!move_uploaded_file($file['tmp_name'],local_dir('books').$fileName)){
                    $problem='Could not save the file. Check that the books folder exists and is writable.';
                } else {
                    $coverName=local_save_cover($img['tmp_name'],local_dir('images'));
                    if($coverName===false){ @unlink(local_dir('books').$fileName); $problem='The cover image could not be read. Use a real JPG, JPEG, PNG or WEBP image.'; }
                }
            }
        }

        if($problem!==''){ flash('danger',$problem); }
        else {
            /* generate the ID: use AUTO_INCREMENT if the column has it, otherwise highest ID + 1 */
            $colInfo=mysqli_fetch_assoc(mysqli_query($conn,"SHOW COLUMNS FROM books LIKE 'book_id'"));
            $auto=stripos($colInfo['Extra']??'','auto_increment')!==false;
            $next=0; $idCol=''; $idVal='';
            if(!$auto){
                $next=(int)mysqli_fetch_row(mysqli_query($conn,"SELECT COALESCE(MAX(CAST(book_id AS UNSIGNED)),0)+1 FROM books"))[0];
                $idCol='book_id,'; $idVal="'$next',";
            }
            $q=array_map(function($x) use($conn){ return esc($conn,$x); },$v);
            $sql="INSERT INTO books ($idCol book_title,author_name,edition,category,language,book_file,book_image,status,media_type) VALUES ($idVal '{$q['book_title']}','{$q['book_author']}','{$q['book_edition']}','{$q['book_category']}','{$q['book_language']}','".esc($conn,$fileName)."','".esc($conn,$coverName)."','active','$type');";
            try{
                if(mysqli_query($conn,$sql)){
                    $newId=$auto?mysqli_insert_id($conn):$next;
                    flash('success',ucfirst(type_noun($type)).' added to the library (ID '.$newId.').');
                    $v=array_map(function(){ return ''; },$v); $v['media_type']=$type;
                }
            }catch(Throwable $ex){
                remove_stored_file($fileName,'books'); remove_stored_file($coverName,'images');
                flash('danger','Could not save it to the database: '.$ex->getMessage());
            }
        }
    }
}
$allExt=['pdf'=>'.pdf','audio'=>'.'.implode(',.',media_exts('audio')),'video'=>'.'.implode(',.',media_exts('video'))];
include '../includes/admin_header.php';
?>
<div class="page-head"><h1>Add content</h1><p>Upload a PDF, an audio recording or a video with its details. The ID is generated automatically.</p></div>

<?php if($onVercel && !$useStorage): ?>
  <div class="alert alert-warning">File storage is not configured yet. Add the <code>S3_*</code> environment variables on the server, then redeploy.</div>
<?php endif; ?>

<form method="post" id="addForm" class="surface"<?= $useStorage ? '' : ' enctype="multipart/form-data"' ?>>
  <input type="hidden" name="content_key" id="content_key">
  <input type="hidden" name="cover_key" id="cover_key">
  <div class="mb-4">
    <label class="form-label req d-block">Content type</label>
    <div class="btn-group type-choice" role="group" aria-label="Content type">
      <?php foreach(media_types() as $k=>$l): ?>
        <input type="radio" class="btn-check" name="media_type" id="t-<?= $k ?>" value="<?= $k ?>"<?= $v['media_type']===$k?' checked':'' ?>>
        <label class="btn btn-outline-primary px-4" for="t-<?= $k ?>"><?= e($l) ?></label>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="row g-3">
    <div class="col-12"><label class="form-label req" for="ti">Title</label><input id="ti" name="book_title" class="form-control" value="<?= e($v['book_title']) ?>" required></div>
    <div class="col-md-6"><label class="form-label req" for="au">Author / presenter</label><input id="au" name="book_author" class="form-control" value="<?= e($v['book_author']) ?>" required></div>
    <div class="col-md-6"><label class="form-label req" for="ed">Edition</label><input id="ed" name="book_edition" class="form-control" value="<?= e($v['book_edition']) ?>" required></div>
    <div class="col-md-6"><label class="form-label req" for="ca">Category</label>
      <select id="ca" name="book_category" class="form-select" required><option value="">Select category</option><?php category_options($v['book_category']); ?></select></div>
    <div class="col-md-6"><label class="form-label req" for="la">Language</label>
      <select id="la" name="book_language" class="form-select" required><option value="">Select language</option>
        <?php foreach(['Tamil','English'] as $l): ?><option<?= $v['book_language']===$l?' selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></div>
    <div class="col-md-6">
      <label class="form-label" for="bf"><span id="file-label" class="req">PDF file</span><span class="info-i" tabindex="0">i<span class="tip" id="file-help">Accepted: .pdf</span></span></label>
      <input id="bf" type="file" class="form-control" accept=".pdf" required<?= $useStorage ? '' : ' name="book_file"' ?>>
    </div>
    <div class="col-md-6">
      <label class="form-label" for="bi"><span class="req">Cover image</span><span class="info-i" tabindex="0">i<span class="tip">JPG, JPEG, PNG or WEBP only. Any size: it is resized to fit the 600 × 800 px card without cropping, and spare space is filled with a soft blurred copy of the image.</span></span></label>
      <input id="bi" type="file" class="form-control" accept=".jpg,.jpeg,.png,.webp" required<?= $useStorage ? '' : ' name="book_image"' ?>>
    </div>
  </div>

  <div id="progressBox" class="mt-4 d-none">
    <div class="d-flex justify-content-between small mb-1"><span id="progressText">Preparing…</span><span id="progressPct">0%</span></div>
    <div class="progress" style="height:10px"><div id="progressBar" class="progress-bar" style="width:0%"></div></div>
  </div>
  <div id="errorBox" class="alert alert-danger mt-3 d-none mb-0"></div>

  <div class="d-flex gap-2 mt-4"><button type="submit" id="uploadBtn" class="btn btn-primary px-4">Upload</button><a href="listBooks.php" class="btn btn-soft">Cancel</a></div>
</form>

<script>
(function(){
  var form=document.getElementById('addForm'), btn=document.getElementById('uploadBtn');
  var fileIn=document.getElementById('bf'), coverIn=document.getElementById('bi');
  var box=document.getElementById('progressBox'), bar=document.getElementById('progressBar');
  var ptext=document.getElementById('progressText'), ppct=document.getElementById('progressPct'), errBox=document.getElementById('errorBox');
  var STORAGE=<?= json_encode($useStorage) ?>, LIMIT=<?= json_encode(ini_get('upload_max_filesize')) ?>;
  var ext=<?= json_encode($allExt) ?>, names={pdf:'PDF file',audio:'Audio file',video:'Video file'};

  function applyType(){
    var t=document.querySelector('input[name=media_type]:checked').value;
    fileIn.accept=ext[t];
    document.getElementById('file-label').textContent=names[t];
    document.getElementById('file-help').textContent='Accepted: '+ext[t].replace(/,/g,', ')+(STORAGE?'':'. Server limit: '+LIMIT+' per file.');
  }
  document.querySelectorAll('input[name=media_type]').forEach(function(r){ r.addEventListener('change',applyType); });
  applyType();

  function show(text,pct){ box.classList.remove('d-none'); ptext.textContent=text; ppct.textContent=pct+'%'; bar.style.width=pct+'%'; }
  function fail(msg){ errBox.textContent=msg; errBox.classList.remove('d-none'); box.classList.add('d-none'); btn.disabled=false; }

  /* resize the cover to 600 x 800 without cropping; empty space = soft blurred copy of the picture */
  async function makeCover(file){
    var bmp=await createImageBitmap(file);
    var W=600,H=800, c=document.createElement('canvas'); c.width=W; c.height=H;
    var g=c.getContext('2d');
    g.fillStyle='#ffffff'; g.fillRect(0,0,W,H);
    var cs=Math.max(W/bmp.width,H/bmp.height)*1.15, bw=bmp.width*cs, bh=bmp.height*cs;
    if('filter' in g){
      g.filter='blur(24px)'; g.drawImage(bmp,(W-bw)/2,(H-bh)/2,bw,bh); g.filter='none';
    } else {
      var t=document.createElement('canvas'); t.width=30; t.height=40;
      t.getContext('2d').drawImage(bmp,0,0,30,40);
      g.imageSmoothingEnabled=true; g.imageSmoothingQuality='high'; g.drawImage(t,0,0,W,H);
    }
    g.fillStyle='rgba(255,255,255,0.3)'; g.fillRect(0,0,W,H);
    var fs=Math.min(W/bmp.width,H/bmp.height), fw=bmp.width*fs, fh=bmp.height*fs;
    g.drawImage(bmp,(W-fw)/2,(H-fh)/2,fw,fh);
    return new Promise(function(res,rej){ c.toBlob(function(b){ b?res(b):rej(new Error('Could not process the cover image.')); },'image/jpeg',0.88); });
  }

  function put(url,body,type,onProgress){
    return new Promise(function(res,rej){
      var x=new XMLHttpRequest();
      x.open('PUT',url);
      x.setRequestHeader('Content-Type',type);
      x.upload.onprogress=function(e){ if(e.lengthComputable) onProgress(Math.round(e.loaded*100/e.total)); };
      x.onload=function(){ (x.status>=200 && x.status<300) ? res() : rej(new Error('Upload failed ('+x.status+'). Check the storage bucket settings (CORS).')); };
      x.onerror=function(){ rej(new Error('Upload was blocked. Check the storage bucket CORS settings and your internet connection.')); };
      x.send(body);
    });
  }

  if(!STORAGE) return;                      // XAMPP: normal form submit, the server saves the files
  form.addEventListener('submit',async function(ev){
    ev.preventDefault();
    errBox.classList.add('d-none');
    if(!form.checkValidity()){ form.reportValidity(); return; }
    var file=fileIn.files[0], img=coverIn.files[0];
    var type=document.querySelector('input[name=media_type]:checked').value;
    var imgExt=(img.name.split('.').pop()||'').toLowerCase();
    if(['jpg','jpeg','png','webp'].indexOf(imgExt)<0){ fail('The cover image must be a JPG, JPEG, PNG or WEBP file.'); return; }
    btn.disabled=true;
    try{
      show('Preparing the cover image…',2);
      var coverBlob=await makeCover(img);

      show('Getting upload permission…',5);
      var r=await fetch('presign.php',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({media_type:type,file_name:file.name})});
      var s={}; try{ s=await r.json(); }catch(e){}
      if(!r.ok) throw new Error(s.error||'Could not start the upload.');

      show('Uploading the cover…',8);
      await put(s.cover.url,coverBlob,'image/jpeg',function(){});

      await put(s.file.url,file,file.type||'application/octet-stream',function(p){ show('Uploading '+file.name+'…',10+Math.round(p*0.88)); });

      document.getElementById('content_key').value=s.file.key;
      document.getElementById('cover_key').value=s.cover.key;
      fileIn.disabled=true; coverIn.disabled=true;
      show('Saving…',100);
      form.submit();
    }catch(err){ fail(err.message||'Upload failed. Please try again.'); }
  });
})();
</script>
<?php include '../includes/admin_footer.php'; ?>
