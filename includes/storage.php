<?php
/* S3-compatible object storage (Cloudflare R2, Backblaze B2, Supabase Storage, AWS S3 ...).
   Settings come from environment variables: S3_ENDPOINT, S3_REGION, S3_BUCKET, S3_ACCESS_KEY, S3_SECRET_KEY, S3_PUBLIC_URL */

function storage_cfg(){
    static $c=null;
    if($c===null){
        $c=[
            'endpoint'=>rtrim((string)getenv('S3_ENDPOINT'),'/'),
            'region'  =>(string)(getenv('S3_REGION') ?: 'auto'),
            'bucket'  =>(string)getenv('S3_BUCKET'),
            'key'     =>(string)getenv('S3_ACCESS_KEY'),
            'secret'  =>(string)getenv('S3_SECRET_KEY'),
            'public'  =>rtrim((string)getenv('S3_PUBLIC_URL'),'/'),
        ];
    }
    return $c;
}
function storage_enabled(){
    $c=storage_cfg();
    return $c['endpoint']!=='' && $c['bucket']!=='' && $c['key']!=='' && $c['secret']!=='' && $c['public']!=='';
}
function storage_encode_key($key){ return implode('/',array_map('rawurlencode',explode('/',$key))); }

/* Time-limited signed URL (AWS Signature V4) so the BROWSER can PUT / the server can HEAD / DELETE */
function storage_presign($method,$key,$expires=900){
    $c=storage_cfg();
    $host=parse_url($c['endpoint'],PHP_URL_HOST);
    $port=parse_url($c['endpoint'],PHP_URL_PORT); if($port) $host.=':'.$port;
    $uri='/'.rawurlencode($c['bucket']).'/'.storage_encode_key($key);
    $now=time(); $amzDate=gmdate('Ymd\THis\Z',$now); $date=gmdate('Ymd',$now);
    $scope=$date.'/'.$c['region'].'/s3/aws4_request';
    $q=[
        'X-Amz-Algorithm'=>'AWS4-HMAC-SHA256',
        'X-Amz-Credential'=>$c['key'].'/'.$scope,
        'X-Amz-Date'=>$amzDate,
        'X-Amz-Expires'=>(string)$expires,
        'X-Amz-SignedHeaders'=>'host',
    ];
    ksort($q);
    $parts=[]; foreach($q as $k=>$v){ $parts[]=rawurlencode($k).'='.rawurlencode($v); }
    $canonQuery=implode('&',$parts);
    $canonReq=implode("\n",[$method,$uri,$canonQuery,"host:$host\n",'host','UNSIGNED-PAYLOAD']);
    $sts="AWS4-HMAC-SHA256\n$amzDate\n$scope\n".hash('sha256',$canonReq);
    $k=hash_hmac('sha256',$date,'AWS4'.$c['secret'],true);
    $k=hash_hmac('sha256',$c['region'],$k,true);
    $k=hash_hmac('sha256','s3',$k,true);
    $k=hash_hmac('sha256','aws4_request',$k,true);
    $sig=hash_hmac('sha256',$sts,$k);
    return $c['endpoint'].$uri.'?'.$canonQuery.'&X-Amz-Signature='.$sig;
}

