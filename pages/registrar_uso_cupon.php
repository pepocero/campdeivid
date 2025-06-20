<?php
require_once '../users/init.php';

/**
 * Función para registrar el uso de un cupón después de una compra exitosa
 * Se debe llamar desde procesar_venta.php o donde se procese el pago
 */
function registrarUsoCupon($cupon_id, $ruta_id, $user_id, $precio_original, $descuento_aplicado, $precio_final) {
    try {
        $db = DB::getInstance();
        
        // Verificar que el cupón existe y está activo
        $cupon = $db->query("
            SELECT id, usos_actuales, usos_maximos 
            FROM aa_cupones 
            WHERE id = ? AND activo = 1
        ", [$cupon_id])->first();
        
        if (!$cupon) {
            return ['success' => false, 'message' => 'Cupón no válido'];
        }
        
        // Verificar que no se exceda el límite de usos
        if ($cupon->usos_maximos && $cupon->usos_actuales >= $cupon->usos_maximos) {
            return ['success' => false, 'message' => 'Cupón agotado'];
        }
        
        // Registrar el uso del cupón
        $data_uso = [
            'cupon_id' => $cupon_id,
            'ruta_id' => $ruta_id,
            'user_id' => $user_id,
            'precio_original' => $precio_original,
            'descuento_aplicado' => $descuento_aplicado,
            'precio_final' => $precio_final
        ];
        
        $result = $db->insert('aa_cupones_uso', $data_uso);
        
        if ($result) {
            // Incrementar contador de usos del cupón
            $db->query("
                UPDATE aa_cupones 
                SET usos_actuales = usos_actuales + 1 
                WHERE id = ?
            ", [$cupon_id]);
            
            return ['success' => true, 'message' => 'Uso de cupón registrado correctamente'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar uso del cupón'];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

// Si se llama directamente via POST (para uso desde JavaScript)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'registrar_uso') {
    header('Content-Type: application/json');
    
    if (!$user->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
        exit;
    }
    
    $cupon_id = intval($_POST['cupon_id'] ?? 0);
    $ruta_id = intval($_POST['ruta_id'] ?? 0);
    $user_id = $user->data()->id;
    $precio_original = floatval($_POST['precio_original'] ?? 0);
    $descuento_aplicado = floatval($_POST['descuento_aplicado'] ?? 0);
    $precio_final = floatval($_POST['precio_final'] ?? 0);
    
    $resultado = registrarUsoCupon($cupon_id, $ruta_id, $user_id, $precio_original, $descuento_aplicado, $precio_final);
    echo json_encode($resultado);
    exit;
}
?>