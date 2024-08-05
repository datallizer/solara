<?php
// Iniciar la sesión para obtener el código del usuario y el número de actividades anteriores
session_start();

// Incluir la conexión a la base de datos
include 'dbcon.php';

// Obtener el código del usuario desde la sesión
$codigo = $_SESSION['codigo'];

// Realizar la consulta a la base de datos
$query = "SELECT COUNT(*) as total
          FROM diagrama 
          JOIN asignaciondiagrama ON asignaciondiagrama.idplano = diagrama.id 
          JOIN usuarios ON asignaciondiagrama.codigooperador = usuarios.codigo
          WHERE asignaciondiagrama.codigooperador = $codigo 
          AND (diagrama.estatusplano = 1 OR diagrama.estatusplano = 2 OR diagrama.estatusplano = 3)";

$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$nuevas_actividades = $row['total'];

// Verificar el número de actividades anteriores
$actividades_anteriores = isset($_SESSION['actividades_anteriores']) ? $_SESSION['actividades_anteriores'] : 0;

// Actualizar el número de actividades en la sesión
$_SESSION['actividades_anteriores'] = $nuevas_actividades;

// Crear la respuesta en formato JSON
$response = array('nuevas_actividades' => $nuevas_actividades, 'actividades_anteriores' => $actividades_anteriores);
echo json_encode($response);

// Cerrar la conexión a la base de datos
mysqli_close($con);
?>
