<?php
declare(strict_types=1);
header('Content-Type: text/plain; charset=utf-8');
$autoload = __DIR__ . '/vendor/autoload.php';
$confFile = __DIR__ . '/../secrets/smtp_config.php';
if (!is_file($autoload)) { http_response_code(500); echo "ERROR: vendor/autoload.php missing"; exit; }
if (!is_file($confFile)) { http_response_code(500); echo "ERROR: ../secrets/smtp_config.php missing"; exit; }
require $autoload;
$conf = require $confFile;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
$log = __DIR__ . '/smtp-debug.log';
$logger = function(string $str, int $level) use ($log) { @file_put_contents($log, gmdate('c')." [$level] $str\n", FILE_APPEND); };
$mail = new PHPMailer(true);
try {
  $mail->isSMTP();
  $mail->Host       = $conf['host'];          // mail.privateemail.com
  $mail->Port       = (int)$conf['port'];     // 587
  $mail->SMTPAuth   = true;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Username   = $conf['user'];          // info@cobrascholarship.org
  $mail->Password   = $conf['pass'];          // mailbox password
  $mail->SMTPDebug  = SMTP::DEBUG_SERVER;
  $mail->Debugoutput = $logger;
  $mail->setFrom($conf['from_email'], $conf['from_name']);
  $mail->addAddress($conf['from_email']);
  $mail->Subject = 'SMTP test from Cobra site';
  $mail->Body    = 'If you received this, SMTP works. ' . date('c');
  $mail->send();
  echo "OK";
} catch (\Throwable $e) {
  http_response_code(500);
  echo "ERR: " . $e->getMessage();
}
