<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit;
}

// Honeypot: gizli alan dolu ise bot
if (!empty($_POST['website'])) {
    echo json_encode(['success' => true]); exit;
}

// Rate limiting: IP başına 5 dakikada 3 istek
session_start();
$now  = time();
$hits = $_SESSION['contact_hits'] ?? [];
$hits = array_filter($hits, fn($t) => $now - $t < 300);
if (count($hits) >= 3) {
    http_response_code(429); echo json_encode(['error' => 'Çok fazla istek. Lütfen birkaç dakika bekleyin.']); exit;
}
$hits[] = $now;
$_SESSION['contact_hits'] = $hits;

// Input doğrulama
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? 'İletişim Formu');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message) {
    http_response_code(400); echo json_encode(['error' => 'Ad, e-posta ve mesaj zorunludur.']); exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400); echo json_encode(['error' => 'Geçersiz e-posta adresi.']); exit;
}
if (mb_strlen($name) > 100 || mb_strlen($subject) > 200 || mb_strlen($message) > 5000) {
    http_response_code(400); echo json_encode(['error' => 'Girilen veriler çok uzun.']); exit;
}

$to_email = get_setting('site_email', getenv('CONTACT_EMAIL') ?: '');
if (!$to_email) {
    http_response_code(500); echo json_encode(['error' => 'Alıcı e-posta adresi yapılandırılmamış.']); exit;
}

// SMTP ayarları
$smtp_host = getenv('SMTP_HOST') ?: '';
$smtp_user = getenv('SMTP_USER') ?: '';
$smtp_pass = getenv('SMTP_PASS') ?: '';
$smtp_port = (int)(getenv('SMTP_PORT') ?: 587);

$body = "
<html><body style='font-family:Arial,sans-serif;color:#333'>
<h2 style='color:#6366f1'>Yeni İletişim Mesajı</h2>
<table cellpadding='8' style='border-collapse:collapse;width:100%'>
<tr><td style='font-weight:bold;width:120px'>Ad Soyad:</td><td>" . htmlspecialchars($name) . "</td></tr>
<tr><td style='font-weight:bold'>E-posta:</td><td><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></td></tr>
<tr><td style='font-weight:bold'>Konu:</td><td>" . htmlspecialchars($subject) . "</td></tr>
<tr><td style='font-weight:bold;vertical-align:top'>Mesaj:</td><td>" . nl2br(htmlspecialchars($message)) . "</td></tr>
</table>
<hr style='border:1px solid #eee;margin-top:20px'>
<p style='color:#999;font-size:12px'>Bu mesaj abdurrahmankaya.com iletişim formu üzerinden gönderildi.</p>
</body></html>";

$sent = false;

if ($smtp_host && $smtp_user && $smtp_pass && file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $smtp_host;
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtp_user;
        $mail->Password   = $smtp_pass;
        $mail->SMTPSecure = $smtp_port === 465 ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $smtp_port;
        $mail->CharSet    = 'UTF-8';
        $mail->setFrom($smtp_user, get_setting('site_name', 'Abdurrahman Kaya'));
        $mail->addAddress($to_email);
        $mail->addReplyTo($email, $name);
        $mail->isHTML(true);
        $mail->Subject = "[abdurrahmankaya.com] $subject";
        $mail->Body    = $body;
        $mail->send();
        $sent = true;
    } catch (Throwable $e) {
        error_log('Mail error: ' . $e->getMessage());
    }
} else {
    // PHP mail() fallback
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . get_setting('site_name') . " <noreply@abdurrahmankaya.com>\r\n";
    $headers .= "Reply-To: $name <$email>\r\n";
    $sent = @mail($to_email, "[abdurrahmankaya.com] $subject", $body, $headers);
}

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Mesajınız alındı, en kısa sürede dönüş yapacağım.']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Mesaj gönderilemedi. Lütfen doğrudan e-posta ile ulaşın.']);
}
