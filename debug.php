<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cargamos PHPMailer directamente
require_once __DIR__ . '/libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/libs/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // 🔴 ESTO ES LA MAGIA: Nos mostrará toda la plática entre tu servidor y Outlook
    $mail->SMTPDebug = 3; 
    $mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = 'smtp.office365.com';
    $mail->SMTPAuth   = true;                                  
    $mail->Username   = 'compras@mmpharma.mx';                  
    $mail->Password   = 'ypduheoddowhculd'; // Tu contraseña de aplicación        
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587;                                   

    // Remitente y destinatario
    $mail->setFrom('compras@mmpharma.mx', 'MMPharma Portal');
    $mail->addAddress('compras@mmpharma.mx'); 

    $mail->isHTML(true);
    $mail->Subject = 'Prueba Debug Outlook';
    $mail->Body    = 'Si ves esto, funciona.';

    $mail->send();
    echo "<h2>¡EXITO! El correo se envió.</h2>";
} catch (Exception $e) {
    echo "<h2>ERROR</h2>";
    echo "Error de PHPMailer: " . $mail->ErrorInfo;
}
?>
