<?php
require_once "config.php";

try {
    $sql_table = "CREATE TABLE IF NOT EXISTS festivos_globales (
        id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        titulo VARCHAR(255) NOT NULL,
        mes INT(2) NOT NULL,
        dia INT(2) NOT NULL,
        descripcion TEXT DEFAULT NULL,
        fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY unique_festivo (mes, dia, titulo)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
    
    $conn->exec($sql_table);
    echo "Tabla festivos_globales creada correctamente.<br>";
    
    $conn->exec("DELETE FROM festivos_globales");
    echo "Festivos anteriores eliminados.<br>";
    
    $festivos = [
        1 => [
            ['titulo' => 'Año Nuevo', 'dia' => 1, 'descripcion' => 'Celebración del primer día del año'],
            ['titulo' => 'Día de los Reyes Magos', 'dia' => 6, 'descripcion' => 'Celebración tradicional de la Epifanía'],
            ['titulo' => 'Día Mundial de la Paz', 'dia' => 1, 'descripcion' => 'Día internacional de la paz'],
            ['titulo' => 'Día de Martin Luther King Jr.', 'dia' => 15, 'descripcion' => 'Conmemoración del líder de los derechos civiles'],
            ['titulo' => 'Día Mundial de la Religión', 'dia' => 21, 'descripcion' => 'Celebración de la diversidad religiosa']
        ],
        2 => [
            ['titulo' => 'Día de la Candelaria', 'dia' => 2, 'descripcion' => 'Festividad cristiana tradicional'],
            ['titulo' => 'Día de San Valentín', 'dia' => 14, 'descripcion' => 'Día de los enamorados'],
            ['titulo' => 'Día Mundial de la Justicia Social', 'dia' => 20, 'descripcion' => 'Promoción de la justicia social'],
            ['titulo' => 'Día Internacional de la Lengua Materna', 'dia' => 21, 'descripcion' => 'Promoción de la diversidad lingüística'],
            ['titulo' => 'Carnaval', 'dia' => 28, 'descripcion' => 'Celebración tradicional antes de la Cuaresma']
        ],
        3 => [
            ['titulo' => 'Día Internacional de la Mujer', 'dia' => 8, 'descripcion' => 'Celebración de los derechos de la mujer'],
            ['titulo' => 'Día Mundial del Agua', 'dia' => 22, 'descripcion' => 'Concienciación sobre la importancia del agua'],
            ['titulo' => 'Día Internacional de la Felicidad', 'dia' => 20, 'descripcion' => 'Promoción del bienestar y la felicidad'],
            ['titulo' => 'Día Mundial de la Poesía', 'dia' => 21, 'descripcion' => 'Celebración de la expresión poética'],
            ['titulo' => 'Día Mundial del Teatro', 'dia' => 27, 'descripcion' => 'Celebración del arte teatral']
        ],
        4 => [
            ['titulo' => 'Día Internacional del Libro', 'dia' => 23, 'descripcion' => 'Promoción de la lectura y la literatura'],
            ['titulo' => 'Día Mundial de la Salud', 'dia' => 7, 'descripcion' => 'Concienciación sobre temas de salud global'],
            ['titulo' => 'Día de la Tierra', 'dia' => 22, 'descripcion' => 'Concienciación ambiental'],
            ['titulo' => 'Día Mundial de la Danza', 'dia' => 29, 'descripcion' => 'Celebración del arte de la danza'],
            ['titulo' => 'Día Internacional del Deporte', 'dia' => 6, 'descripcion' => 'Promoción del deporte y la actividad física']
        ],
        5 => [
            ['titulo' => 'Día del Trabajador', 'dia' => 1, 'descripcion' => 'Celebración internacional del trabajo'],
            ['titulo' => 'Día de la Madre', 'dia' => 10, 'descripcion' => 'Homenaje a las madres'],
            ['titulo' => 'Día Mundial de la Libertad de Prensa', 'dia' => 3, 'descripcion' => 'Defensa de la libertad de expresión'],
            ['titulo' => 'Día Mundial del Medio Ambiente', 'dia' => 5, 'descripcion' => 'Concienciación ambiental'],
            ['titulo' => 'Día Internacional de los Museos', 'dia' => 18, 'descripcion' => 'Promoción del patrimonio cultural']
        ],
        6 => [
            ['titulo' => 'Día Mundial del Medio Ambiente', 'dia' => 5, 'descripcion' => 'Concienciación sobre el medio ambiente'],
            ['titulo' => 'Día Mundial de los Océanos', 'dia' => 8, 'descripcion' => 'Protección de los océanos'],
            ['titulo' => 'Día del Padre', 'dia' => 21, 'descripcion' => 'Homenaje a los padres'],
            ['titulo' => 'Día Internacional del Yoga', 'dia' => 21, 'descripcion' => 'Promoción del bienestar físico y mental'],
            ['titulo' => 'Día Mundial de la Música', 'dia' => 21, 'descripcion' => 'Celebración de la música']
        ],
        7 => [
            ['titulo' => 'Día Mundial de la Población', 'dia' => 11, 'descripcion' => 'Concienciación sobre temas demográficos'],
            ['titulo' => 'Día Internacional de la Justicia Penal', 'dia' => 17, 'descripcion' => 'Promoción de la justicia internacional'],
            ['titulo' => 'Día Mundial contra la Hepatitis', 'dia' => 28, 'descripcion' => 'Concienciación sobre la hepatitis'],
            ['titulo' => 'Día Internacional de la Amistad', 'dia' => 30, 'descripcion' => 'Celebración de la amistad'],
            ['titulo' => 'Día Mundial de los Abuelos', 'dia' => 26, 'descripcion' => 'Homenaje a los abuelos']
        ],
        8 => [
            ['titulo' => 'Día Internacional de la Juventud', 'dia' => 12, 'descripcion' => 'Celebración de los jóvenes'],
            ['titulo' => 'Día Mundial de la Fotografía', 'dia' => 19, 'descripcion' => 'Celebración del arte fotográfico'],
            ['titulo' => 'Día Internacional de los Pueblos Indígenas', 'dia' => 9, 'descripcion' => 'Reconocimiento de los pueblos originarios'],
            ['titulo' => 'Día Mundial del Gato', 'dia' => 8, 'descripcion' => 'Celebración de los felinos domésticos'],
            ['titulo' => 'Día Internacional de la Solidaridad', 'dia' => 31, 'descripcion' => 'Promoción de la solidaridad humana']
        ],
        9 => [
            ['titulo' => 'Día Internacional de la Paz', 'dia' => 21, 'descripcion' => 'Promoción de la paz mundial'],
            ['titulo' => 'Día Mundial del Turismo', 'dia' => 27, 'descripcion' => 'Promoción del turismo sostenible'],
            ['titulo' => 'Día Internacional de la Caridad', 'dia' => 5, 'descripcion' => 'Promoción de obras caritativas'],
            ['titulo' => 'Día Mundial del Corazón', 'dia' => 29, 'descripcion' => 'Concienciación sobre enfermedades cardiovasculares'],
            ['titulo' => 'Día Internacional de la Alfabetización', 'dia' => 8, 'descripcion' => 'Promoción de la educación']
        ],
        10 => [
            ['titulo' => 'Día Mundial de los Animales', 'dia' => 4, 'descripcion' => 'Protección y bienestar animal'],
            ['titulo' => 'Día Mundial de los Docentes', 'dia' => 5, 'descripcion' => 'Reconocimiento a los educadores'],
            ['titulo' => 'Día Mundial de la Salud Mental', 'dia' => 10, 'descripcion' => 'Concienciación sobre salud mental'],
            ['titulo' => 'Día de las Naciones Unidas', 'dia' => 24, 'descripcion' => 'Celebración de la ONU'],
            ['titulo' => 'Halloween', 'dia' => 31, 'descripcion' => 'Celebración tradicional de Halloween']
        ],
        11 => [
            ['titulo' => 'Día de Todos los Santos', 'dia' => 1, 'descripcion' => 'Festividad cristiana tradicional'],
            ['titulo' => 'Día Mundial de la Ciencia', 'dia' => 10, 'descripcion' => 'Promoción de la ciencia'],
            ['titulo' => 'Día Internacional del Estudiante', 'dia' => 17, 'descripcion' => 'Celebración de los estudiantes'],
            ['titulo' => 'Día Mundial de la Filosofía', 'dia' => 21, 'descripcion' => 'Promoción del pensamiento filosófico'],
            ['titulo' => 'Día Internacional contra la Violencia de Género', 'dia' => 25, 'descripcion' => 'Lucha contra la violencia hacia las mujeres']
        ],
        12 => [
            ['titulo' => 'Día Mundial del SIDA', 'dia' => 1, 'descripcion' => 'Concienciación sobre el VIH/SIDA'],
            ['titulo' => 'Día Internacional de los Derechos Humanos', 'dia' => 10, 'descripcion' => 'Promoción de los derechos humanos'],
            ['titulo' => 'Nochebuena', 'dia' => 24, 'descripcion' => 'Víspera de Navidad'],
            ['titulo' => 'Navidad', 'dia' => 25, 'descripcion' => 'Celebración del nacimiento de Jesucristo'],
            ['titulo' => 'Nochevieja', 'dia' => 31, 'descripcion' => 'Último día del año']
        ]
    ];
    
    $stmt = $conn->prepare("INSERT INTO festivos_globales (titulo, mes, dia, descripcion) VALUES (?, ?, ?, ?)");
    
    $count = 0;
    foreach ($festivos as $mes => $festivosDelMes) {
        foreach ($festivosDelMes as $festivo) {
            $stmt->execute([$festivo['titulo'], $mes, $festivo['dia'], $festivo['descripcion']]);
            $count++;
        }
    }
    
    echo "Se han insertado $count días festivos en la tabla festivos_globales.<br>";
    
    crearEventosFestivosParaUsuarios($conn);
    
    echo "<br>Proceso completado exitosamente.";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

function crearEventosFestivosParaUsuarios($conn) {
    $usuarios = $conn->query("SELECT id FROM usuarios")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($usuarios)) {
        echo "No hay usuarios en la base de datos.<br>";
        return;
    }
    
    $año_actual = date('Y');
    
    $conn->prepare("DELETE FROM eventos WHERE categoria = 'party' AND YEAR(fecha) = ?")->execute([$año_actual]);
    
    $festivos = $conn->query("SELECT * FROM festivos_globales")->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("INSERT INTO eventos (usuario_id, titulo, fecha, categoria, descripcion) VALUES (?, ?, ?, 'party', ?)");
    
    $eventos_creados = 0;
    
    foreach ($usuarios as $usuario_id) {
        foreach ($festivos as $festivo) {
            $fecha = sprintf('%04d-%02d-%02d', $año_actual, $festivo['mes'], $festivo['dia']);
            
            try {
                $stmt->execute([
                    $usuario_id,
                    $festivo['titulo'],
                    $fecha,
                    $festivo['descripcion']
                ]);
                $eventos_creados++;
            } catch(PDOException $e) {
                if ($e->getCode() != 23000) {
                    echo "Error al crear evento: " . $e->getMessage() . "<br>";
                }
            }
        }
    }
    
    echo "Se han creado $eventos_creados eventos festivos para " . count($usuarios) . " usuarios.<br>";
}

function añadirFestivosANuevoUsuario($conn, $usuario_id, $año = null) {
    if ($año === null) {
        $año = date('Y');
    }
    
    $festivos = $conn->query("SELECT * FROM festivos_globales")->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("INSERT INTO eventos (usuario_id, titulo, fecha, categoria, descripcion) VALUES (?, ?, ?, 'party', ?)");
    
    foreach ($festivos as $festivo) {
        $fecha = sprintf('%04d-%02d-%02d', $año, $festivo['mes'], $festivo['dia']);
        
        try {
            $stmt->execute([
                $usuario_id,
                $festivo['titulo'],
                $fecha,
                $festivo['descripcion']
            ]);
        } catch(PDOException $e) {
            if ($e->getCode() != 23000) {
                error_log("Error al añadir festivo para nuevo usuario: " . $e->getMessage());
            }
        }
    }
}

$conn = null;
?>