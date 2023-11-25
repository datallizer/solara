
CREATE TABLE `asignacionplano` (
  `id` int(100) NOT NULL,
  `idplano` int(100) DEFAULT NULL,
  `codigooperador` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `asignacionplano` (`id`, `idplano`, `codigooperador`) VALUES
(2, 2, 456),
(3, 3, 456),
(4, 4, 66966),
(5, 5, 0),
(6, 6, 456),
(7, 7, 66666);


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


INSERT INTO `historial` (`id`, `idcodigo`, `detalles`, `hora`, `fecha`) VALUES
(1, 12345, 'Creo un nuevo usuario \r\nNombre: Operador Uno Test\r\nCodigo: 456\r\nRol: Operador', '13:25', '2023-11-16');



CREATE TABLE `plano` (
  `id` int(100) NOT NULL,
  `idproyecto` int(100) DEFAULT NULL,
  `nombreplano` varchar(100) DEFAULT NULL,
  `medio` longblob DEFAULT NULL,
  `nivel` int(10) DEFAULT NULL,
  `piezas` int(100) DEFAULT NULL,
  `etapa` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `plano` (`id`, `idproyecto`, `nombreplano`, `medio`, `nivel`, `piezas`, `etapa`) VALUES
(7, 1, 'fffffff', 0x343439363337343931355365707469656d6272652d323032332e706466, 3, 3, 3);



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

INSERT INTO `proyecto` (`id`, `nombre`, `detalles`, `cliente`, `prioridad`, `fechainicio`, `fechafin`, `presupuesto`, `estatus`) VALUES
(1, 'Proyecto1', 's de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creació', 'Cliente 1', 1, '2023-11-16', '2023-11-30', 85542, 1),
(2, 'Datallizer', 'Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas \"Letraset\", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.', 'David', 3, '2023-11-01', '2024-01-25', 85, 1),
(3, 'Solara', 'Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas \"Letraset\", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas \"Letraset\", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.', 'Miguel Villa', 4, '2023-11-01', '2023-11-30', 50, 1),
(4, 'Prueba', 'Lorem Ipsum es simplemente el texto de relleno de las imprentas y archivos de texto. Lorem Ipsum ha sido el texto de relleno estándar de las industrias desde el año 1500, cuando un impresor (N. del T. persona que se dedica a la imprenta) desconocido usó una galería de textos y los mezcló de tal manera que logró hacer un libro de textos especimen. No sólo sobrevivió 500 años, sino que tambien ingresó como texto de relleno en documentos electrónicos, quedando esencialmente igual al original. Fue popularizado en los 60s con la creación de las hojas \"Letraset\", las cuales contenian pasajes de Lorem Ipsum, y más recientemente con software de autoedición, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum.', 'Prueba', 2, '2023-11-09', '2024-02-23', 85000, NULL);


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
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `encargadoproyecto`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT;

ALTER TABLE `historial`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `plano`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `proyecto`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `usuarios`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
