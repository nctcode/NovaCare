<?php
/**
 * MailHelper - Tiện ích gửi email thông qua PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load thư viện PHPMailer tải thủ công
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

class MailHelper {
    private static function getMailer() {
        $mail = new PHPMailer(true);
        try {
            // Cấu hình Server SMTP (Sẽ được thay bằng thông tin thật)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'vanvu14121310@gmail.com'; // TODO: Thay email
            $mail->Password   = 'ruxq gwdb rymt dtfp';    // TODO: Thay app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->CharSet    = 'UTF-8';

            // Người gửi mặc định
            $mail->setFrom('no-reply@novacare.com', 'NovaCare Hospital');

            return $mail;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Gửi email nhắc lịch và link phòng họp trực tuyến
     */
    public static function sendMeetingLink($patientEmail, $patientName, $doctorName, $reason, $meetingLink) {
        $mail = self::getMailer();
        if (!$mail) return false;

        try {
            $mail->addAddress($patientEmail, $patientName);
            $mail->isHTML(true);
            $mail->Subject = 'Thông báo Lịch Tư vấn Trực tuyến - NovaCare Hospital';

            // Template HTML cho email
            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
                <div style='background-color: #0d6efd; color: #ffffff; padding: 20px; text-align: center;'>
                    <h2 style='margin: 0;'>NovaCare Smart Hospital</h2>
                </div>
                <div style='padding: 30px;'>
                    <p>Kính gửi <strong>{$patientName}</strong>,</p>
                    <p>Bạn có một lịch hẹn tư vấn trực tuyến với <strong>Bác sĩ {$doctorName}</strong>.</p>
                    <p><strong>Lý do khám:</strong> {$reason}</p>
                    
                    <div style='margin: 30px 0; text-align: center;'>
                        <a href='{$meetingLink}' style='background-color: #198754; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;'>Tham gia Phòng Tư vấn</a>
                    </div>
                    
                    <p style='color: #6c757d; font-size: 14px;'>Vui lòng nhấp vào nút trên để tham gia khi đến giờ hẹn. Bạn không cần cài đặt thêm phần mềm nào.</p>
                </div>
                <div style='background-color: #f8f9fa; color: #6c757d; padding: 15px; text-align: center; font-size: 12px;'>
                    &copy; " . date('Y') . " NovaCare Hospital. All rights reserved.
                </div>
            </div>
            ";

            $mail->Body = $body;
            $mail->AltBody = "Kính gửi {$patientName}, bạn có lịch tư vấn trực tuyến với Bác sĩ {$doctorName}. Lý do: {$reason}. Link tham gia: {$meetingLink}";

            // Gửi email thật
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log lỗi nếu gửi thất bại
            error_log('Lỗi gửi email: ' . $mail->ErrorInfo);
            return false;
        }
    }
}
