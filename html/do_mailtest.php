<?php
if (($_GET['k'] ?? '') !== 'mailtest2026') { http_response_code(403); exit; }
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/config.php';

$smtp_host = get_setting('smtp_host', getenv('SMTP_HOST') ?: '');
$smtp_user = get_setting('smtp_user', getenv('SMTP_USER') ?: '');
$smtp_pass = get_setting('smtp_pass', getenv('SMTP_PASS') ?: '');
$smtp_port = (int)(get_setting('smtp_port', getenv('SMTP_PORT') ?: '587') ?: 587);

echo "SMTP host: " . ($smtp_host ?: '(BOŞ)') . "\n";
echo "SMTP user: " . ($smtp_user ?: '(BOŞ)') . "\n";
echo "SMTP port: " . $smtp_port . "\n";
echo "Vendor autoload: " . (file_exists(__DIR__ . '/vendor/autoload.php') ? 'VAR' : 'YOK') . "\n";
echo "----\n";

$to = 'hello@abdurrahmankaya.com';
$body = "<html><body style='font-family:Arial,sans-serif;color:#333'>"
      . "<h2 style='color:#6366f1'>Test maili ✅</h2>"
      . "<p>Bu bir test mailidir. Eğer bu maili Gmail kutunda görüyorsan, <b>hello@abdurrahmankaya.com adresine gelen mailler başarıyla Gmail'ine düşüyor</b> demektir.</p>"
      . "<p>Gönderim zamanı: " . date('d.m.Y H:i:s') . "</p>"
      . "<hr><p style='color:#999;font-size:12px'>abdurrahmankaya.com mail kurulum testi</p>"
      . "</body></html>";

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
        $mail->setFrom($smtp_user, 'Mail Testi');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'Test — hello@ kutusu calisiyor mu?';
        $mail->Body    = $body;
        $mail->send();
        echo "SONUC: BASARILI — '$to' adresine test maili gonderildi.\n";
        echo "Simdi Gmail kutunu kontrol et (birkac dakika surebilir, spam klasorune de bak).\n";
    } catch (Throwable $e) {
        echo "SONUC: HATA — " . $e->getMessage() . "\n";
    }
} else {
    echo "SONUC: SMTP ayarlari eksik, mail gonderilemedi.\n";
}
