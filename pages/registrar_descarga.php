<?php
// /xmlhttp/registrar_descarga.php
require_once '../users/init.php';
header('Content-Type: application/json');

// ✅ Protección CSRF
if(!Input::exists() || !Token::check(Input::get('csrf'))){
  http_response_code(403);
  echo json_encode(['error' => 'Token CSRF no válido']);
  exit;
}

$ruta_id = (int)Input::get('ruta_id');
$tipo    = Input::get('tipo') === 'venta' ? 'venta' : 'gratis';

$db = DB::getInstance();
$db->insert('rutas_log_descargas', [
  'ruta_id' => $ruta_id,
  'user_id' => ($user->isLoggedIn() ? $user->data()->id : null),
  'tipo'    => $tipo,
  'ip'      => $_SERVER['REMOTE_ADDR']
]);

echo json_encode(['ok' => true]);

?>

