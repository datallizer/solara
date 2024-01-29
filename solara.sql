
CREATE TABLE `asignacionplano` (
  `id` int(100) NOT NULL,
  `idplano` int(100) DEFAULT NULL,
  `codigooperador` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `encargadoproyecto` (
  `id` int(100) NOT NULL,
  `codigooperador` int(100) NOT NULL,
  `idproyecto` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `historial` (
  `id` int(255) NOT NULL,
  `idcodigo` int(100) DEFAULT NULL,
  `detalles` varchar(2000) DEFAULT NULL,
  `hora` varchar(30) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `plano` (
  `id` int(100) NOT NULL,
  `idproyecto` int(100) DEFAULT NULL,
  `nombreplano` varchar(100) DEFAULT NULL,
  `medio` longblob DEFAULT NULL,
  `nivel` int(10) DEFAULT NULL,
  `piezas` int(100) DEFAULT NULL,
  `etapa` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `proyecto` (
  `id` int(100) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `detalles` varchar(2000) DEFAULT NULL,
  `cliente` varchar(100) DEFAULT NULL,
  `prioridad` int(2) DEFAULT NULL,
  `fechainicio` date DEFAULT NULL,
  `fechafin` date DEFAULT NULL,
  `presupuesto` int(100) DEFAULT NULL,
  `estatus` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usuarios` (
  `id` int(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidop` varchar(50) NOT NULL,
  `apellidom` varchar(50) NOT NULL,
  `rol` int(2) NOT NULL,
  `codigo` int(100) NOT NULL,
  `estatus` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `apellidop`, `apellidom`, `rol`, `codigo`, `estatus`) VALUES
(7, 'Miguel', 'Villa', 'Solara', 1, 12345, 1),
(8, 'Operador', 'Uno', 'Test', 8, 66666, 1),
(9, 'Operador', 'Dos', 'Test', 8, 55555, 1);

CREATE TABLE `inventario` (
  `id` int(255) NOT NULL,
  `clasificacion` varchar(100) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `proveedor` varchar(100) NOT NULL,
  `parte` varchar(100) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `condicion` varchar(100) NOT NULL,
  `cantidad` int(255) NOT NULL,
  `rack` varchar(100) NOT NULL,
  `bin` varchar(100) NOT NULL,
  `caja` varchar(100) NOT NULL,
  `ubicacion` varchar(100) NOT NULL,
  `costo` int(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `numero` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventario`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;
COMMIT;


ALTER TABLE `asignacionplano`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `encargadoproyecto`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `historial`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `plano`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `asignacionplano`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

ALTER TABLE `encargadoproyecto`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

ALTER TABLE `historial`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `plano`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

ALTER TABLE `proyecto`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

CREATE TABLE `diagrama` (
  `id` int(100) NOT NULL,
  `idproyecto` int(100) DEFAULT NULL,
  `nombreplano` varchar(100) DEFAULT NULL,
  `medio` longblob DEFAULT NULL,
  `nivel` int(10) DEFAULT NULL,
  `piezas` int(100) DEFAULT NULL,
  `estatusplano` int(10) NOT NULL,
  `actividad` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `quotes` (
  `id` int(255) NOT NULL,
  `solicitante` varchar(300) DEFAULT NULL,
  `rol` varchar(100) DEFAULT NULL,
  `proyecto` varchar(150) DEFAULT NULL,
  `medio` longblob DEFAULT NULL,
  `estatusq` varchar(150) DEFAULT NULL,
  `cotizacion` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `bom` (
  `id` int(255) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `piezas` int(255) NOT NULL,
  `proveedor` varchar(200) NOT NULL,
  `descripcion` varchar(1000) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `condicion` varchar(100) NOT NULL,
  `costo` float(100,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `historialoperadores` (
  `id` int(255) NOT NULL,
  `idcodigo` int(255) NOT NULL,
  `idplano` int(255) NOT NULL,
  `motivoactividad` varchar(1000) NOT NULL,
  `fecha` date NOT NULL,
  `hora` varchar(30) NOT NULL,
  `fechareinicio` date DEFAULT NULL,
  `horareinicio` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `historialoperadores`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `historialoperadores`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

CREATE TABLE `historialensamble` (
  `id` int(255) NOT NULL,
  `idcodigo` int(255) NOT NULL,
  `idplano` int(255) NOT NULL,
  `motivoactividad` varchar(1000) NOT NULL,
  `fecha` date NOT NULL,
  `hora` varchar(30) NOT NULL,
  `fechareinicio` date DEFAULT NULL,
  `horareinicio` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `historialensamble`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `historialensamble`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `bom`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `bom`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;


ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `quotes`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

ALTER TABLE `diagrama`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `diagrama`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

  CREATE TABLE `asignaciondiagrama` (
  `id` int(255) NOT NULL,
  `idplano` int(255) NOT NULL,
  `codigooperador` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `asignaciondiagrama`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `asignaciondiagrama`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

