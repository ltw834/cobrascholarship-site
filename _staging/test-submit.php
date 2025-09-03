<?php
// Temporary test endpoint to trigger a sample application submission.
// Usage: /test-submit.php?key=run-test-2025
// After verifying email + CSV, delete this file for safety.

declare(strict_types=1);

$allowedKey = 'run-test-2025'; // change if you like
$key = $_GET['key'] ?? '';
if ($key !== $allowedKey) {
  http_response_code(403);
  echo "Forbidden. Append ?key={$allowedKey} to run the test.";
  exit;
}

// Prepare a realistic payload
$post = [
  '_honey' => '',
  'full_name' => 'Test Submission — Please Ignore',
  'age' => '15',
  'email' => 'applicant+test@cobrascholarship.org',
  'phone' => '555-000-1234',
  'city_zip' => 'Plano, 75025',
  'school_grade' => 'Plano Senior High, 10th grade',
  'guardian_name' => 'Test Parent',
  'guardian_relationship' => 'Mother',
  'guardian_phone' => '555-000-5678',
  'guardian_email' => 'parent+test@cobrascholarship.org',
  'eligibility' => ['frpl','single_parent'],
  'other_hardship' => 'Temporary income loss',
  'support' => ['tuition','uniform'],
  'why_bjj' => 'I want to build confidence and discipline.',
  'goals' => 'Attend 3 classes per week and compete once.',
  'additional_info' => 'No additional info.',
  'confirm_true' => 'yes',
  'confirm_attendance' => 'yes',
  'confirm_age_approval' => 'yes',
];

// Post to the live handler
$endpoint = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/apply-handler.php';
$opts = [
  'http' => [
    'method' => 'POST',
    'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
    'content' => http_build_query($post),
    'ignore_errors' => true,
    'timeout' => 10,
  ]
];

$context = stream_context_create($opts);
$resp = @file_get_contents($endpoint, false, $context);
$statusLine = $http_response_header[0] ?? '';

header('Content-Type: text/plain; charset=UTF-8');
echo "Triggered sample application.\n";
echo "Handler response: " . ($statusLine ?: 'unknown') . "\n";
echo "Check your inbox (info@cobrascholarship.org) and CSV at /public_html/backups/applications.csv.\n";
echo "Reminder: remove _staging/test-submit.php after confirming.\n";