function storage_request($method,$key){
    if(!function_exists('curl_init')) return 0;
    $ch=curl_init(storage_presign($method,$key,120));
    $opts=[CURLOPT_RETURNTRANSFER=>true,CURLOPT_TIMEOUT=>20];
    if($method==='HEAD') $opts[CURLOPT_NOBODY]=true; else $opts[CURLOPT_CUSTOMREQUEST]=$method;
    curl_setopt_array($ch,$opts);
    curl_exec($ch);
    $code=(int)curl_getinfo($ch,CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code;
}
function storage_exists($key){ return storage_request('HEAD',$key)===200; }
function storage_delete($key){ $c=storage_request('DELETE',$key); return $c===200 || $c===204 || $c===404; }

/* A stored value with a "/" is a storage key (books/xxx.pdf, covers/xxx.jpg). Anything else is an old local file name. */
function is_storage_key($v){ return strpos((string)$v,'/')!==false; }
function storage_public_url($key){ return storage_enabled() ? storage_cfg()['public'].'/'.storage_encode_key($key) : ''; }

function content_src($stored){
    $stored=trim((string)$stored);
    return is_storage_key($stored) ? storage_public_url($stored) : '../books/'.rawurlencode(basename($stored));
}
function cover_src($stored){
    $stored=trim((string)$stored);
    if($stored==='') return '';
    return is_storage_key($stored) ? storage_public_url($stored) : '../images/'.rawurlencode(basename($stored));
}
function remove_stored_file($stored,$localDir){
    $stored=trim((string)$stored);
    if($stored==='') return true;
    if(is_storage_key($stored)) return storage_enabled() ? storage_delete($stored) : false;
    $p=__DIR__.'/../'.$localDir.'/'.basename($stored);
    return is_file($p) ? @unlink($p) : true;
}

/* ---------- Local folder mode (XAMPP): used automatically when the S3_* settings are not present ---------- */
function local_dir($name){
    $d=__DIR__.'/../'.$name.'/';
    if(!is_dir($d)) @mkdir($d,0775,true);
    return $d;
}

/* Fit an uploaded cover inside 600 x 800 without cropping; spare space gets a smooth blurred copy. Saved as JPG.
   Needs PHP's GD extension (enabled by default in XAMPP). Returns the file name or false. */
function local_save_cover($tmp,$dir,$w=600,$h=800){
    $info=@getimagesize($tmp); if(!$info) return false;
    $type=$info[2];
    if(!in_array($type,[IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_WEBP],true)) return false;
    $base=uniqid('cover_');
    if(!function_exists('imagecreatetruecolor')){                  // GD off: keep the original file
        $name=$base.'.'.image_type_to_extension($type,false);
        return move_uploaded_file($tmp,$dir.$name)?$name:false;
    }
    switch($type){
        case IMAGETYPE_JPEG: $src=@imagecreatefromjpeg($tmp); break;
        case IMAGETYPE_PNG:  $src=@imagecreatefrompng($tmp); break;
        default:             $src=function_exists('imagecreatefromwebp')?@imagecreatefromwebp($tmp):false;
    }
    if(!$src) return false;
    if($type===IMAGETYPE_JPEG && function_exists('exif_read_data')){
        $ex=@exif_read_data($tmp); $o=$ex['Orientation']??1;
        if($o==3) $src=imagerotate($src,180,0); elseif($o==6) $src=imagerotate($src,-90,0); elseif($o==8) $src=imagerotate($src,90,0);
    }
    $sw=imagesx($src); $sh=imagesy($src);
    $dst=imagecreatetruecolor($w,$h);
    imagefill($dst,0,0,imagecolorallocate($dst,255,255,255));
    $cs=max($w/$sw,$h/$sh);
    $cw=max(1,(int)round($w/$cs)); $ch=max(1,(int)round($h/$cs));
    $sx=(int)(($sw-$cw)/2); $sy=(int)(($sh-$ch)/2);
    $bw=max(1,(int)round($w/4)); $bh=max(1,(int)round($h/4));
    $bg=imagecreatetruecolor($bw,$bh);
    imagefill($bg,0,0,imagecolorallocate($bg,255,255,255));
    imagecopyresampled($bg,$src,0,0,$sx,$sy,$bw,$bh,$cw,$ch);
    for($i=0;$i<30;$i++){ imagefilter($bg,IMG_FILTER_GAUSSIAN_BLUR); }
    $big=function_exists('imagescale') ? imagescale($bg,$w,$h,IMG_BICUBIC) : false;
    if($big){ imagecopy($dst,$big,0,0,0,0,$w,$h); imagedestroy($big); }
    else { imagecopyresampled($dst,$bg,0,0,0,0,$w,$h,$bw,$bh); }
    imagedestroy($bg);
    imagefilledrectangle($dst,0,0,$w,$h,imagecolorallocatealpha($dst,255,255,255,80));
    $fs=min($w/$sw,$h/$sh);
    $fw=max(1,(int)round($sw*$fs)); $fh=max(1,(int)round($sh*$fs));
    imagecopyresampled($dst,$src,(int)(($w-$fw)/2),(int)(($h-$fh)/2),0,0,$fw,$fh,$sw,$sh);
    $name=$base.'.jpg';
    $ok=imagejpeg($dst,$dir.$name,88);
    imagedestroy($src); imagedestroy($dst);
    return $ok?$name:false;
}
