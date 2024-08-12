<?php
header('Content-Type: application/json'); // Asegura que el contenido devuelto es JSON
include 'dbcon.php'; // Asegúrate de incluir el archivo de conexión a la base de datos

$query = "SELECT * FROM proyecto WHERE estatus = 1 ORDER BY prioridad ASC";
$query_run = mysqli_query($con, $query);

$resultado = array();

while ($registro = mysqli_fetch_assoc($query_run)) {
    $idProyecto = $registro['id'];

    $querySumaPiezas = "SELECT SUM(piezas) AS suma_piezas FROM plano WHERE idproyecto = $idProyecto";
    $querySumaPiezasResult = mysqli_query($con, $querySumaPiezas);
    $sumaPiezas = mysqli_fetch_assoc($querySumaPiezasResult)['suma_piezas'];

    $querySumaAsignadas = "SELECT SUM(p.piezas) AS suma_asignadas
        FROM plano p
        INNER JOIN (
            SELECT DISTINCT idplano
            FROM asignacionplano
        ) a ON p.id = a.idplano
        WHERE p.idproyecto = $idProyecto";
    $querySumaAsignadasResult = mysqli_query($con, $querySumaAsignadas);
    $sumaAsignadas = mysqli_fetch_assoc($querySumaAsignadasResult)['suma_asignadas'];

    $querySumaPiezasTerminadas = "SELECT SUM(piezas) AS suma_piezas FROM plano WHERE idproyecto = $idProyecto AND estatusplano = 0";
    $querySumaPiezasTerminadasResult = mysqli_query($con, $querySumaPiezasTerminadas);
    $sumaPiezasTerminadas = mysqli_fetch_assoc($querySumaPiezasTerminadasResult)['suma_piezas'];

    $registro['suma_piezas'] = $sumaPiezas;
    $registro['suma_asignadas'] = $sumaAsignadas;
    $registro['suma_piezas_terminadas'] = $sumaPiezasTerminadas;

    $resultado[] = $registro;
}

echo json_encode($resultado);
?>
