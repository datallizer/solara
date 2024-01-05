<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibieron los IDs seleccionados
    if (isset($_POST['idsSeleccionados'])) {
        $idsSeleccionados = json_decode($_POST['idsSeleccionados'], true);

        // Lógica para obtener los detalles de los materiales seleccionados de la base de datos
        require 'dbcon.php'; // Asegúrate de incluir la conexión a tu base de datos

        // Construir la consulta SQL para obtener los detalles de los materiales seleccionados
        $idArray = array_column($idsSeleccionados, 'id');
        $idString = implode(',', $idArray); // Convertir el array de IDs a una cadena para usar en la consulta
        $query = "SELECT * FROM inventario WHERE id IN ($idString)";
        $result = mysqli_query($con, $query);

        // Verificar si se obtuvieron resultados
        if ($result && mysqli_num_rows($result) > 0) {
            echo "
            <!DOCTYPE html>
            <html lang='en'>
            
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <meta http-equiv='X-UA-Compatible' content='ie=edge'>
                <title>BOM Propuesta | Solara</title>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css' rel='stylesheet' integrity='sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor' crossorigin='anonymous'>
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css'>
                <link rel='shortcut icon' type='image/x-icon' href='images/ics.png' />
            </head>
            
            <body>
            
                <div class='container-fluid' style='padding:80px;'>
                    <div class='row justify-content-center'>
                    <div class='col-9'>
                        <h1 style='font-size:50px;margin-top:80px;'>BOM PROPUESTA</h1>
                    </div>
                    <div class='col-3'><img src='images/logolateral.png' alt='Logo' style='width:100%;'></div>
                    <div class='col-12 mt-3'>
                    <p style='font-size:25px;'>
                    Esta propuesta ha sido generada utilizando datos históricos almacenados en la base de datos de SOLARA, es importante tener en cuenta que los costos, materiales u otros detalles presentados en esta propuesta podrían estar desactualizados, descontinuados o contener errores. Le recomendamos verificar la vigencia y precisión de la información presentada antes de tomar decisiones basadas en estos datos.</p>
                    </div>
                        <div class='col-12 mt-3'>
                            <table class='table table-bordered table-striped' style='width: 100%;font-size:25px;'>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Material</th>
                                        <th>Piezas</th>
                                        <th>Proveedor</th>
                                        <th>Descripcion</th>
                                        <th>Marca</th>
                                        <th>Condicion</th>
                                        <th>Costo unitario</th>
                                        <th>Costo total</th>
                                    </tr>
                                </thead>
                                <tbody>";

            $totalCosto = 0;

            // Iterar a través de los resultados y agregar filas a la tabla
            while ($row = mysqli_fetch_assoc($result)) {
                $materialId = $row['id'];
                $materialData = array_filter($idsSeleccionados, function ($item) use ($materialId) {
                    return $item['id'] == $materialId;
                });

                if (!empty($materialData)) {
                    $material = reset($materialData);
                    $piezas = $material['piezas'];
                    $costoTotal = floatval(str_replace('$', '', $material['costoTotal'])); // Convertir a número y eliminar cualquier coma en el valor

                    echo "<script>console.log('Costo Total:', " . json_encode($costoTotal) . ");</script>";

                    $totalCosto += $costoTotal; // Sumar el costo actual al total
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nombre']}</td>
                            <td>{$piezas}</td>
                            <td>{$row['proveedor']}</td>
                            <td>{$row['descripcion']}</td>
                            <td>{$row['marca']}</td>
                            <td>{$row['condicion']}</td>
                            <td>$ {$row['costo']}</td>
                            <td>$ {$costoTotal}</td>
                        </tr>";
                }
            }

            echo "<tr>
                                        <td class='text-end' colspan='8'>Total:</td>
                                        <td>$$totalCosto</td>
                                    </tr>";

            echo "</tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js' integrity='sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2' crossorigin='anonymous'></script>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
                <script src='https://code.jquery.com/jquery-3.6.4.min.js'></script>
                <script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js'></script>
                <script src='https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js'></script>
                <script>
            html2canvas(document.body).then(function(canvas) {
                var imgData = canvas.toDataURL('image/png');
                window.jsPDF = window.jspdf.jsPDF;
                var pdf = new jsPDF('p', 'pt', 'letter'); 

    var pdfWidth = pdf.internal.pageSize.getWidth();
    var pdfHeight = pdf.internal.pageSize.getHeight();

    var imgWidth = pdfWidth;
    var imgHeight = (canvas.height * pdfWidth) / canvas.width;

    if (imgHeight > pdfHeight) {
        imgWidth = (canvas.width * pdfHeight) / canvas.height;
        imgHeight = pdfHeight;
    }

    pdf.addImage(imgData, 'PNG', 0, 0, imgWidth, imgHeight);
    pdf.save('bom.pdf');

                // Redireccionar al usuario a bom.php después de descargar el PDF
                setTimeout(function() {
                    window.location.href = 'bom.php';
                }, 9000); // Redirecciona después de 1 segundo (1000 milisegundos)
            });
        </script>
            </body>
            
            </html>";
        } else {
            echo "No se encontraron datos para los IDs seleccionados.";
        }
    } else {
        echo "No se recibieron IDs seleccionados.";
    }
}
