<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Requerir los archivos de la librería que descargamos localmente
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';

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
        $mail->Host       = 'smtp.gmail.com';                      // Servidor SMTP (ej. Gmail)
        $mail->SMTPAuth   = true;                                  // Habilitar autenticación SMTP
        
        // ---------------------------------------------------------------------
        // TODO: CAMBIA ESTOS DATOS POR TU CORREO REAL PARA QUE FUNCIONE
        // ---------------------------------------------------------------------
        $mail->Username   = 'hjona7573@gmail.com';                  // Tu correo de Gmail
        $mail->Password   = 'vspxbycvinkqytos';         // Contraseña de App (No la de tu correo normal)
        // ---------------------------------------------------------------------
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           // Habilitar encriptación TLS implícita
        $mail->Port       = 465;                                   // Puerto TCP (465 para SMTPS)

        // Configuración de Remitente
        // El primer parámetro debe ser el mismo que Username, el segundo es el nombre que verá el cliente
        $mail->setFrom('hjona7573@gmail.com', 'MMPharma Portal');
        
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
