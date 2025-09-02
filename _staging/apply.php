<?php
session_start();
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cobra Scholarship – Apply</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="preload" href="/critical.min.css" as="style" onload="this.rel='stylesheet'">
  <noscript><link rel="stylesheet" href="/critical.min.css"></noscript>
</head>
<body>
  <main>
    <h1>Apply for the Cobra Youth Scholarship</h1>
    <form method="post" action="/apply-handler.php" novalidate>
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($_SESSION['csrf']); ?>">
      <!-- honeypot field for bots -->
      <input type="text" name="website" autocomplete="off" tabindex="-1"
             style="position:absolute;left:-5000px" aria-hidden="true">
      <input type="hidden" name="ts" value="<?php echo time(); ?>">
      <p><label>Full name<br><input required name="name"></label></p>
      <p><label>Email<br><input required type="email" name="email"></label></p>
      <p><label>Message<br><textarea required name="message" rows="6"></textarea></label></p>
      <button type="submit">Submit</button>
    </form>
  </main>
</body>
</html>
