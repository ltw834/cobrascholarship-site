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

require __DIR__ . '/vendor/autoload.php';
$conf = require __DIR__ . '/../secrets/smtp_config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host = $conf['host'];
  $mail->Port = $conf['port'];
  $mail->SMTPAuth = true;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Username = $conf['user'];
  $mail->Password = $conf['pass'];
  $mail->setFrom($conf['from_email'], $conf['from_name']);
  $mail->addAddress($conf['from_email']); // send to site inbox
  $mail->addReplyTo($email, $name);
  $mail->Subject = 'New Scholarship Application';
  $mail->Body = $body;
  $mail->send();
  $ok = true;
} catch (Exception $e) {
  $ok = false;
}


if ($ok){
  header("Location: /thank-you.html", true, 303);
  exit;
}
fail('Send failed');
