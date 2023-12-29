<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['idsSeleccionados'])) {
        $idsSeleccionados = json_decode($_POST['idsSeleccionados']);

        require 'dbcon.php'; // Incluye la conexión a la base de datos

        $idString = implode(',', $idsSeleccionados);
        $query = "SELECT * FROM inventario WHERE id IN ($idString)";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            require_once('tcpdf/tcpdf.php'); // Ruta al archivo TCPDF

            // Función para generar el contenido HTML común para la página y el PDF
            function generateContent($result) {
                $html = "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <title>BOM Propuesta | Solara</title>
                    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor' crossorigin='anonymous'>
                </head>
                <body>
                <div class='container-fluid'>
                <div class='row justify-content-center'>
                <div class='col-10'>
                <h1 class='mt-5'>BOM PROPUESTA</h1></div>
                <div class='col-2'><img src='https://datallizer.com/images/capullitos.png' alt='Logo' style='width:100%;'></div>
                <div class='col-12 mt-3'>
                <p>
                Esta propuesta ha sido generada utilizando datos históricos almacenados en la base de datos de SOLARA, es importante tener en cuenta que los costos, materiales u otros detalles presentados en esta propuesta podrían estar desactualizados, descontinuados o contener errores. Le recomendamos verificar la vigencia y precisión de la información presentada antes de tomar decisiones basadas en estos datos.</p>
                </div>
                    <div class='col-12 mt-3'>
                                <table class='table table-bordered table-striped' style='width: 100%;'>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Material</th>
                                            <th>Proveedor</th>
                                            <th>Descripcion</th>
                                            <th>Marca</th>
                                            <th>Condicion</th>
                                            <th>Costo</th>
                                        </tr>
                                    </thead>
                                    <tbody>";

                $totalCosto = 0;

                // Iterar a través de los resultados y agregar filas a la tabla
                while ($row = mysqli_fetch_assoc($result)) {
                    $totalCosto += $row['costo']; // Sumar el costo actual al total
                    $html .= "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['nombre']}</td>
                        <td>{$row['proveedor']}</td>
                        <td>{$row['descripcion']}</td>
                        <td>{$row['marca']}</td>
                        <td>{$row['condicion']}</td>
                        <td>{$row['costo']}</td>
                    </tr>";
                }

                $html .= "<tr>
                    <td class='text-end' colspan='6'>Total:</td>
                    <td>$totalCosto</td>
                </tr>";

                $html .= "</tbody>
                </table>
                </div>
                </div>
                </div>
                <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js' integrity='sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2' crossorigin='anonymous'></script>
                <!-- Tus scripts JavaScript adicionales -->
                </body>
                </html>";

                return $html;
            }

            // Generar el contenido HTML para la página
            $contentForPage = generateContent($result);

            // Crear instancia de TCPDF
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            // Establecer información del documento, encabezado, pie de página, etc.

            // Agregar página
            $pdf->AddPage();

            // Generar el PDF a partir del contenido HTML
            $pdf->writeHTML($contentForPage, true, false, true, false, '');

            // Generar el PDF y forzar la descarga
            $pdf->Output('BOM_Propuesta.pdf', 'D'); // 'D' fuerza la descarga del archivo

            // Redirigir al usuario a bom.php después de descargar el PDF
            header('Location: bom.php');
            exit();
        } else {
            echo "No se encontraron datos para los IDs seleccionados.";
        }

        // Liberar el resultado y cerrar la conexión a la base de datos
        mysqli_free_result($result);
        mysqli_close($con);
    } else {
        echo "No se recibieron IDs seleccionados.";
    }
}
?>
