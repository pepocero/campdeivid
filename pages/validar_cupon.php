<?php
require_once '../users/init.php';

// Solo permitir peticiones AJAX POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método no permitido');
}

header('Content-Type: application/json');

try {
    $db = DB::getInstance();
    
    // Obtener datos del POST
    $codigo_cupon = strtoupper(trim($_POST['codigo'] ?? ''));
    $ruta_id = intval($_POST['ruta_id'] ?? 0);
    $precio_original = floatval($_POST['precio_original'] ?? 0);
    
    // Validaciones básicas
    if (empty($codigo_cupon)) {
        echo json_encode(['success' => false, 'message' => 'Código de cupón requerido']);
        exit;
    }
    
    if ($ruta_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID de ruta inválido']);
        exit;
    }
    
    if ($precio_original <= 0) {
        echo json_encode(['success' => false, 'message' => 'Precio inválido']);
        exit;
    }
    
    // Buscar el cupón
    $cupon = $db->query("
        SELECT * FROM aa_cupones 
        WHERE codigo = ? AND activo = 1
    ", [$codigo_cupon])->first();
    
    if (!$cupon) {
        echo json_encode(['success' => false, 'message' => 'Cupón no válido o no existe']);
        exit;
    }
    
    // Obtener información de la ruta
    $ruta = $db->query("SELECT plan FROM aa_rutas WHERE id = ?", [$ruta_id])->first();
    
    if (!$ruta) {
        echo json_encode(['success' => false, 'message' => 'Ruta no encontrada']);
        exit;
    }
    
    // Verificar si el cupón aplica a este tipo de ruta
    if ($cupon->aplicable_a !== 'todos') {
        if ($cupon->aplicable_a === 'premium' && $ruta->plan !== 'Premium') {
            echo json_encode(['success' => false, 'message' => 'Este cupón solo aplica a rutas Premium']);
            exit;
        }
        if ($cupon->aplicable_a === 'gratis' && $ruta->plan !== 'Gratis') {
            echo json_encode(['success' => false, 'message' => 'Este cupón solo aplica a rutas gratuitas']);
            exit;
        }
    }
    
    // Verificar fechas de vigencia
    $ahora = new DateTime();
    $fecha_inicio = new DateTime($cupon->fecha_inicio);
    $fecha_fin = new DateTime($cupon->fecha_fin);
    
    if ($ahora < $fecha_inicio) {
        echo json_encode([
            'success' => false, 
            'message' => 'Este cupón aún no está vigente. Válido desde: ' . $fecha_inicio->format('d/m/Y H:i')
        ]);
        exit;
    }
    
    if ($ahora > $fecha_fin) {
        echo json_encode([
            'success' => false, 
            'message' => 'Este cupón ha expirado. Expiró el: ' . $fecha_fin->format('d/m/Y H:i')
        ]);
        exit;
    }
    
    // Verificar límite de usos
    if ($cupon->usos_maximos) {
        $usos_actuales = $db->query("
            SELECT COUNT(*) as total 
            FROM aa_cupones_uso 
            WHERE cupon_id = ?
        ", [$cupon->id])->first();
        
        if ($usos_actuales->total >= $cupon->usos_maximos) {
            echo json_encode(['success' => false, 'message' => 'Este cupón ha alcanzado su límite de usos']);
            exit;
        }
    }
    
    // Verificar si el usuario ya usó este cupón (opcional - puedes comentar si permites múltiples usos por usuario)
    if ($user->isLoggedIn()) {
        $uso_previo = $db->query("
            SELECT id FROM aa_cupones_uso 
            WHERE cupon_id = ? AND user_id = ? AND ruta_id = ?
        ", [$cupon->id, $user->data()->id, $ruta_id])->first();
        
        if ($uso_previo) {
            echo json_encode(['success' => false, 'message' => 'Ya has usado este cupón para esta ruta']);
            exit;
        }
    }
    
    // Calcular descuento
    $descuento = 0;
    $precio_final = $precio_original;
    
    if ($cupon->tipo_descuento === 'porcentaje') {
        $descuento = $precio_original * ($cupon->valor_descuento / 100);
        $precio_final = $precio_original - $descuento;
    } else { // fijo
        $descuento = min($cupon->valor_descuento, $precio_original); // No puede ser mayor al precio
        $precio_final = $precio_original - $descuento;
    }
    
    // Asegurar que el precio final no sea negativo
    $precio_final = max(0, $precio_final);
    $descuento = $precio_original - $precio_final;
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Cupón válido aplicado correctamente',
        'cupon' => [
            'id' => $cupon->id,
            'codigo' => $cupon->codigo,
            'descripcion' => $cupon->descripcion,
            'tipo_descuento' => $cupon->tipo_descuento,
            'valor_descuento' => $cupon->valor_descuento
        ],
        'precios' => [
            'original' => number_format($precio_original, 2),
            'descuento' => number_format($descuento, 2),
            'final' => number_format($precio_final, 2),
            'final_raw' => $precio_final // Para PayPal
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>