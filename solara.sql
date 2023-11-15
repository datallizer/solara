CREATE DATABASE solara;
USE solara;
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


CREATE TABLE `plano` (
  `id` int(100) NOT NULL,
  `idproyecto` int(100) DEFAULT NULL,
  `nombre` int(100) DEFAULT NULL,
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
  `presupuesto` int(100) DEFAULT NULL
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
(1, 'Miguel', 'Villa', 'Solara', 1, 12345, 1),
(2, 'Prueba', 'Prueba', 'Prueba', 6, 0, 1);

ALTER TABLE `asignacionplano`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `encargadoproyecto`
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

ALTER TABLE `plano`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

ALTER TABLE `proyecto`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

ALTER TABLE `usuarios`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
