<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
$codigouser = $_SESSION['userid'];

// Obtener la solicitud JSON desde el cliente
$data = json_decode(file_get_contents("php://input"), true);

$planosSeleccionados = isset($data['planos']) ? $data['planos'] : [];

$query = "SELECT historialoperadores.*, plano.nombreplano 
          FROM historialoperadores 
          INNER JOIN plano ON historialoperadores.idplano = plano.id 
          WHERE historialoperadores.idcodigo = '$codigouser' 
          AND motivoactividad <> 'Inicio' 
          AND motivoactividad <> 'Fin de jornada laboral' 
          AND motivoactividad <> 'Atención a otra prioridad'";

// Aplicar filtro si hay planos seleccionados
if (!empty($planosSeleccionados)) {
    $planosIds = implode(",", array_map('intval', $planosSeleccionados));
    $query .= " AND historialoperadores.idplano IN ($planosIds)";
}


$query .= " ORDER BY historialoperadores.id DESC";
$query_run = mysqli_query($con, $query);

// Array para acumular el tiempo total por motivoactividad
$tablaHTML = '';
$tiemposPorMotivo = [];

if (mysqli_num_rows($query_run) > 0) {
    foreach ($query_run as $registro) {
        // Convertir las fechas y horas en objetos DateTime
        $fechaInicio = new DateTime($registro['fecha'] . ' ' . $registro['hora']);
        $fechaFin = new DateTime($registro['fechareinicio'] . ' ' . $registro['horareinicio']);

        // Calcular la diferencia
        $intervalo = $fechaInicio->diff($fechaFin);

        // Calcular el tiempo total en minutos
        $totalMinutos = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

        // Acumular el tiempo por motivo
        $motivo = $registro['motivoactividad'];
        if (isset($tiemposPorMotivo[$motivo])) {
            $tiemposPorMotivo[$motivo] += $totalMinutos;
        } else {
            $tiemposPorMotivo[$motivo] = $totalMinutos;
        }

        // Formatear el resultado en días, horas y minutos
        $totalTiempo = $intervalo->format('%d días, %h horas, %i minutos');

        // Generar el HTML de la tabla
        $tablaHTML .= "<tr>
                           <td>{$registro['id']}</td>
                           <td>{$registro['nombreplano']}</td>
                           <td>{$registro['motivoactividad']}</td>
                           <td>{$registro['fecha']}</td>
                           <td>{$registro['hora']}</td>
                           <td>{$registro['fechareinicio']}</td>
                           <td>{$registro['horareinicio']}</td>
                           <td>{$totalTiempo}</td>
                       </tr>";
    }
} else {
    $tablaHTML = "<td colspan='8'><p>No se encontró ningún usuario</p></td>";
}

// Preparar datos para JSON
$response = [
    'tabla' => $tablaHTML,
    'labels' => array_keys($tiemposPorMotivo),
    'datos' => array_values($tiemposPorMotivo),
];

header('Content-Type: application/json');
echo json_encode($response);
?>
