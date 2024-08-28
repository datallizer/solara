<?php
require 'dbcon.php';

if (isset($_POST['save'])) {
    $mensaje = mysqli_real_escape_string($con, $_POST['mensaje']);

    // Verificar si 'codigooperador' está definido y es un array
    $codigosOperadores = isset($_POST['codigooperador']) ? $_POST['codigooperador'] : [];

    $emisor = $_SESSION['codigo'];
    $fecha = date("Y-m-d");
    $hora = date("H:i");
    $estatus = '1';

    // Preparar la consulta para evitar inyecciones SQL
    $query = "INSERT INTO mensajes (mensaje, idcodigo, emisor, fecha, hora, estatus) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        // Iterar sobre cada código de operador y realizar la inserción
        foreach ($codigosOperadores as $idcodigo) {
            // Enlazar parámetros
            mysqli_stmt_bind_param($stmt, "ssssss", $mensaje, $idcodigo, $emisor, $fecha, $hora, $estatus);

            // Ejecutar la consulta
            mysqli_stmt_execute($stmt);
        }

        // Cerrar el statement
        mysqli_stmt_close($stmt);

        $_SESSION['message'] = "Mensajes enviados exitosamente";
    } else {
        $_SESSION['message'] = "Error al enviar los mensajes, contacte a soporte";
    }
}
?>

