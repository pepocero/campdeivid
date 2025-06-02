<?php
// contacto.php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';

$errors = [];
$success = false;
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar y sanitizar datos
    $formData = [
        'nombre' => Input::get('nombre'),
        'email' => Input::get('email'),
        'telefono' => Input::get('telefono'),
        'tipo_ruta' => Input::get('tipo_ruta'),
        'fecha' => Input::get('fecha'),
        'detalles' => Input::get('detalles')
    ];

    // Validaciones
    if (empty($formData['nombre'])) $errors['nombre'] = 'Por favor ingrese su nombre completo';
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Ingrese un email válido';
    if (empty($formData['tipo_ruta'])) $errors['tipo_ruta'] = 'Seleccione un tipo de ruta';
    if (strlen($formData['detalles']) < 20) $errors['detalles'] = 'Describa su solicitud con más detalle (mínimo 20 caracteres)';

    if (empty($errors)) {
        // Construir cuerpo del email
        $body = '
        <div style="max-width: 600px; margin: 20px auto; font-family: Arial, sans-serif; color: #333;">
            <div style="background: #ff6b00; padding: 20px; border-radius: 8px 8px 0 0;">
                <h2 style="color: white; margin: 0;">Nueva solicitud de presupuesto</h2>
            </div>
            
            <div style="padding: 25px; border: 1px solid #eee; border-radius: 0 0 8px 8px;">
                <div style="margin-bottom: 15px;">
                    <strong style="display: inline-block; width: 120px;">Nombre:</strong>
                    '.$formData['nombre'].'
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="display: inline-block; width: 120px;">Email:</strong>
                    '.$formData['email'].'
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="display: inline-block; width: 120px;">Teléfono:</strong>
                    '.($formData['telefono'] ?: 'No especificado').'
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="display: inline-block; width: 120px;">Tipo de ruta:</strong>
                    '.ucfirst($formData['tipo_ruta']).'
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="display: inline-block; width: 120px;">Fecha tentativa:</strong>
                    '.($formData['fecha'] ?: 'Sin fecha definida').'
                </div>
                <div>
                    <strong style="display: block; margin-bottom: 10px;">Detalles:</strong>
                    '.nl2br($formData['detalles']).'
                </div>
            </div>
            
            <div style="margin-top: 20px; text-align: center; color: #666; font-size: 0.9em;">
                Enviado desde el formulario de contacto de '.$settings->site_name.'
            </div>
        </div>';

        try {
            // Destinatario principal
            $to = rawurlencode('rutascandeivid@gmail.com');
            
            // Opciones del email incluyendo BCC para administradores
            $opts = array(
                'bcc' => 'pepocero@gmail.com, fchbass@gmail.com',
                
               );
            
            // Enviar el email con las opciones definidas
            if(email($to, 'Consulta sobre una ruta', $body, $opts)) {
                $success = true;
                $formData = [];
            } else {
                $errors['general'] = 'Error al enviar el mensaje. Por favor intente nuevamente.';
            }
        } catch(Exception $e) {
            $errors['general'] = 'Error del sistema: '.$e->getMessage();
        }
    }
}
?>



    <style>
        .contact-form { background: #ffffff; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 2rem; }
        .form-label { font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
        .form-control { border: 2px solid #e2e8f0; border-radius: 6px; padding: 0.75rem; transition: all 0.2s ease; }
        .form-control:focus { border-color: #ff6b00; box-shadow: 0 0 0 3px rgba(255,107,0,0.1); }
        .required::after { content: '*'; color: #e53e3e; margin-left: 3px; }
        .error-msg { color: #e53e3e; font-size: 0.875rem; margin-top: 0.25rem; }
        .alert { border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; }
        .alert-success { background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; }
        .alert-danger { background: #fff5f5; color: #c53030; border: 1px solid #fed7d7; }
        .btn-primary { background: #ff6b00; border: none; padding: 0.75rem 2rem; font-size: 1rem; }
        .btn-primary:hover { background: #e55d00; }
        .form-group.error .form-control { border-color: #e53e3e; background: #fff5f5; }
        .select-arrow { appearance: none; background: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e") no-repeat right 1rem center/16px; }
    </style>

<main class="section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="text-center mb-4" style="color: #2d3748;">Solicitud de Presupuesto</h1>
                
                <?php if($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> ¡Solicitud enviada con éxito!<br>
                        Te responderemos en menos de 24 horas.
                    </div>
                <?php elseif(isset($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> <?= $errors['general'] ?>
                    </div>
                <?php endif; ?>

                <div class="contact-form">
                    <form method="POST">
                        <div class="row g-4">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <div class="form-group <?= isset($errors['nombre']) ? 'error' : '' ?>">
                                    <label class="form-label required">Nombre completo</label>
                                    <input type="text" class="form-control" name="nombre" 
                                           value="<?= htmlspecialchars($formData['nombre'] ?? '') ?>" 
                                           required>
                                    <?php if(isset($errors['nombre'])): ?>
                                        <span class="error-msg"><?= $errors['nombre'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group <?= isset($errors['email']) ? 'error' : '' ?>">
                                    <label class="form-label required">Correo electrónico</label>
                                    <input type="email" class="form-control" name="email" 
                                           value="<?= htmlspecialchars($formData['email'] ?? '') ?>" 
                                           required>
                                    <?php if(isset($errors['email'])): ?>
                                        <span class="error-msg"><?= $errors['email'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Teléfono -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Teléfono de contacto</label>
                                    <input type="tel" class="form-control" name="telefono" 
                                           value="<?= htmlspecialchars($formData['telefono'] ?? '') ?>">
                                </div>
                            </div>

                            <!-- Tipo de Ruta -->
                            <div class="col-md-6">
                                <div class="form-group <?= isset($errors['tipo_ruta']) ? 'error' : '' ?>">
                                    <label class="form-label required">Tipo de ruta</label>
                                    <select class="form-control select-arrow" name="tipo_ruta" required>
                                        <option value="">Seleccione una opción</option>
                                        <option value="individual" <?= ($formData['tipo_ruta'] ?? '') === 'individual' ? 'selected' : '' ?>>Ruta Individual</option>
                                        <option value="grupo" <?= ($formData['tipo_ruta'] ?? '') === 'grupo' ? 'selected' : '' ?>>Viaje en Grupo</option>
                                        <option value="personalizada" <?= ($formData['tipo_ruta'] ?? '') === 'personalizada' ? 'selected' : '' ?>>Planificación Premium</option>
                                    </select>
                                    <?php if(isset($errors['tipo_ruta'])): ?>
                                        <span class="error-msg"><?= $errors['tipo_ruta'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Fecha -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Fecha tentativa</label>
                                    <input type="date" class="form-control" name="fecha" 
                                           value="<?= htmlspecialchars($formData['fecha'] ?? '') ?>" 
                                           min="<?= date('Y-m-d') ?>">
                                </div>
                            </div>

                            <!-- Detalles -->
                            <div class="col-12">
                                <div class="form-group <?= isset($errors['detalles']) ? 'error' : '' ?>">
                                    <label class="form-label required">Detalles de su solicitud</label>
                                    <textarea class="form-control" name="detalles" rows="5" 
                                              required><?= htmlspecialchars($formData['detalles'] ?? '') ?></textarea>
                                    <?php if(isset($errors['detalles'])): ?>
                                        <span class="error-msg"><?= $errors['detalles'] ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Botón de envío -->
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<div style="height: 30rem;"></div>
<?php include $abs_us_root.$us_url_root.'users/includes/html_footer.php'; ?>