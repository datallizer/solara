<?php
require 'dbcon.php';
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
        <!-- Navbar Search-->
        <!-- <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Buscar..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-light" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form> -->
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-12 me-3 me-lg-12">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="soporte.php">Soporte</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li><a class="dropdown-item" href="logout.php">Salir</a></li>
                </ul>
            </li>
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

                        <?php
                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                            // Mostrar el enlace HTML solo si la condición se cumple
                            echo '<a class="nav-link" href="usuarios.php">
                            <div class="sb-nav-link-icon"><i class="bi bi-person-fill"></i></div>
                            Usuarios
                        </a>';
                        }
                        ?>

                        <div class="sb-sidenav-menu-heading">Modulos</div>
                        <?php
                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 3, 4, 5, 8, 9])) {
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
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 8])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="maquinados.php">Maquinados</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 4, 5, 8, 9])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="ensamble.php">Ensamble</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="bom.php">BOM</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="estadisticas.php">Estadisticas</a>';
                                }
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5])) {
                                    // Mostrar el enlace HTML solo si la condición se cumple
                                    echo '<a class="nav-link" href="proyectos.php">Proyectos</a>';
                                }
                                ?>
                            </nav>
                        </div>


                        <?php
                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 6, 7])) {
                            echo '
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                    Almacen
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                    <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                        <a class="nav-link" href="inventario.php">Inventario</a>
                                            <a class="nav-link" href="reorden.php">Reorden</a>
                                            <a class="nav-link" href="quotes.php">Quotes</a>
                                    </nav>
                                </div>
                                ';
                        }
                        ?>

                        <?php
                        if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                            // Mostrar el enlace HTML solo si la condición se cumple
                            echo '<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePagesDos" aria-expanded="false" aria-controls="collapsePagesDos">
                            <div class="sb-nav-link-icon"><i class="bi bi-trash-fill"></i></div>
                            Asignaciones
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>';
                        }
                        ?>
                        <div class="collapse" id="collapsePagesDos" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <?php
                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2])) {
                                    echo '<a class="nav-link" href="encargadoproyecto.php">Proyectos</a>';
                                }

                                if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1, 2, 5, 9])) {
                                    echo '<a class="nav-link" href="encargadoplanos.php">Maquinados</a>
                                            <a class="nav-link" href="encargadomecanico.php">Técnicos mecánicos</a>
                                            <a class="nav-link" href="encargadocontrol.php">Técnicos de control</a>';
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
                            Motivos de paro
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
                    <div class="small">Usuario:</div>
                    <?php
                    if (isset($_SESSION['codigo'])) {
                        $registro_id = $_SESSION['codigo'];
                        $query = "SELECT * FROM usuarios WHERE codigo='$registro_id' ";
                        $query_run = mysqli_query($con, $query);
                        if (mysqli_num_rows($query_run) > 0) {
                            $registro = mysqli_fetch_array($query_run);
                    ?>
                            <p class="mb-2"><?= $registro['nombre']; ?> <?= $registro['apellidop']; ?> <?= $registro['apellidom']; ?></p>
                            <img style="width: 40%;border-radius:10px;" src="data:image/jpeg;base64,<?php echo base64_encode($registro['medio']); ?>" alt="Foto perfil">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="js/sidenav.js"></script>