<link rel="stylesheet" href="css/sidenav.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark" style="min-height: 80px;">
        <!-- Navbar Brand-->
        <a class="navbar-brand" href="dashboard.php"><img style="width: 200px;margin-left:15px;" src="images/logolateral.png" alt=""></a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-12 me-3 me-lg-12">
            <li class="nav-item dropdown m-1">
                <?php
                $queryUsuarios = "SELECT COUNT(*) as numUsuarios
                FROM (
                    SELECT usuarios.codigo, COUNT(plano.id) as cuenta
                    FROM usuarios
                    LEFT JOIN asignacionplano ON asignacionplano.codigooperador = usuarios.codigo
                    LEFT JOIN plano ON asignacionplano.idplano = plano.id AND plano.estatusplano IN (1, 2, 3)
                    WHERE usuarios.rol = 8
                    GROUP BY usuarios.codigo
                    HAVING cuenta <= 3
                ) as subquery";

                $resultado = mysqli_query($con, $queryUsuarios);
                $usuarioData = mysqli_fetch_assoc($resultado);
                $numUsuarios = $usuarioData['numUsuarios'];

                $queryControl = "SELECT COUNT(*) as numEnsambles
    FROM (
        SELECT usuarios.codigo, COUNT(diagrama.id) as cuenta
        FROM usuarios
        LEFT JOIN asignaciondiagrama ON asignaciondiagrama.codigooperador = usuarios.codigo
        LEFT JOIN diagrama ON asignaciondiagrama.idplano = diagrama.id AND diagrama.estatusplano IN (1, 2, 3)
        WHERE usuarios.rol = 4
        GROUP BY usuarios.codigo
        HAVING cuenta <= 3
    ) as subquery";


                $resultado = mysqli_query($con, $queryControl);
                $usuarioData = mysqli_fetch_assoc($resultado);
                $numEnsambles = $usuarioData['numEnsambles'];

                $queryAprobar = "SELECT COUNT(*) as numQuotes
                FROM (
                SELECT * FROM quotes WHERE estatusq = 1
                ) as subquery";

                $resultado = mysqli_query($con, $queryAprobar);
                $usuarioData = mysqli_fetch_assoc($resultado);
                $numQuotes = $usuarioData['numQuotes'];

                $queryBuy = "SELECT COUNT(*) as numBuy
                FROM (
                SELECT * FROM quotes WHERE estatusq = 0
                ) as subquery";

                $resultado = mysqli_query($con, $queryBuy);
                $usuarioData = mysqli_fetch_assoc($resultado);
                $numBuy = $usuarioData['numBuy'];

                ?>

                <?php
                // Variables de control para mostrar el enlace
                $mostrarEnlace = false;

                // Comprobar condiciones y asignar la variable de control
                if (($numUsuarios > 0 && in_array($_SESSION['rol'], [1, 2, 5])) ||
                    ($numEnsambles > 0 && in_array($_SESSION['rol'], [1, 2, 9])) || ($numQuotes > 0 && in_array($_SESSION['rol'], [1, 2])) || ($numBuy > 0 && in_array($_SESSION['rol'], [1, 2, 6, 7]))
                ) {
                    $mostrarEnlace = true;
                }

                if ($mostrarEnlace) : ?>
                    <a style="background-color:#363636;padding:3px 7px;border-radius:5px;" class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill"></i>
                        <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger">
                            <?php
                            if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                echo $numUsuarios + $numEnsambles + $numQuotes + $numBuy;
                            } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [5])) {
                                echo $numUsuarios;
                            } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [9])) {
                                echo $numEnsambles;
                            } elseif (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [6, 7])) {
                                echo $numBuy;
                            }
                            ?>
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </a>
                <?php endif; ?>


                <?php
                $queryUsuarios = "SELECT usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.medio, COUNT(plano.id) as cuenta
                FROM usuarios
                LEFT JOIN asignacionplano ON asignacionplano.codigooperador = usuarios.codigo
                LEFT JOIN plano ON asignacionplano.idplano = plano.id AND plano.estatusplano IN (1, 2, 3)
                WHERE usuarios.rol = 8
                GROUP BY usuarios.codigo
                HAVING cuenta <= 3 ORDER BY cuenta ASC";

                $resultado = mysqli_query($con, $queryUsuarios);
                function numeroATexto($numero)
                {
                    $textos = [
                        0 => 'no tiene ningún maquinado asignado',
                        1 => 'tiene un maquinado asignado',
                        2 => 'tiene dos maquinados asignados',
                        3 => 'tiene tres maquinados asignados'
                    ];
                    return $textos[$numero] ?? $numero; // Devuelve el texto o el número si no está en el array
                }

                $queryEnsambles = "SELECT usuarios.nombre, usuarios.apellidop, usuarios.apellidom, usuarios.medio, COUNT(diagrama.id) as cuenta
                                FROM usuarios
                                LEFT JOIN asignaciondiagrama ON asignaciondiagrama.codigooperador = usuarios.codigo
                                LEFT JOIN diagrama ON asignaciondiagrama.idplano = diagrama.id AND diagrama.estatusplano IN (1, 2, 3)
                                WHERE usuarios.rol = 4
                                GROUP BY usuarios.codigo
                                HAVING cuenta <= 3 ORDER BY cuenta ASC";


                $resultados = mysqli_query($con, $queryEnsambles);
                function numeroATextos($numeros)
                {
                    $texto = [
                        0 => 'no tiene ningún ensamble asignado',
                        1 => 'tiene un ensamble asignado',
                        2 => 'tiene dos ensambles asignados',
                        3 => 'tiene tres ensambles asignados'
                    ];
                    return $texto[$numeros] ?? $numeros; // Devuelve el texto o el número si no está en el array
                }

                ?>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="max-height:500px; overflow-y:auto;">
                    <?php
                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5])) {
                    ?>
                        <?php while ($usuario = mysqli_fetch_assoc($resultado)) : ?>
                            <li style="width: 400px;padding:0px 15px;">
                                <a href="maquinados.php" style="color:#000;">
                                    <div class="row">
                                        <div class="col-3"><img style="width: 100%;border-radius:35px;height:75px;object-fit: cover;object-position: top;" src="data:image/jpeg;base64,<?php echo base64_encode($usuario['medio']); ?>" alt="Foto perfil"></div>
                                        <div class="col-9">
                                            <small style="text-transform:uppercase;font-size:11px;"><i style="color: #ebc634;" class="bi bi-exclamation-triangle-fill"></i> Aviso Maquinados</small>
                                            <p><?php echo $usuario['nombre'] . ' ' . $usuario['apellidop'] . ' ' . $usuario['apellidom']; ?> <?php echo numeroATexto($usuario['cuenta']); ?>.</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <hr style="color: #fcfcfc;" class="dropdown-divider" />
                        <?php endwhile; ?>
                    <?php
                    }
                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 9])) {
                    ?>
                        <?php while ($ensamble = mysqli_fetch_assoc($resultados)) : ?>
                            <li style="width: 400px;padding:0px 15px;">
                                <a href="ensamble.php" style="color:#000;">
                                    <div class="row">
                                        <div class="col-3"><img style="width: 100%;border-radius:35px;height:75px;object-fit: cover;object-position: top;" src="data:image/jpeg;base64,<?php echo base64_encode($ensamble['medio']); ?>" alt="Foto perfil"></div>
                                        <div class="col-9">
                                            <small style="text-transform:uppercase;font-size:11px;"><i style="color: #ebc634;" class="bi bi-exclamation-triangle-fill"></i> Aviso Ensambles</small>
                                            <p><?php echo $ensamble['nombre'] . ' ' . $ensamble['apellidop'] . ' ' . $ensamble['apellidom']; ?> <?php echo numeroATextos($ensamble['cuenta']); ?>.</p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <hr style="color: #fcfcfc;" class="dropdown-divider" />
                        <?php endwhile; ?>
                    <?php
                    }
                    ?>

                    <?php
                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                        $query = "SELECT quotes.*, usuarios.* FROM quotes JOIN usuarios ON quotes.solicitante = CONCAT(usuarios.nombre,' ', usuarios.apellidop,' ', usuarios.apellidom) WHERE quotes.estatusq = 1";

                        $query_run = mysqli_query($con, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            foreach ($query_run as $cotizaciones) {
                    ?>
                                <li style="width: 400px;padding:0px 15px;">
                                    <a href="quotes.php" style="color:#000;">
                                        <div class="row">
                                            <div class="col-3"><img style="width: 100%;border-radius:35px;height:75px;object-fit: cover;object-position: top;" src="data:image/jpeg;base64,<?php echo base64_encode($cotizaciones['medio']); ?>" alt="Foto perfil"></div>
                                            <div class="col-9">
                                                <small style="text-transform:uppercase;font-size:11px;"><i style="color: #ebc634 !important;" class="bi bi-exclamation-triangle-fill"></i> Aviso quotes</small>
                                                <p><?php echo $cotizaciones['solicitante']; ?> registro una nueva cotización.</p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <hr style="color: #fcfcfc;" class="dropdown-divider" />
                    <?php
                            }
                        }
                    }
                    ?>

                    <?php
                    if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 4, 6, 7])) {
                        $query = "SELECT quotes.*, usuarios.* FROM quotes JOIN usuarios ON quotes.solicitante = CONCAT(usuarios.nombre,' ', usuarios.apellidop,' ', usuarios.apellidom) WHERE quotes.estatusq = 0";

                        $query_run = mysqli_query($con, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            foreach ($query_run as $compras) {
                    ?>
                                <li style="width: 400px;padding:0px 15px;">
                                    <a href="compras.php" style="color:#000;">
                                        <div class="row">
                                            <div class="col-3"><img style="width: 100%;border-radius:35px;height:75px;object-fit: cover;object-position: top;" src="data:image/jpeg;base64,<?php echo base64_encode($compras['medio']); ?>" alt="Foto perfil"></div>
                                            <div class="col-9">
                                                <small style="text-transform:uppercase;font-size:11px;"><i style="color: #ebc634 !important;" class="bi bi-exclamation-triangle-fill"></i> Aviso compras</small>
                                                <p>Hay una nueva compra pendiente: <?php echo $compras['cotizacion']; ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <hr style="color: #fcfcfc;" class="dropdown-divider" />
                    <?php
                            }
                        }
                    }
                    ?>
                </ul>

            </li>
            <li class="m-1">
                <button type="button" style="color:#fff;padding:6px 7px;border-radius:5px;" class="btn nav-link btn-primary" name="save" data-bs-toggle="modal" data-bs-target="#exampleModalMensaje">
                    <i class="bi bi-chat-left-dots-fill"></i>
                </button>
            </li>
            <li class="m-1">
                <a style="color:#000;padding:3px 7px;border-radius:5px;" class="nav-link btn-warning" href="logout.php" role="button">
                    <i class="bi bi-box-arrow-right"></i> Salir
                </a>
            </li>
            <!-- <li class="nav-item dropdown m-1">
                <a style="background-color:#363636;padding:3px 7px;border-radius:5px;" class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="soporte.php">Soporte</a></li>
                    <hr class="dropdown-divider" />
                    <li><a class="dropdown-item" href="logout.php">Salir</a></li>
                </ul>
            </li> -->
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Principal</div>
                        <a class="nav-link" href="dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Inicio
                        </a>
                        <div class="sb-sidenav-menu-heading">Modulos</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePagesFour" aria-expanded="false" aria-controls="collapsePagesFour">
                            <div class="sb-nav-link-icon"><i class="bi bi-person-arms-up"></i></div>
                            RRHH
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePagesFour" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <?php
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                    echo '
                                    <a class="nav-link" href="usuarios.php">Usuarios</a>';
                                }
                                ?>
                                <?php
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 10])) {
                                    echo '
                                    <a class="nav-link" href="asistencia.php">Asistencia</a>
                                    <a class="nav-link" href="nomina.php">Nómina</a>
                                    <a class="nav-link" href="sesiones.php">Sesiones</a>
                                    <a class="nav-link" href="dashboard.php">Movimientos</a>
                                    <a class="nav-link" href="permisos.php">Permisos</a>
                                    ';
                                } else if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [3, 4, 5, 6, 7, 8, 9, 13])) {
                                    echo '
                                    <a class="nav-link" href="asistenciapersonal.php?id=' . $_SESSION['codigo'] . '">Asistencia</a>
                                    <a class="nav-link" href="dashboard.php">Movimientos</a>
                                    <a class="nav-link" href="permisos.php">Permisos</a>
                                ';
                                }
                                ?>
                            </nav>
                        </div>

                        <?php
                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 8, 9, 13])) {
                            // Mostrar el enlace HTML solo si la condición se cumple
                            echo '<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="bi bi-shield-fill-check"></i></div>
                            Procesos
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>';
                        }
                        ?>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <?php
                                // Verificar si existe la sesión 'rol' y si el valor es 1, 2, 3 o 7
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 8, 5, 9, 13])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="maquinados.php">Maquinados</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 4, 5, 9, 13])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="ensamble.php">Ensamble</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9, 13])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="bom.php">BOM</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9, 10, 13])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="estadisticas.php">Estadisticas</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9, 13])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    
                                    echo '<a class="nav-link" href="proyectos.php">Proyectos</a>';
                                }
                                ?>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Almacen
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <?php
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7, 8, 9, 13])) {
                                    echo '
                                        <a class="nav-link" href="inventario.php">Inventario</a>
                                ';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 6, 7])) {
                                    echo '<a class="nav-link" href="reorden.php">Reorden</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 4, 5, 6, 7, 9, 13])) {
                                    echo '
                                            <a class="nav-link" href="quotes.php">Quotes</a>
                                            <a class="nav-link" href="compras.php">Compras</a>
                                    
                                ';
                                }
                                ?>
                            </nav>
                        </div>

                        <div class="sb-sidenav-menu-heading">Panel de control</div>
                        <?php
                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                            // Mostrar el enlace HTML solo si la condición se cumple
                            echo '<a class="nav-link" href="motivos.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-sign-stop"></i></div>
                            Motivos
                        </a>
                        
                        <a class="nav-link" href="actividades.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-check2-circle"></i></div>
                            Actividades
                        </a>';
                        }
                        ?>

                        <a class="nav-link" href="soporte.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-headset"></i></div>
                            Soporte
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <?php
                    if (isset($_SESSION['codigo'])) {
                        $registro_id = $_SESSION['codigo'];
                        $query = "SELECT * FROM usuarios WHERE codigo='$registro_id' ";
                        $query_run = mysqli_query($con, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            $registro = mysqli_fetch_array($query_run);
                    ?>
                            <div class="row">
                                <div class="col-5"><img style="width: 100%;border-radius:5px;height:92px;object-fit: cover;object-position:top;" src="data:image/jpeg;base64,<?php echo base64_encode($registro['medio']); ?>" alt="Foto perfil">
                                </div>
                                <div class="col-7">
                                    <p style="margin-left: -10px;"><?= $registro['nombre']; ?>
                                        <?= $registro['apellidop']; ?>
                                        <?= $registro['apellidom']; ?> <br>
                                        <small style="font-size: 11px;"><?php
                                                                        if ($registro['rol'] === '1') {
                                                                            echo "Administrador";
                                                                        } else if ($registro['rol'] === '2') {
                                                                            echo "Gerencia";
                                                                        } else if ($registro['rol'] === '4') {
                                                                            echo "Técnico controles";
                                                                        } else if ($registro['rol'] === '5') {
                                                                            echo "Ing. Diseño";
                                                                        } else if ($registro['rol'] === '6') {
                                                                            echo "Compras";
                                                                        } else if ($registro['rol'] === '7') {
                                                                            echo "Almacenista";
                                                                        } else if ($registro['rol'] === '8') {
                                                                            echo "Técnico mecanico";
                                                                        } else if ($registro['rol'] === '9') {
                                                                            echo "Ing. Control";
                                                                        } else if ($registro['rol'] === '10') {
                                                                            echo "Recursos humanos";
                                                                        } else if ($registro['rol'] === '13') {
                                                                            echo "Ing. Laser";
                                                                        } else {
                                                                            echo "Error, contacte a soporte";
                                                                        }
                                                                        ?></small>
                                    </p>

                                </div>
                            </div>
                    <?php
                        } else {
                            echo "<p>Error contacte a soporte</p>";
                        }
                    }
                    ?>
                </div>
            </nav>
        </div>
    </div>
