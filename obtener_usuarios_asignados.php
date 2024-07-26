<?php
if (isset($_POST['idplano'])) {
    $idPlano = $_POST['idplano'];

    // Conexión a la base de datos (asegúrate de incluir tu archivo de conexión aquí)
    require_once 'dbcon.php';

    $query = "SELECT codigooperador FROM asignacionplano WHERE idplano = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $idPlano);
    $stmt->execute();
    $result = $stmt->get_result();

    $usuariosAsignados = [];
    while ($row = $result->fetch_assoc()) {
        $usuariosAsignados[] = $row['codigooperador'];
    }

    header('Content-Type: application/json');
    echo json_encode($usuariosAsignados);
}
?>
