<?php
header('Content-Type: application/json');

// CONFIG
$to = "ledlutece@gmail.com"; // recipient
$domainFrom = $_SERVER['SERVER_NAME'] ?? 'website';
$subjectPrefix = "Led Lutece Contact";

// Basic validation + sanitization
function field($key){
  return isset($_POST[$key]) ? trim(strip_tags($_POST[$key])) : "";
}

$name = field('name');
$email = filter_var(field('email'), FILTER_VALIDATE_EMAIL) ? field('email') : "";
$phone = field('phone');
$company = field('company');
$subjectLine = field('subject');
$message = field('message');
$honeypot = field('website'); // should be empty

if ($honeypot) { echo json_encode(["ok"=>true]); exit; } // silently ignore bots

if (!$name || !$email || !$subjectLine || !$message){
  http_response_code(400);
  echo json_encode(["ok"=>false, "error"=>"Missing required fields."]);
  exit;
}

// Compose email
$subject = "$subjectPrefix: $subjectLine";
$body  = "New inquiry from Led Lutece website\n\n";
$body .= "Name: $name\n";
$body .= "Email: $email\n";
$body .= "Phone: $phone\n";
$body .= "Company: $company\n";
$body .= "Subject: $subjectLine\n\n";
$body .= "Message:\n$message\n\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . "\n";

$headers = [];
$headers[] = "From: Led Lutece <noreply@$domainFrom>";
$headers[] = "Reply-To: $name <$email>";
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";

$ok = @mail($to, $subject, $body, implode("\r\n", $headers));

if ($ok){
  echo json_encode(["ok"=>true]);
} else {
  http_response_code(500);
  echo json_encode(["ok"=>false, "error"=>"Mailer not configured on server. See contact.php for SMTP instructions."]);
}
?>

<?php
/*  SMTP OPTION (recommended):
 *  Use PHPMailer with your Gmail (App Password) or your SMTP provider.
 *  1) Install PHPMailer (via Composer or copy library): https://github.com/PHPMailer/PHPMailer
 *  2) Replace the mail() block above with the following (and include PHPMailer classes):
 *
 *  use PHPMailer\PHPMailer\PHPMailer;
 *  use PHPMailer\PHPMailer\Exception;
 *  require 'phpmailer/src/Exception.php';
 *  require 'phpmailer/src/PHPMailer.php';
 *  require 'phpmailer/src/SMTP.php';
 *
 *  $mail = new PHPMailer(true);
 *  try{
 *    $mail->isSMTP();
 *    $mail->Host = 'smtp.gmail.com';
 *    $mail->SMTPAuth = true;
 *    $mail->Username = 'ledlutece@gmail.com';
 *    $mail->Password = 'YOUR_APP_PASSWORD_HERE';
 *    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
 *    $mail->Port = 587;
 *
 *    $mail->setFrom('ledlutece@gmail.com','Led Lutece Website');
 *    $mail->addAddress('ledlutece@gmail.com');
 *    $mail->addReplyTo($email, $name);
 *    $mail->Subject = $subject;
 *    $mail->Body    = $body;
 *
 *    $mail->send();
 *    echo json_encode(["ok"=>true]);
 *  } catch (Exception $e){
 *    http_response_code(500);
 *    echo json_encode(["ok"=>false, "error"=>"SMTP error: {$mail->ErrorInfo}"]);
 *  }
 */
?>