</body>

<!-- Modal Mensajes -->
<div class="modal fade" id="exampleModalMensaje" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">ENVIAR MENSAJE</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" class="row">
                    <div class="form-floating col-12">
                        <textarea type="text" class="form-control" placeholder="Mensaje" id="actividad" name="mensaje" style="min-height: 100px"></textarea>
                        <label for="actividad">Mensaje</label>
                    </div>

                    <div class="form-check col-12 mt-3 m-3" id="usuariosContainer">
                        <?php
                        $query = "SELECT * FROM usuarios WHERE codigo <> $codigo AND rol <> 12";
                        $result = mysqli_query($con, $query);

                        if (mysqli_num_rows($result) > 0) {
                            while ($usuario = mysqli_fetch_assoc($result)) {
                                $nombreCompleto = $usuario['nombre'] . " " . $usuario['apellidop'] . " " . $usuario['apellidom'];
                                $idUsuario = $usuario['codigo'];

                                echo "<input class='form-check-input' type='checkbox' id='codigooperador_$idUsuario' name='codigooperador[]' value='$idUsuario'>";
                                echo "<label class='form-check-label' for='codigooperador_$idUsuario'>$nombreCompleto</label><br>";
                            }
                        }
                        ?>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary" name="save">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/sidenav.js"></script>