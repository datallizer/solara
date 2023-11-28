<?php
session_start();
require 'dbcon.php';

if (isset($_POST['codigo'])) {
    $codigo = mysqli_real_escape_string($con, $_POST['codigo']);

    $query = "SELECT * FROM usuarios WHERE codigo='$codigo'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['nombre'];
        $apellidop = $row['apellidop'];
        $_SESSION['nombre'] = $nombre; // Asignar el nombre a la sesión
        $_SESSION['codigo'] = $row['codigo'];
        $_SESSION['rol'] = $row['rol']; // Guardar el rol en la sesión
        $_SESSION['message'] = "Bienvenido " . $nombre . ' ' . $apellidop;
        header("Location: dashboard.php");
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
    <link rel="shortcut icon" type="image/x-icon" href="images/ico.ico">
    <link rel="stylesheet" href="css/styles.css">
    <title>Acceso al sistema | Solara</title>
</head>

<body>
    <div class="container-fluid">
        <div style="min-height: 100vh;" class="row justify-content-evenly align-items-center loginform text-center">
            <div class="col-3">
                <img src="images/logo.png" alt="Logotipo">
            </div>
            <div class="col-4">
                <?php include 'message.php'; ?>
                <h2 class="mb-4">ACCESO AL SISTEMA</h2>
                <form action="" method="post" class="row justify-content-evenly">
                    <div class="col-11 mb-4"><input id="inputCodigo" style="width: 100%;padding:10px 10px;" type="text" name="codigo" autocomplete="off" required></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
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
    </script>
</body>

</html>