<?php
include 'dbcon.php'; // Conectar a la base de datos

$totals = [];

// Total de planos asignados
$query = "SELECT COUNT(*) as total_planos FROM plano WHERE estatusplano = '1'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$totals['asignados'] = $row['total_planos'];

// Total de planos en progreso
$query = "SELECT COUNT(*) as total_planos FROM plano WHERE estatusplano = '3'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$totals['progreso'] = $row['total_planos'];

// Total de planos pausados
$query = "SELECT COUNT(*) as total_planos FROM plano WHERE estatusplano = '2'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$totals['pausados'] = $row['total_planos'];

echo json_encode($totals);
?>
