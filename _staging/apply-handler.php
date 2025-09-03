<?php
declare(strict_types=1);

// Helpers
function post(string $k, $default = '') {
  return $_POST[$k] ?? $default;
}
function post_str(string $k): string {
  $v = trim((string)($_POST[$k] ?? ''));
  return $v;
}
function cleanline(string $v): string { return str_replace(["\r","\n"], ' ', $v); }
function bad_request(string $msg, int $code = 400): void {
  http_response_code($code);
  header('Content-Type: text/html; charset=UTF-8');
  echo "<!doctype html><meta charset='utf-8'><title>Error</title><body style=\"font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;max-width:760px;margin:40px auto;padding:0 16px;\"><h1>We couldn't submit your application</h1><p>" . htmlspecialchars($msg, ENT_QUOTES) . "</p><p><a href='/apply.php'>Return to the application</a></p></body>";
  exit;
}

// Method
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  bad_request('Invalid request method.', 405);
}

// Honeypot
if (post_str('_honey') !== '') {
  header('Location: /thank-you.html', true, 303);
  exit;
}

// No CSRF check (email disabled, honeypot only as requested)

// Collect & validate
$full_name   = post_str('full_name');
$age_raw     = post_str('age');
$email       = post_str('email');
$phone       = post_str('phone');
$city_zip    = post_str('city_zip');
$school_grade= post_str('school_grade');

$guardian_name         = post_str('guardian_name');
$guardian_relationship = post_str('guardian_relationship');
$guardian_phone        = post_str('guardian_phone');
$guardian_email        = post_str('guardian_email');

$eligibility = $_POST['eligibility'] ?? [];
if (!is_array($eligibility)) $eligibility = [];
$other_hardship = post_str('other_hardship');

$support = $_POST['support'] ?? [];
if (!is_array($support)) $support = [];

$why_bjj = post_str('why_bjj');
$goals   = post_str('goals');
$additional_info = post_str('additional_info');

$confirm_true        = post_str('confirm_true') === 'yes';
$confirm_attendance  = post_str('confirm_attendance') === 'yes';
$confirm_age         = post_str('confirm_age_approval') === 'yes';

// Server-side validations
if ($full_name === '') bad_request('Full name is required.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) bad_request('A valid email is required.');
$age_ok = ctype_digit($age_raw) && (int)$age_raw >= 4 && (int)$age_raw <= 26;
if (!$age_ok) bad_request('Age must be between 4 and 26.');
if ($phone === '') bad_request('Phone is required.');
if ($city_zip === '') bad_request('City and ZIP are required.');
if ($why_bjj === '' || $goals === '') bad_request('Please complete the short answers.');
if (!$confirm_true || !$confirm_attendance || !$confirm_age) bad_request('Please acknowledge all consent items.');

// Parse city/zip loosely: "City, 75025"
$city = $city_zip;
$zip  = '';
if (preg_match('/^\s*(.+?),\s*(\d{5})(?:-\d{4})?\s*$/', $city_zip, $m)) {
  $city = $m[1];
  $zip  = $m[2];
}

// Compose CSV row
$now = date('c');
$ip  = $_SERVER['REMOTE_ADDR'] ?? '';
$ua  = $_SERVER['HTTP_USER_AGENT'] ?? '';

$elig_str   = implode('|', array_map('strval', $eligibility));
$support_str= implode('|', array_map('strval', $support));

$row = [
  $now,
  cleanline($full_name),
  (string)((int)$age_raw),
  $email,
  cleanline($phone),
  cleanline($city),
  $zip,
  cleanline($school_grade),
  cleanline($guardian_name),
  cleanline($guardian_relationship),
  cleanline($guardian_phone),
  $guardian_email,
  $elig_str,
  cleanline($other_hardship),
  $support_str,
  cleanline($why_bjj),
  cleanline($goals),
  cleanline($additional_info),
  $confirm_true ? 'yes':'',
  $confirm_attendance ? 'yes':'',
  $confirm_age ? 'yes':'',
  $ip,
  cleanline($ua),
];

// Save to CSV (private backups)
$csvDir = __DIR__ . '/backups';
if (!is_dir($csvDir)) {@mkdir($csvDir, 0775, true);} // best-effort
// Deny web access to backups
@file_put_contents($csvDir . '/.htaccess', "Require all denied\nDeny from all\n");

$file = $csvDir . '/applications.csv';
$newFile = !file_exists($file);
$fh = @fopen($file, 'ab');
if (!$fh) {
  bad_request('Server was unable to save your application. Please email info@cobrascholarship.org. (Code: CSV) ', 500);
}
if ($newFile) {
  fputcsv($fh, [
    'timestamp','full_name','age','email','phone','city','zip','school_grade',
    'guardian_name','guardian_relationship','guardian_phone','guardian_email',
    'eligibility','other_hardship','support','why_bjj','goals','additional_info',
    'confirm_true','confirm_attendance','confirm_age_approval','ip','user_agent'
  ]);
}
fputcsv($fh, $row);
fclose($fh);

// Send notification email using PHP mail() (simple, no SMTP)
try {
  $toEmail = getenv('MAIL_TO') ?: 'info@cobrascholarship.org';
  $fromEmail = getenv('MAIL_FROM') ?: 'info@cobrascholarship.org';
  $subject = 'New Scholarship Application';

  $body = "New Cobra Scholarship application\n\n" .
    "Name: {$full_name}\n" .
    "Age: {$age_raw}\n" .
    "Email: {$email}\n" .
    "Phone: {$phone}\n" .
    "City/ZIP: {$city_zip}\n" .
    "School/Grade: {$school_grade}\n" .
    "Guardian: {$guardian_name} ({$guardian_relationship}) / {$guardian_email} / {$guardian_phone}\n\n" .
    "Eligibility: {$elig_str}\n" .
    "Other hardship: {$other_hardship}\n" .
    "Support: {$support_str}\n\n" .
    "Why BJJ:\n{$why_bjj}\n\n" .
    "Goals (6 months):\n{$goals}\n\n" .
    "Additional info:\n{$additional_info}\n\n" .
    "IP: {$ip}\nUA: {$ua}\n";

  $headers = 'From: ' . $fromEmail . "\r\n" .
             'Reply-To: ' . $email . "\r\n" .
             'Content-Type: text/plain; charset=UTF-8';
  @mail($toEmail, $subject, $body, $headers);
} catch (Throwable $e) {
  // Ignore email errors; CSV remains source of truth
}

// Success: redirect to thank you
header('Location: /thank-you.html', true, 303);
exit;
