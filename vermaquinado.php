<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';

if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    $query = "SELECT usuarios.codigo, usuarios.estatus FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Visualizador de maquinados' WHERE `usuarios`.`codigo` = '$codigo'";
        mysqli_query($con, $queryubicacion);
    } else {
        header('Location: login.php');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}

// Consulta para obtener todos los planos
$query = "SELECT id, medio FROM quotes";
$result = mysqli_query($con, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $carpeta = 'quotes';
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    while ($plano = mysqli_fetch_assoc($result)) {
        $plano_id = $plano['id'];
        $pdf_content = $plano['medio']; // Suponiendo que 'medio' es un campo BLOB (binario)

        if ($pdf_content) {
            $archivo_pdf = $carpeta . '/' . $plano_id . '.pdf';

            // Guardar el contenido binario directamente en el archivo
            file_put_contents($archivo_pdf, $pdf_content);
            echo "Archivo guardado: $archivo_pdf<br>";
        } else {
            echo "No se encontró contenido PDF para el quote con ID: $plano_id<br>";
        }
    }
} else {
    echo "No se encontraron quote.";
}

mysqli_close($con);
?>
