
CREATE TABLE IF NOT EXISTS festivos_globales (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    mes INT(2) NOT NULL,
    dia INT(2) NOT NULL,
    descripcion TEXT DEFAULT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_festivo (mes, dia, titulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELETE FROM festivos_globales;
-- ENERO
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Año Nuevo', 1, 1, 'Celebración del primer día del año'),
('Día de los Reyes Magos', 1, 6, 'Celebración tradicional de la Epifanía'),
('Día Mundial de la Paz', 1, 1, 'Día internacional de la paz'),
('Día de Martin Luther King Jr.', 1, 15, 'Conmemoración del líder de los derechos civiles'),
('Día Mundial de la Religión', 1, 21, 'Celebración de la diversidad religiosa');

-- FEBRERO
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día de la Candelaria', 2, 2, 'Festividad cristiana tradicional'),
('Día de San Valentín', 2, 14, 'Día de los enamorados'),
('Día Mundial de la Justicia Social', 2, 20, 'Promoción de la justicia social'),
('Día Internacional de la Lengua Materna', 2, 21, 'Promoción de la diversidad lingüística'),
('Carnaval', 2, 28, 'Celebración tradicional antes de la Cuaresma');

-- MARZO
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Internacional de la Mujer', 3, 8, 'Celebración de los derechos de la mujer'),
('Día Internacional de la Felicidad', 3, 20, 'Promoción del bienestar y la felicidad'),
('Día Mundial de la Poesía', 3, 21, 'Celebración de la expresión poética'),
('Día Mundial del Agua', 3, 22, 'Concienciación sobre la importancia del agua'),
('Día Mundial del Teatro', 3, 27, 'Celebración del arte teatral');

-- ABRIL
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Internacional del Deporte', 4, 6, 'Promoción del deporte y la actividad física'),
('Día Mundial de la Salud', 4, 7, 'Concienciación sobre temas de salud global'),
('Día de la Tierra', 4, 22, 'Concienciación ambiental'),
('Día Internacional del Libro', 4, 23, 'Promoción de la lectura y la literatura'),
('Día Mundial de la Danza', 4, 29, 'Celebración del arte de la danza');

-- MAYO
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día del Trabajador', 5, 1, 'Celebración internacional del trabajo'),
('Día Mundial de la Libertad de Prensa', 5, 3, 'Defensa de la libertad de expresión'),
('Día Mundial del Medio Ambiente', 5, 5, 'Concienciación ambiental'),
('Día de la Madre', 5, 10, 'Homenaje a las madres'),
('Día Internacional de los Museos', 5, 18, 'Promoción del patrimonio cultural');

-- JUNIO
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Mundial del Medio Ambiente', 6, 5, 'Concienciación sobre el medio ambiente'),
('Día Mundial de los Océanos', 6, 8, 'Protección de los océanos'),
('Día del Padre', 6, 21, 'Homenaje a los padres'),
('Día Internacional del Yoga', 6, 21, 'Promoción del bienestar físico y mental'),
('Día Mundial de la Música', 6, 21, 'Celebración de la música');

-- JULIO
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Mundial de la Población', 7, 11, 'Concienciación sobre temas demográficos'),
('Día Internacional de la Justicia Penal', 7, 17, 'Promoción de la justicia internacional'),
('Día Mundial de los Abuelos', 7, 26, 'Homenaje a los abuelos'),
('Día Mundial contra la Hepatitis', 7, 28, 'Concienciación sobre la hepatitis'),
('Día Internacional de la Amistad', 7, 30, 'Celebración de la amistad');

-- AGOSTO
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Mundial del Gato', 8, 8, 'Celebración de los felinos domésticos'),
('Día Internacional de los Pueblos Indígenas', 8, 9, 'Reconocimiento de los pueblos originarios'),
('Día Internacional de la Juventud', 8, 12, 'Celebración de los jóvenes'),
('Día Mundial de la Fotografía', 8, 19, 'Celebración del arte fotográfico'),
('Día Internacional de la Solidaridad', 8, 31, 'Promoción de la solidaridad humana');

-- SEPTIEMBRE
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Internacional de la Caridad', 9, 5, 'Promoción de obras caritativas'),
('Día Internacional de la Alfabetización', 9, 8, 'Promoción de la educación'),
('Día Internacional de la Paz', 9, 21, 'Promoción de la paz mundial'),
('Día Mundial del Turismo', 9, 27, 'Promoción del turismo sostenible'),
('Día Mundial del Corazón', 9, 29, 'Concienciación sobre enfermedades cardiovasculares');

-- OCTUBRE
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Mundial de los Animales', 10, 4, 'Protección y bienestar animal'),
('Día Mundial de los Docentes', 10, 5, 'Reconocimiento a los educadores'),
('Día Mundial de la Salud Mental', 10, 10, 'Concienciación sobre salud mental'),
('Día de las Naciones Unidas', 10, 24, 'Celebración de la ONU'),
('Halloween', 10, 31, 'Celebración tradicional de Halloween');

-- NOVIEMBRE
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día de Todos los Santos', 11, 1, 'Festividad cristiana tradicional'),
('Día Mundial de la Ciencia', 11, 10, 'Promoción de la ciencia'),
('Día Internacional del Estudiante', 11, 17, 'Celebración de los estudiantes'),
('Día Mundial de la Filosofía', 11, 21, 'Promoción del pensamiento filosófico'),
('Día Internacional contra la Violencia de Género', 11, 25, 'Lucha contra la violencia hacia las mujeres');

-- DICIEMBRE
INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES
('Día Mundial del SIDA', 12, 1, 'Concienciación sobre el VIH/SIDA'),
('Día Internacional de los Derechos Humanos', 12, 10, 'Promoción de los derechos humanos'),
('Nochebuena', 12, 24, 'Víspera de Navidad'),
('Navidad', 12, 25, 'Celebración del nacimiento de Jesucristo'),
('Nochevieja', 12, 31, 'Último día del año');

INSERT INTO eventos (usuario_id, titulo, fecha, categoria, descripcion)
SELECT 
    u.id as usuario_id,
    f.titulo,
    CONCAT('2025-', LPAD(f.mes, 2, '0'), '-', LPAD(f.dia, 2, '0')) as fecha,
    'party' as categoria,
    f.descripcion
FROM usuarios u
CROSS JOIN festivos_globales f
WHERE NOT EXISTS (
    SELECT 1 FROM eventos e 
    WHERE e.usuario_id = u.id 
    AND e.titulo = f.titulo 
    AND YEAR(e.fecha) = 2025 
    AND e.categoria = 'party'
);

SELECT 
    'Festivos globales creados' as operacion,
    COUNT(*) as cantidad
FROM festivos_globales
UNION ALL
SELECT 
    'Eventos festivos creados para usuarios' as operacion,
    COUNT(*) as cantidad
FROM eventos 
WHERE categoria = 'party' AND YEAR(fecha) = 2025;
