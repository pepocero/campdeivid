<?php
require_once '../users/init.php';
if (!securePage($_SERVER['PHP_SELF'])){die();}

$userId = $user->data()->id;
$ruta_id = Input::get('ruta_id');
$precio = Input::get('precio');
$transactionId = Input::get('transactionId');
$status = Input::get('status');
$payerId = Input::get('payerId');
$payerEmail = Input::get('payerEmail');
$payerName = Input::get('payerName');
$repostaje = Input::get('repostaje');
$hoteles = Input::get('hoteles');
$puntos = Input::get('puntos');

if (!$ruta_id || !$precio || !is_numeric($ruta_id) || !is_numeric($precio)) {
    die("Parámetros inválidos.");
}

try {
    $db = DB::getInstance();

    // Evitar compras duplicadas
    $existe = $db->query("SELECT * FROM aa_compras WHERE user_id = ? AND ruta_id = ?", [$userId, $ruta_id])->count();
    if ($existe > 0) {
        Session::flash('info', 'Ya has comprado esta ruta anteriormente.');
        Redirect::to('mis_compras.php');
    }

    // Guardar compra
    $db->insert('aa_compras', [
        'user_id' => $userId,
        'ruta_id' => $ruta_id,
        'precio_pagado' => $precio,
        'fecha_compra' => date('Y-m-d H:i:s'),
        'paypal_transaction_id' => $transactionId,
        'payer_id' => $payerId,
        'payer_email' => $payerEmail,
        'estado_pago' => $status,
        'payer_name' => $payerName,
        'opcion_repostaje' => $repostaje,
        'opcion_hoteles' => $hoteles,
        'opcion_puntos' => $puntos
    ]);

    Session::flash('success', '¡Compra completada con éxito!');
    Redirect::to('mis_compras.php');
} catch (Exception $e) {
    die("Error al procesar la compra: " . $e->getMessage());
}