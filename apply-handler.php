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
$full_name = v('full_name');
$age = v('age');
$email = v('email');
$phone = v('phone');
$city_zip = v('city_zip');
$school_grade = v('school_grade');

// Parent/Guardian info
$guardian_name = v('guardian_name');
$guardian_relationship = v('guardian_relationship');
$guardian_phone = v('guardian_phone');
$guardian_email = v('guardian_email');

// Eligibility (checkboxes)
$eligibility = isset($_POST['eligibility']) ? $_POST['eligibility'] : [];
$other_hardship = v('other_hardship');

// Support needed (checkboxes)
$support = isset($_POST['support']) ? $_POST['support'] : [];

// Short answers
$why_bjj = v('why_bjj');
$goals = v('goals');
$additional_info = v('additional_info');

// Consent
$confirm_true = v('confirm_true');
$confirm_attendance = v('confirm_attendance');
$confirm_age_approval = v('confirm_age_approval');

// Server-side required checks
foreach ([
    'full_name' => $full_name,
    'age' => $age,
    'email' => $email,
    'phone' => $phone,
    'city_zip' => $city_zip,
    'why_bjj' => $why_bjj,
    'goals' => $goals,
    'confirm_true' => $confirm_true,
    'confirm_attendance' => $confirm_attendance,
    'confirm_age_approval' => $confirm_age_approval
] as $k => $vval) {
    if ($vval === '') {
        http_response_code(400);
        exit('Missing required field: ' . $k);
    }
}

// Additional validation for age
if (!is_numeric($age) || $age < 4 || $age > 26) {
    http_response_code(400);
    exit('Age must be between 4 and 26');
}

// Check if guardian info is required and present
if ($age < 18) {
    foreach ([
        'guardian_name' => $guardian_name,
        'guardian_relationship' => $guardian_relationship,
        'guardian_phone' => $guardian_phone,
        'guardian_email' => $guardian_email
    ] as $k => $vval) {
        if ($vval === '') {
            http_response_code(400);
            exit('Missing required guardian field for minor: ' . $k);
        }
    }
}

// Build HTML table
$rows = [
    'A) Applicant Information' => [
        'Full Name' => $full_name,
        'Age' => $age,
        'Email' => $email,
        'Phone' => $phone,
        'City and ZIP' => $city_zip,
        'School & Grade' => $school_grade
    ],
    'B) Parent/Guardian Information' => [
        'Guardian Name' => $guardian_name,
        'Relationship to Applicant' => $guardian_relationship,
        'Guardian Phone' => $guardian_phone,
        'Guardian Email' => $guardian_email
    ],
    'C) Eligibility' => [
        'Eligibility Programs' => implode(", ", array_map(function($item) {
            $labels = [
                'frpl' => 'Free/Reduced-Price Lunch',
                'snap' => 'SNAP/EBT',
                'medicaid' => 'Medicaid/CHIP',
                'housing' => 'Housing Assistance',
                'benefits' => 'Unemployment/SSI/Disability',
                'single_parent' => 'Single-Parent Household'
            ];
            return $labels[$item] ?? $item;
        }, $eligibility)),
        'Other Hardship' => $other_hardship
    ],
    'D) Support Needed' => [
        'Types of Support' => implode(", ", array_map(function($item) {
            $labels = [
                'tuition' => 'Tuition Assistance',
                'uniform' => 'Uniform & Gear',
                'competition' => 'Competition Support',
                'mentorship' => 'Mentorship Program'
            ];
            return $labels[$item] ?? $item;
        }, $support))
    ],
    'E) Short Answers' => [
        'Why BJJ?' => $why_bjj,
        'Six Month Goals' => $goals,
        'Additional Information' => $additional_info
    ],
    'F) Consent' => [
        'Information is True' => $confirm_true ? 'Yes' : 'No',
        'Understands Requirements' => $confirm_attendance ? 'Yes' : 'No',
        'Age/Guardian Approval' => $confirm_age_approval ? 'Yes' : 'No'
    ]
];

$tbl = '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse">';
foreach ($rows as $section => $fields) {
    $tbl .= '<tr><th align="left" colspan="2" style="background:#e5e9f0;font-size:1.1em;padding:10px">'.esc($section).'</th></tr>';
    foreach ($fields as $label => $value) {
        if ($value !== '') {  // Only show non-empty fields
            $tbl .= '<tr><th align="left" style="background:#f6f8fb;">'.esc($label).'</th><td>'.nl2br_html($value).'</td></tr>';
        }
    }
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