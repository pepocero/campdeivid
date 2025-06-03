<?php
// usersc/scripts/custom_login_script.php
// Script que se ejecuta DESPUÉS de un login exitoso

// En lugar de hacer redirect inmediato, vamos a usar JavaScript
// para leer sessionStorage y hacer el redirect apropiado
?>
<script>
// Verificar si hay una URL de retorno guardada
var returnUrl = sessionStorage.getItem('returnAfterLogin');

if (returnUrl) {
    // Limpiar el sessionStorage
    sessionStorage.removeItem('returnAfterLogin');
    
    // Hacer el redirect a donde estaba el usuario
    console.log('Redirigiendo a:', returnUrl);
    window.location.href = returnUrl;
} else {
    // Si no hay URL guardada, comportamiento normal
    <?php if (hasPerm([2], $user->data()->id)): ?>
        // Es admin, ir al dashboard
        window.location.href = '<?= $us_url_root ?>pages/nueva_ruta.php';
    <?php else: ?>
        // Usuario normal, ir a cuenta
        window.location.href = '<?= $us_url_root ?>index.php';
    <?php endif; ?>
}
</script>

<?php
// NO hacer ningún Redirect::to() aquí porque JavaScript se encarga
// Si por alguna razón JavaScript no funciona, hacer fallback después de un delay
echo "<noscript>";
echo "<meta http-equiv='refresh' content='2;url=".$us_url_root."account.php'>";
echo "</noscript>";
?>
