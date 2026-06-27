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
 * @param float $total_normal_con_iva Monto total de los productos normales (sin red fría) con IVA incluido
 * @param string $estado Estado de la república (ej: 'JALISCO')
 * @param float|null $lat Latitud
 * @param float|null $lng Longitud
 * @param bool $tiene_red_fria Indica si hay productos de red fría en la cotización/carrito
 * @return array [ 'costo' => float, 'mensaje' => string, 'tipo' => string, 'puede_enviarse' => bool, 'mensaje_red_fria' => bool ]
 */
function calcularCostoEnvio($total_normal_con_iva, $estado, $lat, $lng, $tiene_red_fria = false) {
    $estado = strtoupper(trim($estado));
    $origin_lat = 20.639194;
    $origin_lng = -103.403222;

    $resultado = [
        'costo' => 0.00,
        'mensaje' => 'Envío gratis',
        'tipo' => 'GRATIS',
        'puede_enviarse' => true,
        'mensaje_red_fria' => $tiene_red_fria
    ];

    if ($total_normal_con_iva == 0 && $tiene_red_fria) {
        // Solo hay productos de red fría
        $resultado['costo'] = 0.00;
        $resultado['mensaje'] = 'Recoger en sucursal';
        $resultado['tipo'] = 'SUCURSAL';
        $resultado['puede_enviarse'] = false;
        return $resultado;
    }

    if ($estado !== 'JALISCO') {
        if ($total_normal_con_iva >= 8000) {
            $resultado['costo'] = 0.00;
            $resultado['mensaje'] = 'Envío gratis';
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
                if ($total_normal_con_iva >= 4000) {
                    $resultado['costo'] = 0.00;
                    $resultado['mensaje'] = 'Envío gratis';
                    $resultado['tipo'] = 'GRATIS';
                } else {
                    $resultado['costo'] = 0.00;
                    $resultado['mensaje'] = 'Recoger en sucursal';
                    $resultado['tipo'] = 'SUCURSAL';
                    $resultado['puede_enviarse'] = false; // No se envía a domicilio
                }
            } else {
                // Radio > 10km
                if ($total_normal_con_iva >= 7000) {
                    $resultado['costo'] = 0.00;
                    $resultado['mensaje'] = 'Envío gratis';
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
            if ($total_normal_con_iva >= 7000) {
                $resultado['costo'] = 0.00;
                $resultado['mensaje'] = 'Envío gratis';
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
