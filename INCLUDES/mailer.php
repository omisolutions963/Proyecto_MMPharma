<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Requerir los archivos de la librería que descargamos localmente
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/env_loader.php';
loadEnv(__DIR__ . '/../.env');

/**
 * Función genérica para enviar correos usando PHPMailer
 * 
 * @param string $destinatario Correo del cliente
 * @param string $asunto Asunto del correo
 * @param string $cuerpo_html El HTML del correo
 * @return boolean True si se envió, False si hubo error
 */
function enviarCorreoPHPMailer($destinatario, $asunto, $cuerpo_html) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth   = true;                                  
        
        $mail->Username   = getenv('SMTP_USER') ?: 'hjona7573@gmail.com';                  
        $mail->Password   = getenv('SMTP_PASS') ?: 'vspxbycvinkqytos';         
        
        $smtpSecure       = getenv('SMTP_SECURE') ?: 'ssl';
        if ($smtpSecure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           // Habilitar encriptación TLS implícita (SSL/SMTPS)
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Habilitar encriptación STARTTLS
        }
        $mail->Port       = (int)(getenv('SMTP_PORT') ?: 465);                                   

        // Configuración de Remitente
        $fromEmail = getenv('SMTP_FROM_EMAIL') ?: (getenv('SMTP_USER') ?: 'hjona7573@gmail.com');
        $fromName  = getenv('SMTP_FROM_NAME') ?: 'MMPharma Portal';
        $mail->setFrom($fromEmail, $fromName);
        
        // Destinatario
        $mail->addAddress($destinatario);

        // Configuración del Mensaje
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo_html;

        // Enviar correo
        $mail->send();
        return true;
    } catch (Exception $e) {
        // Guarda el error en los logs de PHP para poder depurar sin romper la página
        error_log("No se pudo enviar el correo a $destinatario. Error de PHPMailer: {$mail->ErrorInfo}");
        return false;
    }
}
