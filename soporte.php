<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';

// Verificar si existe una sesión activa y los valores de usuario y contraseña están establecidos
if (isset($_SESSION['codigo'])) {
    $codigo = $_SESSION['codigo'];

    // Consultar la base de datos para verificar si los valores coinciden con algún registro en la tabla de usuarios
    $query = "SELECT * FROM usuarios WHERE codigo = '$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);

    // Si se encuentra un registro coincidente, el usuario está autorizado
    if (mysqli_num_rows($result) > 0) {
        // El usuario está autorizado, se puede acceder al contenido
        $queryubicacion = "UPDATE `usuarios` SET `ubicacion` = 'Soporte' WHERE `usuarios`.`codigo` = '$codigo'";
        $queryubicacion_run = mysqli_query($con, $queryubicacion);
    } else {
        // Redirigir al usuario a una página de inicio de sesión
        header('Location: login.php');
        exit(); // Finalizar el script después de la redirección
    }
} else {
    // Redirigir al usuario a una página de inicio de sesión si no hay una sesión activa
    header('Location: login.php');
    exit(); // Finalizar el script después de la redirección
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Alliance Broker litigio y seguros">
    <meta name="keywords" content="seguros, litigio, abogados, aseguradora, aguascalientes, broker, alliance">
    <meta name="author" content="Alliance Broker">
    <meta property="og:title" content="Soporte | Alliance Broker">
    <meta property="og:description" content="Alliance Broker litigio y seguros">
    <meta property="og:image" content="images/cintilloinferior.jpg">
    <meta property="og:url" content="https://alliancebroker.com.mx/">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Soporte | Solara</title>
</head>

<body class="sb-nav-fixed">
    <?php include 'sidenav.php'; ?>
<?php include 'mensajes.php'; ?>
    <div id="layoutSidenav">
        <div id="layoutSidenav_content">
            <div class="container-fluid">
                <div class="row justify-content-center mt-5 mb-5">
                    <div class="col-12 col-md-11 mb-5">
                        <h2>SOPORTE</h2>
                        <p>Los siguientes medios son enlaces directos para ponerte en contácto con el equipo de desarrollo de <b>datallizer</b>, recuerda hacer buen uso de ellos, unicamente podrás reportar fallas relacionadas al funcionamiento de la aplicación web cómo: Alta, baja o actualizacion de usuarios, planos, proyectos, notificaciones, clientes, motivos de paro, error al mostrar datos, mal funcionamiento o comportamientos no esperados en el diseño de algún componente, etc.</p>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <a class="card text-center p-5" style="text-decoration: none;" href="mailto:soporte@datallizer.com">
                            <i class="bi bi-envelope-fill" style="font-size: 45px;color:#3273a8"></i>
                            <p style="color: #000000;">soporte@datallizer.com</p>
                        </a>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <a class="card text-center p-5" style="text-decoration: none;" href="https://wa.me/524493854039">
                            <i class="bi bi-whatsapp" style="font-size: 45px;color:#309c49"></i>
                            <p style="color: #000000;">449 385 4039</p>
                        </a>
                    </div>
                    <div class="col-12 col-md-3">
                        <a class="card text-center p-5" style="text-decoration: none;" href="tel:4494175357">
                            <i class="bi bi-telephone-fill" style="font-size: 45px;color:#9c3530;"></i>
                            <p style="color: #000000;">449 417 5357</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
</body>

</html>