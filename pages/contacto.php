<?php
// contacto.php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/template/prep.php';

// --- CONFIGURACIÓN RECAPTCHA ---
// Sustituye 'TU_SITE_KEY' y 'TU_SECRET_KEY' por las claves que obtengas en https://www.google.com/recaptcha/admin
if(!defined('RECAPTCHA_SITE_KEY')) define('RECAPTCHA_SITE_KEY','6Leq68wSAAAAAFhwzVZ0BhgcSwVUfHeaiBTcq0YF');
if(!defined('RECAPTCHA_SECRET_KEY')) define('RECAPTCHA_SECRET_KEY','6Leq68wSAAAAAAhUFEF7qd7REVuEUImzU2UeYy2f');


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


    // Verificar reCAPTCHA
    $recaptcha_response = Input::get('g-recaptcha-response');
    if(empty($recaptcha_response)){
        $errors['captcha'] = 'Por favor, marque la casilla reCAPTCHA';
    } else {
        // Petición a la API de Google
        $verify = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.RECAPTCHA_SECRET_KEY.'&response='.$recaptcha_response.'&remoteip='.$_SERVER['REMOTE_ADDR']);
        $captcha_success = json_decode($verify, true);
        if(!$captcha_success['success']){
            $errors['captcha'] = 'La verificación reCAPTCHA ha fallado. Inténtalo de nuevo.';
        }
    }

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
                    '.$formData['telefono'].'
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="display: inline-block; width: 120px;">Tipo Ruta:</strong>
                    '.$formData['tipo_ruta'].'
                </div>
                <div style="margin-bottom: 15px;">
                    <strong style="display: inline-block; width: 120px;">Fecha:</strong>
                    '.$formData['fecha'].'
                </div>
                <div>
                    <strong style="display: inline-block; width: 120px;">Detalles:</strong>
                    '.$formData['detalles'].'
                </div>
            </div>
        </div>';

        // Envía el email (usa tu lógica o librería preferida)
        // ...

        $success = true;
    }
}

// A partir de aquí el HTML de tu página permanece igual, salvo por la inclusión del recaptcha y la etiqueta script

?>
<?php if($success): ?>
    <div class="alert alert-success">¡Gracias! Tu solicitud ha sido enviada correctamente.</div>
<?php endif; ?>

<main class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">Pide tu presupuesto</h1>

                    <?php if(isset($errors['general'])): ?>
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
                                        <select class="form-select" name="tipo_ruta" required>
                                            <option value="">Selecciona</option>
                                            <option value="cultural" <?= ($formData['tipo_ruta'] ?? '') === 'cultural' ? 'selected' : '' ?>>Cultural</option>
                                            <option value="gastronomica" <?= ($formData['tipo_ruta'] ?? '') === 'gastronomica' ? 'selected' : '' ?>>Gastronómica</option>
                                            <option value="aventura" <?= ($formData['tipo_ruta'] ?? '') === 'aventura' ? 'selected' : '' ?>>Aventura</option>
                                        </select>
                                        <?php if(isset($errors['tipo_ruta'])): ?>
                                            <span class="error-msg"><?= $errors['tipo_ruta'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Fecha -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Fecha deseada</label>
                                        <input type="date" class="form-control" name="fecha"
                                               value="<?= htmlspecialchars($formData['fecha'] ?? '') ?>">
                                    </div>
                                </div>

                                <!-- Detalles -->
                                <div class="col-12">
                                    <div class="form-group <?= isset($errors['detalles']) ? 'error' : '' ?>">
                                        <label class="form-label required">Detalles adicionales</label>
                                        <textarea class="form-control" name="detalles" rows="5" required><?= htmlspecialchars($formData['detalles'] ?? '') ?></textarea>
                                        <?php if(isset($errors['detalles'])): ?>
                                            <span class="error-msg"><?= $errors['detalles'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- reCAPTCHA -->
                                <div class="col-12 text-center">
                                    <div class="g-recaptcha d-inline-block" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                                    <?php if(isset($errors['captcha'])): ?>
                                        <div class="error-msg mt-2"><?= $errors['captcha'] ?></div>
                                    <?php endif; ?>
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
    </div>
</main>
<div style="height: 30rem;"></div>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php include $abs_us_root.$us_url_root.'users/includes/html_footer.php'; ?>
