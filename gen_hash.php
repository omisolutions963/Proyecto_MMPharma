<?php
// Ejecuta ESTO en http://localhost/Proyecto_MMPharma/gen_hash.php
// Luego copia el hash y ejecuta el UPDATE en phpMyAdmin
$hash = password_hash('Admin123!', PASSWORD_BCRYPT);
echo "<pre>Hash generado:\n$hash\n\n";
echo "Copia y pega esto en phpMyAdmin:\n\n";
echo "UPDATE administradores SET password_hash = '$hash' WHERE email = 'admin@mmpharma.com';\n</pre>";
