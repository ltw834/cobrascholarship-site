<?php
// apply-handler.php — plain PHP mail(), no third-party services
// Place this file in: public_html/apply-handler.php
// The form posts here with method="POST"

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method Not Allowed'); }
if (!empty($_POST['_honey'])) { header('Location: /thank-you.html'); exit; } // spam trap

function v($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : ''; }
function esc($s){ return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function nl2br_html($s){ return nl2br(esc($s)); }

// Collect inputs
$student_full_name = v('student_full_name');
$student_dob       = v('student_dob');
$school_grade      = v('school_grade');
$student_phone     = v('student_phone');
$parent_name       = v('parent_name');
$email             = v('email');
$parent_phone      = v('parent_phone');
$home_address      = v('home_address');
$trained_before    = v('trained_before');
$training_details  = v('training_details');
$motivation        = v('motivation');
$income_range      = v('income_range');
$household_size    = v('household_size');
$need_reason       = v('need_reason');
$commit_attendance = v('commit_attendance');
$confirm_location_age = v('confirm_location_age');
$commit_character  = v('commit_character');
$signature         = v('signature');
$signature_date    = v('signature_date');
$consent           = v('consent');

// Server-side required checks
foreach ([
  'student_full_name'=>$student_full_name,'student_dob'=>$student_dob,'parent_name'=>$parent_name,
  'email'=>$email,'parent_phone'=>$parent_phone,'home_address'=>$home_address,
  'motivation'=>$motivation,'income_range'=>$income_range,'need_reason'=>$need_reason,
  'commit_attendance'=>$commit_attendance,'confirm_location_age'=>$confirm_location_age,
  'commit_character'=>$commit_character,'signature'=>$signature,'signature_date'=>$signature_date,'consent'=>$consent
] as $k=>$vval){
  if ($vval===''){ http_response_code(400); exit('Missing required field: '.$k); }
}

// Build HTML table
$rows = [
  'Student Name'=>$student_full_name, 'DOB'=>$student_dob, 'School/Grade'=>$school_grade,
  'Student Phone'=>$student_phone, 'Contact Name'=>$parent_name, 'Email'=>$email,
  'Phone'=>$parent_phone, 'Home Address'=>$home_address, 'Trained Before'=>$trained_before,
  'Training Details'=>$training_details, 'Motivation'=>$motivation,
  'Household Income'=>$income_range, 'Household Size'=>$household_size,
  'Reason for Need'=>$need_reason, 'Commit Attendance'=>$commit_attendance?'Yes':'No',
  'Confirm Location/Age'=>$confirm_location_age?'Yes':'No',
  'Commit Character'=>$commit_character?'Yes':'No',
  'Signature'=>$signature, 'Signature Date'=>$signature_date, 'Consent'=>$consent?'Yes':'No'
];

$tbl = '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse">';
foreach ($rows as $label=>$value){
  $tbl .= '<tr><th align="left" style="background:#f6f8fb;">'.esc($label).'</th><td>'.nl2br_html($value).'</td></tr>';
}
$tbl .= '</table>';

$to       = 'info@cobrascholarship.org';  // where applications are delivered
$subject  = 'New Scholarship Application';
$from     = 'no-reply@cobrascholarship.org'; // MUST be a mailbox on your domain (created in cPanel)
$headers  = [
  'MIME-Version: 1.0',
  'Content-Type: text/html; charset=UTF-8',
  'From: Cobra Scholarship <'.$from.'>',
  'Reply-To: '.esc($email),
  'X-Mailer: PHP/'.phpversion()
];
$envelopeSender = '-f '.$from;

// Send
$ok = @mail($to, $subject, '<h2>New Scholarship Application</h2>'.$tbl, implode("\r\n", $headers), $envelopeSender);

// Optional logging for troubleshooting (disabled)
// @file_put_contents(__DIR__.'/apply-mail.log', date('c').' :: '.($ok?'SENT':'FAILED').PHP_EOL, FILE_APPEND);

// Redirect to thank-you page either way
header('Location: /thank-you.html');
exit;