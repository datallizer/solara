<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'dbcon.php';
$message = isset($_SESSION['message']) ? $_SESSION['message'] : ''; // Obtener el mensaje de la sesión

if (!empty($message)) {
    // HTML y JavaScript para mostrar la alerta...
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const message = " . json_encode($message) . ";
                Swal.fire({
                    title: 'NOTIFICACIÓN',
                    text: message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hacer algo si se confirma la alerta
                    }
                });
            });
        </script>";
    unset($_SESSION['message']); // Limpiar el mensaje de la sesión
}

if (isset($_POST['codigo'])) {
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);
    $query = "SELECT * FROM usuarios WHERE codigo='$codigo' AND estatus = 1";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['nombre'];
        $apellidop = $row['apellidop'];
        $apellidom = $row['apellidom'];
        $_SESSION['nombre'] = $nombre;
        $_SESSION['apellidop'] = $apellidop;
        $_SESSION['apellidom'] = $apellidom;
        $_SESSION['codigo'] = $row['codigo'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['rol'] = $row['rol'];
        if ($_SESSION['rol'] == 8) {
            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d");
            $hora_actual = date("H:i");
            $querydos = "INSERT INTO asistencia SET idcodigo='$idcodigo', entrada='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);
            $message = "Bienvenido " . $nombre . ' ' . $apellidop . ', ' . "ingresaste a las " . $hora_actual;
            $_SESSION['message'] = $message;
            header("Location: maquinados.php");
            exit();
        } elseif ($_SESSION['rol'] == 4) {
            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d");
            $hora_actual = date("H:i");
            $querydos = "INSERT INTO asistencia SET idcodigo='$idcodigo', entrada='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);
            $message = "Bienvenido " . $nombre . ' ' . $apellidop . ', ' . "ingresaste a las " . $hora_actual;
            $_SESSION['message'] = $message;
            header("Location: ensamble.php");
            exit();
        } elseif ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
            $hora_actual = date("H:i");
            $message = "Bienvenido " . $nombre . ' ' . $apellidop . ', ' . "ingresaste a las " . $hora_actual;
            $_SESSION['message'] = $message;
            header("Location: dashboard.php");
            exit();
        } elseif ($_SESSION['rol'] == 12) {
            header("Location: monitor.php");
            exit();
        } elseif ($_SESSION['rol'] == 5 || $_SESSION['rol'] == 9 || $_SESSION['rol'] == 13) {
            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d");
            $hora_actual = date("H:i");
            $querydos = "INSERT INTO asistencia SET idcodigo='$idcodigo', entrada='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);
            $message = "Bienvenido " . $nombre . ' ' . $apellidop . ', ' . "ingresaste a las " . $hora_actual;
            $_SESSION['message'] = $message;
            header("Location: dashboard.php");
            exit();
        } else {
            $idcodigo = $_SESSION['codigo'];
            $fecha_actual = date("Y-m-d");
            $hora_actual = date("H:i");
            $querydos = "INSERT INTO asistencia SET idcodigo='$idcodigo', entrada='$hora_actual', fecha='$fecha_actual'";
            $query_rundos = mysqli_query($con, $querydos);
            $message = "Bienvenido " . $nombre . ' ' . $apellidop . ', ' . "ingresaste a las " . $hora_actual;
            $_SESSION['message'] = $message;
            header("Location: dashboard.php");
            exit();
        }
        exit();
    } else {
        $_SESSION['message'] = "Codigo incorrecto";
        header("Location: login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="shortcut icon" type="image/x-icon" href="images/ics.png">
    <link rel="stylesheet" href="css/styles.css">
    <title>Acceso al sistema | Solara</title>
</head>
<style>
    .spinner-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1050;
    }

    .spinner-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .spinner {
        width: 3rem;
        height: 3rem;
    }
</style>

<body>
    <div class="container-fluid">
        <div style="min-height: 100vh;" class="row justify-content-evenly align-items-center loginform text-center">
            <div class="col-3">
                <img src="images/logo.png" alt="Logotipo">
            </div>
            <div class="col-4">
                <?php include 'message.php'; ?>
                <h2 class="mb-1">ACCESO AL SISTEMA</h2>
                <p id="reloj"><?php echo date("Y-m-d / H:i:s"); ?></p> <!-- Muestra la fecha y hora actual -->
                <form action="" method="post" class="row justify-content-evenly">
                    <div class="col-11 mb-4"><input id="inputCodigo" style="width: 100%;padding:10px 10px;" type="password" name="codigo" autocomplete="off" required></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(1)">1</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(2)">2</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(3)">3</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(4)">4</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(5)">5</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(6)">6</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(7)">7</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(8)">8</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(9)">9</a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-danger btnlogin" onclick="borrarUltimoCaracter()"><i class="bi bi-arrow-left"></i></a></div>
                    <div class="col-3 colbtnlogin"><a class="btn btn-outline-dark btnlogin" onclick="agregarValor(0)">0</a></div>
                    <div class="col-3 colbtnlogin"><button type="submit" class="btn btn-success btnlogin"><i class="bi bi-check2"></i></button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="spinner-overlay" style="z-index: 99999999999999999999999999999999999999999999999999999999999999999999;">
        <div class="spinner-container">
            <div class="spinner-grow text-primary spinner" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@10'></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        function agregarValor(valor) {
            var input = document.getElementById('inputCodigo');
            input.value += valor;
        }

        function borrarUltimoCaracter() {
            var input = document.getElementById('inputCodigo');
            var valor = input.value;
            input.value = valor.substring(0, valor.length - 1);
        }

        function actualizarHora() {
            const ahora = new Date();
            const anio = ahora.getFullYear();
            const mes = String(ahora.getMonth() + 1).padStart(2, '0'); // Los meses empiezan desde 0
            const dia = String(ahora.getDate()).padStart(2, '0');
            const hora = String(ahora.getHours()).padStart(2, '0');
            const minutos = String(ahora.getMinutes()).padStart(2, '0');
            const segundos = String(ahora.getSeconds()).padStart(2, '0');

            const formato = `${anio}-${mes}-${dia} / ${hora}:${minutos}:${segundos}`;
            document.getElementById('reloj').textContent = formato;
        }

        setInterval(actualizarHora, 1000); // Actualizar cada segundo


        $(document).ready(function() {
            $('form').on('submit', function(e) {
                e.preventDefault();

                $('.spinner-overlay').show(); // Muestra el spinner

                var buttonName = $(this).find('button[type=submit]:focus').attr('name');

                var form = this;
                $('<input>').attr({
                    type: 'hidden',
                    name: buttonName
                }).appendTo(form);

                form.submit();
            });
        });
    </script>
</body>

</html>