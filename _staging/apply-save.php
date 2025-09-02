<?php
declare(strict_types=1);
session_start();
function post($k){return trim($_POST[$k]??'');}
if (isset($_POST['csrf'], $_SESSION['csrf']) && !hash_equals($_SESSION['csrf'], $_POST['csrf'])){http_response_code(400);echo "Invalid session.";exit;}
if (isset($_POST['website']) && $_POST['website']!==''){header('Location: /thank-you.html',true,303);exit;}
$name=post('name'); $email=post('email'); $phone=post('phone'); $academy=post('academy'); $message=post('message'); $consent=post('consent');
if (!$name || !filter_var($email,FILTER_VALIDATE_EMAIL) || !$academy || !$message){http_response_code(400);echo "Please complete all required fields.";exit;}
$dir=__DIR__.'/backups'; @mkdir($dir,0775,true); $deny=$dir.'/.htaccess'; if(!file_exists($deny)){@file_put_contents($deny,"Require all denied\nDeny from all\n");}
$file=$dir.'/applications.csv'; $new=!file_exists($file); $f=@fopen($file,'ab'); if(!$f){http_response_code(500);echo "Server could not save your application. Please email info@cobrascholarship.org.";exit;}
if($new){fputcsv($f,['timestamp','name','email','phone','academy','message','ip','ua']);}
fputcsv($f,[date('c'),str_replace(["\n","\r"],' ',$name),$email,str_replace(["\n","\r"],' ',$phone),str_replace(["\n","\r"],' ',$academy),str_replace(["\n","\r"],' ',substr($message,0,2000)),$_SERVER['REMOTE_ADDR']??'',$_SERVER['HTTP_USER_AGENT']??'']); fclose($f);
header('Location: /thank-you.html',true,303); exit;
