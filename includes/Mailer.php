<?php
/**
 * Mailer Class - SSL Fixed for Gmail
 */

// Include PHPMailer files directly
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private $config;
    private $mail;
    
    public function __construct($config = []) {
        $this->config = $config;
        $this->initializeMailer();
    }
    
    private function initializeMailer() {
        $this->mail = new PHPMailer(true);
        
        try {
            // Gmail SMTP settings
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp_host'];
            $this->mail->SMTPAuth = $this->config['smtp_auth'];
            $this->mail->Username = $this->config['smtp_username'];
            $this->mail->Password = $this->config['smtp_password'];
            
            // Use SSL encryption for port 465
            if ($this->config['smtp_secure'] === 'ssl') {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;  // SSL
            } else {
                $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS
            }
            
            $this->mail->Port = $this->config['smtp_port'];
            
            // SSL context options for Gmail
            $this->mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Debug settings
            $this->mail->SMTPDebug = $this->config['debug_level'] ?? 0;
            
            // Content settings
            $this->mail->isHTML(true);
            $this->mail->CharSet = $this->config['charset'];
            
            // From address
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
            
        } catch (Exception $e) {
            error_log('Mailer initialization failed: ' . $e->getMessage());
            throw new Exception('Email system unavailable: ' . $e->getMessage());
        }
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordReset($to_email, $to_name, $reset_link, $language = 'sv') {
        try {
            // Get language strings
            $strings = $this->getPasswordResetStrings($language);
            
            // Prepare email
            $this->mail->clearAddresses();
            $this->mail->addAddress($to_email, $to_name);
            
            $this->mail->Subject = $strings['subject'];
            
            // Create email content
            $html_body = $this->createPasswordResetHTML($to_name, $reset_link, $strings);
            $text_body = $this->createPasswordResetText($to_name, $reset_link, $strings);
            
            $this->mail->Body = $html_body;
            $this->mail->AltBody = $text_body;
            
            // Send email
            $result = $this->mail->send();
            
            if ($result) {
                return [
                    'success' => true,
                    'message' => $strings['email_sent']
                ];
            } else {
                throw new Exception('Failed to send email');
            }
            
        } catch (Exception $e) {
            error_log('Password reset email failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Email kunde inte skickas. Fel: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function getPasswordResetStrings($language) {
        $strings = [
            'sv' => [
                'subject' => 'Återställ ditt lösenord - Karis Antikvariat',
                'greeting' => 'Hej',
                'intro' => 'Du har begärt att återställa ditt lösenord för ditt konto på Karis Antikvariat.',
                'instruction' => 'Klicka på länken nedan för att ställa in ett nytt lösenord:',
                'button_text' => 'Återställ lösenord',
                'expire_info' => 'Denna länk är giltig i 2 timmar.',
                'no_request' => 'Om du inte begärde denna återställning kan du ignorera detta e-postmeddelande.',
                'footer' => 'Med vänliga hälsningar,<br>Karis Antikvariat',
                'email_sent' => 'Återställningslänk skickad till din e-post.',
                'alt_instruction' => 'Om knappen inte fungerar, kopiera och klistra in denna länk i din webbläsare:'
            ],
            'fi' => [
                'subject' => 'Palauta salasanasi - Karis Antikvariat',
                'greeting' => 'Hei',
                'intro' => 'Olet pyytänyt salasanan palautusta Karis Antikvariat -tilillesi.',
                'instruction' => 'Napsauta alla olevaa linkkiä asettaaksesi uuden salasanan:',
                'button_text' => 'Palauta salasana',
                'expire_info' => 'Tämä linkki on voimassa 2 tuntia.',
                'no_request' => 'Jos et pyytänyt tätä palautusta, voit jättää tämän sähköpostin huomiotta.',
                'footer' => 'Ystävällisin terveisin,<br>Karis Antikvariat',
                'email_sent' => 'Palautuslinkki lähetetty sähköpostiisi.',
                'alt_instruction' => 'Jos painike ei toimi, kopioi ja liitä tämä linkki selaimeen:'
            ]
        ];
        
        return $strings[$language] ?? $strings['sv'];
    }
    
    private function createPasswordResetHTML($to_name, $reset_link, $strings) {
        $html = '
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($strings['subject']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
        .header { background-color: #2c3e50; color: white; text-align: center; padding: 20px; }
        .content { padding: 30px; }
        .button { display: inline-block; background-color: #3498db; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .footer { background-color: #ecf0f1; padding: 20px; text-align: center; font-size: 12px; color: #7f8c8d; }
        .warning { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .link-fallback { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; word-break: break-all; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Karis Antikvariat</h1>
        </div>
        <div class="content">
            <h2>' . htmlspecialchars($strings['greeting']) . ' ' . htmlspecialchars($to_name) . '!</h2>
            
            <p>' . htmlspecialchars($strings['intro']) . '</p>
            
            <p>' . htmlspecialchars($strings['instruction']) . '</p>
            
            <div style="text-align: center;">
                <a href="' . htmlspecialchars($reset_link) . '" class="button">' . htmlspecialchars($strings['button_text']) . '</a>
            </div>
            
            <div class="link-fallback">
                <strong>' . htmlspecialchars($strings['alt_instruction']) . '</strong><br>
                <a href="' . htmlspecialchars($reset_link) . '">' . htmlspecialchars($reset_link) . '</a>
            </div>
            
            <div class="warning">
                <p><strong>' . htmlspecialchars($strings['expire_info']) . '</strong></p>
                <p>' . htmlspecialchars($strings['no_request']) . '</p>
            </div>
        </div>
        <div class="footer">
            <p>' . $strings['footer'] . '</p>
            <p>© ' . date('Y') . ' Karis Antikvariat. Alla rättigheter förbehållna.</p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }
    
    private function createPasswordResetText($to_name, $reset_link, $strings) {
        $text = $strings['greeting'] . ' ' . $to_name . "!\n\n";
        $text .= $strings['intro'] . "\n\n";
        $text .= $strings['instruction'] . "\n\n";
        $text .= $reset_link . "\n\n";
        $text .= $strings['expire_info'] . "\n\n";
        $text .= $strings['no_request'] . "\n\n";
        $text .= strip_tags($strings['footer']) . "\n";
        $text .= "© " . date('Y') . " Karis Antikvariat. Alla rättigheter förbehållna.";
        
        return $text;
    }
    
    /**
     * Send test email
     */
    public function sendTestEmail($to_email) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to_email);
            $this->mail->Subject = 'Karis Antikvariat - Email Test (SSL)';
            $this->mail->Body = '<h2>Gmail SSL Test</h2><p>Success! Your Gmail SMTP configuration with SSL (port 465) is working correctly!</p><p>Test sent at: ' . date('Y-m-d H:i:s') . '</p>';
            $this->mail->AltBody = 'Gmail SSL Test - Success! Your Gmail SMTP configuration with SSL (port 465) is working correctly! Test sent at: ' . date('Y-m-d H:i:s');
            
            $result = $this->mail->send();
            
            return [
                'success' => true,
                'message' => 'Test email sent successfully using SSL (port 465)!'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Test email failed: ' . $e->getMessage()
            ];
        }
    }
}
?>