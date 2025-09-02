<?php
session_start();
function fail($m){ http_response_code(400); exit($m); }
if ($_SERVER['REQUEST_METHOD']!=='POST') fail('Invalid method');
if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) fail('CSRF');
if (!empty($_POST['website'])) fail('Bot detected');
if (time() - (int)($_POST['ts'] ?? 0) < 3) fail('Too fast');

$name = trim($_POST['name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$msg = trim($_POST['message'] ?? '');
if (!$name || !$email || !$msg) fail('Missing fields');

$body = "Name: $name\nEmail: $email\n\n$msg\n";

/*
  TODO: send email via SMTP (PHPMailer). Do NOT hardcode secrets.
  Placeholder success for now:
*/
$ok = true;

if ($ok){
  header("Location: /thank-you.html", true, 303);
  exit;
}
fail('Send failed');
