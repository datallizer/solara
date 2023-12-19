<?php
session_start();
require 'dbcon.php';

// Verificar si se recibe un parámetro "id" a través de la URL
if(isset($_GET['id'])) {



    $plano_id = $_GET['id'];

    // Consulta para obtener el PDF según el ID del plano
    $query = "SELECT * FROM plano WHERE id = $plano_id";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $plano = mysqli_fetch_assoc($result);
        $pdf_content = $plano['medio']; // Suponiendo que el contenido del PDF se almacena en la columna 'medio'

        // Mostrar el PDF si se encontró y se pudo obtener el contenido
        if ($pdf_content) {
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Plano</title>
</head>
<body>
    <div style="width: 100%; height: 100vh;">
        <embed src="data:application/pdf;base64,<?= base64_encode($pdf_content); ?>" type="application/pdf" width="100%" height="100%" />
    </div>
</body>
</html>
<?php
        } else {
            echo "No se encontró el PDF correspondiente al ID del plano.";
        }
    } else {
        echo "No se encontró el plano con el ID proporcionado.";
    }

    mysqli_close($con); // Cerrar la conexión a la base de datos
} else {
    echo "ID de plano no válido.";
}
?>
