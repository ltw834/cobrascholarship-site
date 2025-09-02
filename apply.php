<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Apply for a Cobra Youth Scholarship</title>
  <meta name="description" content="Apply for a Brazilian Jiu-Jitsu scholarship for underserved youth in North Dallas/Plano." />
  <link rel="icon" type="image/png" href="/favicon.png">
  <style>
    :root{
      --bg:#f5f7fb; --panel:#fff; --ink:#0b1220; --muted:#6b7380;
      --ring:rgba(22,163,74,.18); --brand:#16a34a; --brand-600:#15803d;
      --line:#e8edf3; --radius:18px; --maxw:960px;
    }
    *{box-sizing:border-box}
    html,body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.6 system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial,sans-serif}
    .wrap{max-width:var(--maxw);margin:48px auto;padding:0 20px}
    header h1{font-size:38px;line-height:1.15;margin:0 0 6px}
    header p{color:var(--muted);margin:0 0 20px}
    .card{background:var(--panel);border:1px solid var(--line);border-radius:24px;box-shadow:0 12px 36px rgba(10,18,31,.06);padding:26px}
    fieldset{border:0;margin:0 0 24px;padding:0}
    legend{font-weight:800;margin:0 0 10px;font-size:20px}
    .row{display:grid;grid-template-columns:1fr;gap:14px}
    @media (min-width:760px){.row.two{grid-template-columns:1fr 1fr}}
    label{font-weight:650;margin:10px 0 6px;display:block}
    .hint{color:var(--muted);font-size:.92rem;margin-top:2px}
    input[type=text],input[type=date],input[type=email],input[type=tel],select,textarea{
      width:100%;border:1px solid #d9e0e8;border-radius:12px;padding:12px 14px;
      background:#fff;outline:none;transition:border .15s, box-shadow .15s;appearance:none
    }
    textarea{min-height:120px;resize:vertical}
    input:focus,select:focus,textarea:focus{border-color:var(--brand);box-shadow:0 0 0 6px var(--ring)}
    select{
      background-image:linear-gradient(45deg,transparent 50%,#8a94a4 50%),linear-gradient(135deg,#8a94a4 50%,transparent 50%),linear-gradient(to right,#fff,#fff);
      background-position:calc(100% - 18px) calc(1em - 2px), calc(100% - 12px) calc(1em - 2px), 100% 0;
      background-size:6px 6px,6px 6px,2.5em 100%;background-repeat:no-repeat
    }
    .checks{display:flex;flex-direction:column;gap:10px;margin-top:8px}
    .check{display:flex;gap:12px;align-items:flex-start}
    .check input{margin-top:3px}
    .actions{margin-top:22px;display:flex;gap:12px;align-items:center}
    button{background:var(--brand);color:#fff;border:0;border-radius:14px;padding:12px 18px;font-weight:800;cursor:pointer}
    button:hover{background:var(--brand-600)}
    .req{color:#c026d3;font-weight:800;margin-left:3px}
    .note{color:var(--muted);font-size:.9rem}
  </style>
</head>
<body>
  <main class="wrap">
    <header>
      <h1>Apply for a Cobra Youth Scholarship</h1>
      <p>Complete this application. If selected and funding is available, we'll contact you. Fields marked <span class="req">*</span> are required.</p>
    </header>

    <section class="card">
      <form action="/apply-handler.php" method="POST" accept-charset="UTF-8">
        <input type="text" name="_honey" style="display:none" aria-hidden="true">

        <!-- A) Applicant Info -->
        <fieldset>
          <legend>A) Applicant Information</legend>
          <div class="row">
            <div>
              <label for="full_name">Full Name <span class="req">*</span></label>
              <input id="full_name" name="full_name" type="text" required>
            </div>
          </div>
          <div class="row two">
            <div>
              <label for="age">Age <span class="req">*</span></label>
              <input id="age" name="age" type="number" min="4" max="26" required>
              <div class="hint">Must be between 4 and 26 years old</div>
            </div>
            <div>
              <label for="email">Email <span class="req">*</span></label>
              <input id="email" name="email" type="email" required>
            </div>
          </div>
          <div class="row two">
            <div>
              <label for="phone">Phone <span class="req">*</span></label>
              <input id="phone" name="phone" type="tel" required>
            </div>
            <div>
              <label for="city_zip">City and ZIP <span class="req">*</span></label>
              <input id="city_zip" name="city_zip" type="text" required placeholder="e.g., Plano, 75025">
            </div>
          </div>
          <div class="row">
            <div>
              <label for="school_grade">School & Grade (if in school)</label>
              <input id="school_grade" name="school_grade" type="text" placeholder="e.g., Plano Senior High, 11th grade">
            </div>
          </div>
        </fieldset>

        <!-- B) Parent/Guardian -->
        <fieldset>
          <legend>B) Parent/Guardian Information (If Under 18)</legend>
          <div class="row two">
            <div>
              <label for="guardian_name">Parent/Guardian Full Name <span class="req">*</span></label>
              <input id="guardian_name" name="guardian_name" type="text">
              <div class="hint">Required if applicant is under 18</div>
            </div>
            <div>
              <label for="guardian_relationship">Relationship to Applicant <span class="req">*</span></label>
              <input id="guardian_relationship" name="guardian_relationship" type="text" placeholder="e.g., Mother, Father, Legal Guardian">
            </div>
          </div>
          <div class="row two">
            <div>
              <label for="guardian_phone">Parent/Guardian Phone <span class="req">*</span></label>
              <input id="guardian_phone" name="guardian_phone" type="tel">
            </div>
            <div>
              <label for="guardian_email">Parent/Guardian Email <span class="req">*</span></label>
              <input id="guardian_email" name="guardian_email" type="email">
            </div>
          </div>
        </fieldset>

        <!-- C) Eligibility -->
        <fieldset>
          <legend>C) Eligibility Snapshot</legend>
          <p class="hint">Check all that apply</p>
          <div class="checks">
            <label class="check">
              <input type="checkbox" name="eligibility[]" value="frpl">
              <span>Free/Reduced-Price Lunch (FRPL)</span>
            </label>
            <label class="check">
              <input type="checkbox" name="eligibility[]" value="snap">
              <span>SNAP / EBT</span>
            </label>
            <label class="check">
              <input type="checkbox" name="eligibility[]" value="medicaid">
              <span>Medicaid / CHIP</span>
            </label>
            <label class="check">
              <input type="checkbox" name="eligibility[]" value="housing">
              <span>Housing assistance (e.g., Section 8)</span>
            </label>
            <label class="check">
              <input type="checkbox" name="eligibility[]" value="benefits">
              <span>Unemployment / SSI / disability benefits</span>
            </label>
            <label class="check">
              <input type="checkbox" name="eligibility[]" value="single_parent">
              <span>Single-parent household</span>
            </label>
          </div>
          <div class="row" style="margin-top: 14px;">
            <div>
              <label for="other_hardship">Other financial hardship (brief note)</label>
              <input id="other_hardship" name="other_hardship" type="text">
            </div>
          </div>
        </fieldset>

        <!-- D) Support Needed -->
        <fieldset>
          <legend>D) What support do you need?</legend>
          <p class="hint">Check all that apply</p>
          <div class="checks">
            <label class="check">
              <input type="checkbox" name="support[]" value="tuition">
              <span>Tuition assistance</span>
            </label>
            <label class="check">
              <input type="checkbox" name="support[]" value="uniform">
              <span>Uniform & gear</span>
            </label>
            <label class="check">
              <input type="checkbox" name="support[]" value="competition">
              <span>Competition support</span>
            </label>
            <label class="check">
              <input type="checkbox" name="support[]" value="mentorship" checked>
              <span>Mentorship program</span>
            </label>
          </div>
        </fieldset>

        <!-- E) Short Answers -->
        <fieldset>
          <legend>E) Short Answers</legend>
          <div class="row">
            <div>
              <label for="why_bjj">Why do you want to train Brazilian Jiu-Jitsu? <span class="req">*</span></label>
              <textarea id="why_bjj" name="why_bjj" required placeholder="2-3 sentences"></textarea>
            </div>
          </div>
          <div class="row">
            <div>
              <label for="goals">What goals would you like to reach in the next 6 months? <span class="req">*</span></label>
              <textarea id="goals" name="goals" required placeholder="2-3 sentences"></textarea>
            </div>
          </div>
          <div class="row">
            <div>
              <label for="additional_info">Anything else we should know? (optional)</label>
              <textarea id="additional_info" name="additional_info" placeholder="2-3 sentences"></textarea>
            </div>
          </div>
        </fieldset>

        <!-- F) Consent -->
        <fieldset>
          <legend>F) Consent & Acknowledgments</legend>
          <div class="checks">
            <label class="check">
              <input type="checkbox" name="confirm_true" value="yes" required>
              <span>I confirm the information provided is true. <span class="req">*</span></span>
            </label>
            <label class="check">
              <input type="checkbox" name="confirm_attendance" value="yes" required>
              <span>I understand regular attendance and positive behavior are required. <span class="req">*</span></span>
            </label>
            <label class="check">
              <input type="checkbox" name="confirm_age_approval" value="yes" required>
              <span>I am 18+ or my parent/guardian listed above approves this application. <span class="req">*</span></span>
            </label>
          </div>
        </fieldset>

        <div class="actions">
          <button type="submit">Submit Application</button>
          <span class="note">You'll be redirected to a confirmation page after submission.</span>
        </div>
      </form>
    </section>
  </main>
</body>
</html>