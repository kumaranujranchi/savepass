<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class Mailer
{
    private static $host = SMTP_HOST;
    private static $port = SMTP_PORT;
    private static $username = SMTP_USER;
    private static $password = SMTP_PASS;

    public static function sendOTP($toEmail, $otp, $ip = 'Unknown', $browser = 'Unknown')
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = self::$host;
            $mail->SMTPAuth = true;
            $mail->Username = self::$username;
            $mail->Password = self::$password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = self::$port;

            // Recipients
            $mail->setFrom(self::$username, 'SecureVault Security');
            $mail->addAddress($toEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your SecureVault Verification Code';

            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #ffffff;'>
                    <div style='text-align: center; margin-bottom: 30px;'>
                        <h1 style='color: #2c0fbd; margin: 0;'>SecureVault</h1>
                        <p style='color: #8e92a4; margin-top: 5px;'>Security Verification</p>
                    </div>
                    <div style='padding: 20px; background-color: #f9f9f9; border-radius: 8px; text-align: center;'>
                        <p style='font-size: 16px; color: #333;'>We detected a login attempt from a new browser or device.</p>
                        
                        <div style='margin: 20px 0; padding: 15px; border: 1px dashed #ccc; border-radius: 8px; text-align: left; display: inline-block; width: 100%; box-sizing: border-box;'>
                            <p style='margin: 0; font-size: 14px; color: #555;'><strong>IP Address:</strong> $ip</p>
                            <p style='margin: 5px 0 0 0; font-size: 14px; color: #555;'><strong>Browser:</strong> $browser</p>
                        </div>

                        <p style='font-size: 16px; color: #333; margin-top: 20px;'>Your one-time verification code is:</p>
                        <h2 style='font-size: 32px; letter-spacing: 5px; color: #2c0fbd; margin: 20px 0;'>$otp</h2>
                        <p style='font-size: 14px; color: #5c5f73;'>This code is valid for 10 minutes. If you did not attempt to login, please change your master password immediately.</p>
                    </div>
                    <div style='margin-top: 30px; text-align: center; font-size: 12px; color: #8e92a4;'>
                        <p>&copy; " . date('Y') . " SecureVault. All rights reserved.</p>
                    </div>
                </div>
            ";

            $mail->AltBody = "Your SecureVault verification code is: $otp. It is valid for 10 minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
