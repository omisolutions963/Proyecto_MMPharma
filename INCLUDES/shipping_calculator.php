<?php
function haversineGreatCircleDistance($latFrom, $lonFrom, $latTo, $lonTo, $earthRadius = 6371) {
    $latFrom = deg2rad((float)$latFrom);
    $lonFrom = deg2rad((float)$lonFrom);
    $latTo = deg2rad((float)$latTo);
    $lonTo = deg2rad((float)$lonTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}

/**
 * Calcula el costo de envío y tipo de entrega
 * 
 * @param float $subtotal Monto total de los productos en el carrito
 * @param string $estado Estado de la república (ej: 'JALISCO')
 * @param float|null $lat Latitud
 * @param float|null $lng Longitud
 * @return array [ 'costo' => float, 'mensaje' => string, 'tipo' => string, 'puede_enviarse' => bool ]
 */
function calcularCostoEnvio($subtotal, $estado, $lat, $lng) {
    $estado = strtoupper(trim($estado));
    $origin_lat = 20.639194;
    $origin_lng = -103.403222;

    $resultado = [
        'costo' => 0.00,
        'mensaje' => 'Envío Gratis',
        'tipo' => 'GRATIS',
        'puede_enviarse' => true
    ];

    if ($estado !== 'JALISCO') {
        if ($subtotal > 8000) {
            $resultado['costo'] = 0.00;
            $resultado['mensaje'] = 'Envío Gratis';
            $resultado['tipo'] = 'GRATIS';
        } else {
            $resultado['costo'] = 290.00;
            $resultado['mensaje'] = 'Costo fijo de envío ($290.00)';
            $resultado['tipo'] = 'COSTO_FIJO';
        }
    } else {
        // Jalisco
        if ($lat !== null && $lng !== null) {
            $distance = haversineGreatCircleDistance($origin_lat, $origin_lng, $lat, $lng);
            if ($distance <= 10) {
                // Radio <= 10km
                if ($subtotal > 4000) {
                    $resultado['costo'] = 0.00;
                    $resultado['mensaje'] = 'Envío Gratis';
                    $resultado['tipo'] = 'GRATIS';
                } else {
                    $resultado['costo'] = 0.00;
                    $resultado['mensaje'] = 'Recoger en sucursal';
                    $resultado['tipo'] = 'SUCURSAL';
                    $resultado['puede_enviarse'] = false; // No se envía a domicilio
                }
            } else {
                // Radio > 10km
                if ($subtotal > 7000) {
                    $resultado['costo'] = 0.00;
                    $resultado['mensaje'] = 'Envío Gratis';
                    $resultado['tipo'] = 'GRATIS';
                } else {
                    $resultado['costo'] = 0.00;
                    $resultado['mensaje'] = 'Recoger en sucursal';
                    $resultado['tipo'] = 'SUCURSAL';
                    $resultado['puede_enviarse'] = false;
                }
            }
        } else {
            // En Jalisco pero sin ubicación exacta, asumimos como fuera del radio de 10km
            if ($subtotal > 7000) {
                $resultado['costo'] = 0.00;
                $resultado['mensaje'] = 'Envío Gratis';
                $resultado['tipo'] = 'GRATIS';
            } else {
                $resultado['costo'] = 0.00;
                $resultado['mensaje'] = 'Recoger en sucursal';
                $resultado['tipo'] = 'SUCURSAL';
                $resultado['puede_enviarse'] = false;
            }
        }
    }

    return $resultado;
}
