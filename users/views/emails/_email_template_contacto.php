<?php
/**
 * Plantilla de email para contacto
 */
extract($params);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $subject ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; }
        h2 { color: #ff6b00; border-bottom: 2px solid #ff6b00; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td, th { padding: 10px; border: 1px solid #eee; text-align: left; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Nueva Solicitud de Presupuesto</h2>
        
        <table>
            <?php foreach($params as $key => $value): ?>
            <tr>
                <th><?= $key ?></th>
                <td><?= $value ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <p><em>Este mensaje fue enviado desde el formulario de contacto de <?= $settings->site_name ?></em></p>
    </div>
</body>
</html>