<?php
declare(strict_types=1);
session_start();
function post(string $k): string { return trim($_POST[$k] ?? ''); }

// CSRF + honeypot
$csrf = post('csrf');
if (!$csrf || !isset($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) { http_response_code(400); echo "Invalid session. Please reload the form."; exit; }
if (post('website') !== '') { header('Location: /thank-you.html', true, 303); exit; }

// Collect fields
$name=post('name'); $email=post('email'); $phone=post('phone'); $age=post('age'); $city=post('city'); $zip=post('zip');
$u18=post('u18'); $gname=post('guardian_name'); $gemail=post('guardian_email');

// Financial need
$need_frpl=post('need_frpl')==='yes'?'yes':''; $need_snap=post('need_snap')==='yes'?'yes':''; $need_medicaid=post('need_medicaid')==='yes'?'yes':'';
$need_housing=post('need_housing')==='yes'?'yes':''; $need_unemployment=post('need_unemployment')==='yes'?'yes':'';
$need_singleparent=post('need_singleparent')==='yes'?'yes':''; $need_other=post('need_other');

// Support requested (updated to mentorship)
$support_tuition=post('support_tuition')==='yes'?'yes':''; $support_uniform=post('support_uniform')==='yes'?'yes':'';
$support_competition=post('support_competition')==='yes'?'yes':''; $support_mentorship=post('support_mentorship')==='yes'?'yes':'';

$message=post('message'); $consent=post('consent');

// Minimal validation
$age_ok = ctype_digit($age) && (int)$age>=4 && (int)$age<=26;
if(!$name || !filter_var($email,FILTER_VALIDATE_EMAIL) || !$age_ok || !$city || !$zip || $consent!=='yes'){
  http_response_code(400); echo "Please complete required fields (name, email, age 4–26, city, ZIP, consent)."; exit;
}

// Save to CSV (private)
$csvDir = __DIR__.'/backups';
@mkdir($csvDir,0775,true);
$deny = $csvDir.'/.htaccess';
if(!file_exists($deny)){ @file_put_contents($deny,"Require all denied\nDeny from all\n"); }

$file = $csvDir.'/applications.csv';
$hasFile = file_exists($file);
$legacy = false;
if($hasFile){
  $rf=@fopen($file,'r');
  if($rf){ $hdr=fgetcsv($rf); fclose($rf); if(is_array($hdr) && in_array('support_transport',$hdr,true)) $legacy=true; }
}
$f=@fopen($file,'ab');
if(!$f){ http_response_code(500); echo "Server could not save your application. Please email info@cobrascholarship.org."; exit; }

if(!$hasFile){
  fputcsv($f, [
    'timestamp','name','email','phone','age','city','zip','under_18',
    'guardian_name','guardian_email',
    'need_frpl','need_snap','need_medicaid','need_housing','need_unemployment','need_singleparent','need_other',
    'support_tuition','support_uniform','support_competition','support_mentorship',
    'message','ip','ua'
  ]);
}

$rowCommon = [
  date('c'),
  str_replace(["\n","\r"],' ',$name),
  $email,
  str_replace(["\n","\r"],' ',$phone),
  $age,
  str_replace(["\n","\r"],' ',$city),
  str_replace(["\n","\r"],' ',$zip),
  $u18==='yes'?'yes':'',
  str_replace(["\n","\r"],' ',$gname),
  $gemail,
  $need_frpl,$need_snap,$need_medicaid,$need_housing,$need_unemployment,$need_singleparent,str_replace(["\n","\r"],' ',$need_other),
];

$tail = [
  str_replace(["\n","\r"],' ',substr($message,0,2000)),
  $_SERVER['REMOTE_ADDR'] ?? '',
  $_SERVER['HTTP_USER_AGENT'] ?? ''
];

if($legacy){
  fputcsv($f, array_merge($rowCommon, [$support_tuition,$support_uniform,$support_competition,$support_mentorship], $tail));
} else {
  fputcsv($f, array_merge($rowCommon, [$support_tuition,$support_uniform,$support_competition,$support_mentorship], $tail));
}
fclose($f);
header('Location: /thank-you.html', true, 303);
exit;
