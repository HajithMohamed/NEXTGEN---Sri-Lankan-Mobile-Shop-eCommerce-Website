<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



// Function to send email
function sendMail(PHPMailer $mail, string $receiverEmail, string $receiverName, string $subject, string $body, array $attachments = []): bool {
    try {
        // Recipients
        $mail->setFrom($mail->Username, 'Mailer');
        $mail->addAddress($receiverEmail, $receiverName);
        // Add more recipients, CC, BCC, etc., as needed
        
        // Add attachments if provided
        foreach ($attachments as $filePath => $fileName) {
            if (file_exists($filePath)) {
                $mail->addAttachment($filePath, $fileName);
            } else {
                throw new Exception("Attachment file not found: $filePath");
            }
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Fallback for non-HTML clients

        $mail->send();
        echo "Message sent to $receiverEmail\n";
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent to $receiverEmail. Mailer Error: {$mail->ErrorInfo}\n";
        return false;
    }
}

try {
    
    $mail = new PHPMailer(true);

    // Server settings
    $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output in production
    $mail->isSMTP();
    $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.example.com'; // Use env vars
    $mail->SMTPAuth   = true;
    $mail->Username   = getenv('SMTP_USER') ?: 'user@example.com';
    $mail->Password   = getenv('SMTP_PASS') ?: 'secret';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Example usage of sendMail
    $attachments = [
        '/var/tmp/file.tar.gz' => 'file.tar.gz',
        '/tmp/image.jpg' => 'new.jpg',
    ];

    sendMail(
        $mail,
        'joe@example.net',
        'Joe User',
        'Test Email Subject',
        'This is the HTML message body <b>in bold!</b>',
        $attachments
    );

} catch (Exception $e) {
    echo "Failed to initialize PHPMailer: {$e->getMessage()}\n";
}