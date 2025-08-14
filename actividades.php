<?php
// Configuración para mostrar errores durante el desarrollo.
// ¡IMPORTANTE! Desactiva o elimina estas líneas en un entorno de producción.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye el encabezado de la página, que maneja la sesión y otros elementos comunes.
include 'encabezado.php';

// Redirige al usuario a la página de inicio de sesión si no ha iniciado sesión.
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit; // Detiene la ejecución del script después de la redirección.
}

// Requiere el archivo de conexión a la base de datos.
require 'database.php';

// Obtiene el ID de usuario de la sesión y una variable para mensajes.
$id_usuario = $_SESSION['user_id'];
$mensaje = '';

// --- Cargar los grados de actividad para el formulario ---
// Consulta la tabla 'grado_actividad' para obtener los IDs y grados.
// Se ha eliminado 'recompensa' de la selección ya que no existe en la tabla 'grado_actividad' según base_gestion.sql.
$grados_actividad = [];
$stmt_grados = $conexion->prepare("SELECT id_grado_actividad, grado FROM grado_actividad ORDER BY id_grado_actividad ASC"); // Ordenar por ID o grado
$stmt_grados->execute();
$result_grados = $stmt_grados->get_result();
while ($row_grado = $result_grados->fetch_assoc()) {
    $grados_actividad[] = $row_grado;
}
$stmt_grados->close();

// --- Procesar añadir nueva actividad ---
// Se ejecuta si el método de la solicitud es POST y la acción es 'agregar_actividad'.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'agregar_actividad') {
    // Recoge los datos del formulario, usando el operador de fusión de null para valores por defecto.
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $fecha_fin = $_POST['fecha_fin'] ?? '';
    $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d'); // Usa la fecha actual si no se proporciona.
    $id_grado_actividad_seleccionado = $_POST['id_grado_actividad'] ?? 1; // ID por defecto para 'Basico' (ID 1).
    // Se asume id_lista_actividades_personal = 1 para 'Tareas Personales' como valor por defecto.
    $id_lista_actividades_personal = 1; 

    // Valida que los campos obligatorios no estén vacíos.
    if (!empty($titulo) && !empty($fecha_fin) && !empty($fecha_inicio)) {
        // ID para 'Incompleta' en la tabla 'estado_actividad' (asumiendo ID 2).
        $id_estado_actividad_incompleta = 2; 

        // Prepara la consulta SQL para insertar una nueva actividad.
        // Los tipos de parámetros son: i (int), s (string), s (string), s (string), s (string), i (int), i (int), i (int).
        $stmt_insert_actividad = $conexion->prepare("INSERT INTO actividad (id_usuario, fecha_inicio, fecha_fin, titulo, descripcion, id_estado_actividad, id_lista_actividades, id_grado_actividad) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_insert_actividad->bind_param("isssiiii", 
            $id_usuario, 
            $fecha_inicio, 
            $fecha_fin, 
            $titulo, 
            $descripcion, 
            $id_estado_actividad_incompleta, 
            $id_lista_actividades_personal,
            $id_grado_actividad_seleccionado
        );

        // Ejecuta la consulta y verifica si fue exitosa.
        if ($stmt_insert_actividad->execute()) {
            $mensaje = "Actividad creada con éxito.";
            // Redirige a home.php después de la inserción exitosa.
            header("Location: home.php?mensaje=" . urlencode($mensaje));
            exit(); // Crucial para asegurar que la redirección se complete.
        } else {
            $mensaje = "Error al crear la actividad: " . $stmt_insert_actividad->error;
        }
        $stmt_insert_actividad->close();
    } else {
        $mensaje = "Por favor, completa todos los campos requeridos (Título, Fecha de Inicio, Fecha de Fin).";
    }
}

// --- Procesar completar actividad ---
// Se ejecuta si el método de la solicitud es POST y la acción es 'completar_actividad'.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'completar_actividad') {
    $id_actividad_completada = $_POST['id_actividad'] ?? 0;

    // Inicia una transacción para asegurar la integridad de los datos (actualización y posible logro).
    $conexion->begin_transaction();
    try {
        // 1. Cambia el estado de la actividad a 'Completada' (asumiendo id_estado_actividad = 1).
        $stmt_update_estado = $conexion->prepare("UPDATE actividad SET id_estado_actividad = 1 WHERE id_actividad = ? AND id_usuario = ?");
        $stmt_update_estado->bind_param("ii", $id_actividad_completada, $id_usuario);
        $stmt_update_estado->execute();
        $stmt_update_estado->close();

        // 2. Obtiene el id_grado_actividad de la actividad recién completada.
        $id_grado_actividad_de_actividad_completada = null;
        $stmt_get_grado = $conexion->prepare("SELECT id_grado_actividad FROM actividad WHERE id_actividad = ?");
        $stmt_get_grado->bind_param("i", $id_actividad_completada);
        $stmt_get_grado->execute();
        $stmt_get_grado->bind_result($id_grado_actividad_de_actividad_completada);
        $stmt_get_grado->fetch();
        $stmt_get_grado->close();

        // 3. Asigna la recompensa de monedas según el id_grado_actividad.
        // NOTA: La tabla 'grado_actividad' en base_gestion.sql no tiene una columna 'recompensa'.
        // Esta lógica asigna recompensas fijas basadas en el ID del grado.
        $recompensa_monedas = 0; // Valor por defecto.
        if ($id_grado_actividad_de_actividad_completada == 1) { // Básico
            $recompensa_monedas = 5;
        } elseif ($id_grado_actividad_de_actividad_completada == 2) { // Intermedio
            $recompensa_monedas = 10;
        } elseif ($id_grado_actividad_de_actividad_completada == 3) { // Importante
            $recompensa_monedas = 20;
        }


        // 4. Suma la recompensa al balance del usuario.
        if ($recompensa_monedas > 0) {
            $stmt_update_balance = $conexion->prepare("UPDATE balance SET total_moneda = total_moneda + ? WHERE id_usuario = ?");
            $stmt_update_balance->bind_param("ii", $recompensa_monedas, $id_usuario);
            $stmt_update_balance->execute();
            $stmt_update_balance->close();
            $mensaje = "¡Actividad completada y has ganado $recompensa_monedas monedas!";
        } else {
             $mensaje = "Actividad completada. No se otorgaron monedas para este grado o el grado no se encontró.";
        }

        $conexion->commit(); // Confirma la transacción si todo fue exitoso.
    } catch (mysqli_sql_exception $e) {
        $conexion->rollback(); // Revierte la transacción si ocurre un error.
        $mensaje = "Error al completar la actividad: " . $e->getMessage();
    }
}

// --- Procesar eliminar actividad ---
// Se ejecuta si el método de la solicitud es POST y la acción es 'eliminar_actividad'.
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'eliminar_actividad') {
    $id_actividad_a_eliminar = $_POST['id_actividad'] ?? 0;

    if ($id_actividad_a_eliminar > 0) {
        // Prepara la consulta para eliminar la actividad.
        $stmt_delete = $conexion->prepare("DELETE FROM actividad WHERE id_actividad = ? AND id_usuario = ?");
        $stmt_delete->bind_param("ii", $id_actividad_a_eliminar, $id_usuario);
        if ($stmt_delete->execute()) {
            if ($stmt_delete->affected_rows > 0) {
                $mensaje = "Actividad eliminada con éxito.";
            } else {
                $mensaje = "La actividad no se encontró o no tienes permiso para eliminarla.";
            }
        } else {
            $mensaje = "Error al eliminar la actividad: " . $stmt_delete->error;
        }
        $stmt_delete->close();
    }
}

// --- Obtener listas de actividades para el filtro y el formulario ---
// Se asegura de usar 'nombre' como nombre de columna, según base_gestion.sql.
$listas_actividades = [];
$stmt_listas = $conexion->query("SELECT id_lista_actividades, nombre FROM lista_actividades");
while ($fila = $stmt_listas->fetch_assoc()) {
    $listas_actividades[] = $fila;
}

// --- Obtener grados de actividad para el formulario ---
// Se asegura de usar 'grado' como nombre de columna, según base_gestion.sql.
$grados_actividad = [];
$stmt_grados = $conexion->query("SELECT id_grado_actividad, grado FROM grado_actividad");
while ($fila = $stmt_grados->fetch_assoc()) {
    $grados_actividad[] = $fila;
}

// --- Filtro de actividades ---
// Obtiene los parámetros de filtro de la URL (GET).
$filtro_lista = $_GET['lista'] ?? '';
$filtro_estado = $_GET['estado'] ?? '';

// Construye la consulta SQL base para obtener todas las actividades del usuario.
// Se asegura de usar 'estado', 'nombre' (para lista) y 'grado' como nombres de columna/alias.
$sql_actividades = "
    SELECT 
        a.id_actividad, 
        a.titulo, 
        a.descripcion, 
        a.fecha_inicio, 
        a.fecha_fin, 
        ea.estado AS estado, 
        la.nombre AS lista, 
        ga.grado AS grado
    FROM 
        actividad a
    JOIN 
        estado_actividad ea ON a.id_estado_actividad = ea.id_estado_actividad
    JOIN
        lista_actividades la ON a.id_lista_actividades = la.id_lista_actividades
    JOIN
        grado_actividad ga ON a.id_grado_actividad = ga.id_grado_actividad
    WHERE 
        a.id_usuario = ?
";
$params_types = "i"; // Tipo de parámetro para id_usuario.
$params = [$id_usuario]; // Array de parámetros.

// Añade condiciones de filtro si se proporcionan.
if (!empty($filtro_lista)) {
    $sql_actividades .= " AND la.id_lista_actividades = ?";
    $params_types .= "i";
    $params[] = $filtro_lista;
}

if (!empty($filtro_estado)) {
    $sql_actividades .= " AND ea.id_estado_actividad = ?";
    $params_types .= "i";
    $params[] = $filtro_estado;
}

// Ordena las actividades por fecha de fin.
$sql_actividades .= " ORDER BY a.fecha_fin ASC";

// Prepara, enlaza y ejecuta la consulta para obtener todas las actividades filtradas.
$stmt_all_actividades = $conexion->prepare($sql_actividades);
$stmt_all_actividades->bind_param($params_types, ...$params);
$stmt_all_actividades->execute();
$todas_las_actividades = $stmt_all_actividades->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_all_actividades->close();

// Cierra la conexión a la base de datos al final del script.
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mis Actividades - Gestión de Tareas</title>
</head>
<body class="home-body">
    <?php
        // Incluye el encabezado de la página, que maneja la sesión y otros elementos comunes.
        // include 'encabezado.php'; // Ya incluido al principio del script
    ?>

    <div class="dashboard-container">
        <h1 class="dashboard-h1">Mis Actividades</h1>

        <?php 
            // Muestra mensajes de éxito o error.
            if (!empty($mensaje)): ?>
            <p class="message"><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <div class="dashboard-section create-activity-section">
            <h2 class="dashboard-h2">Añadir Nueva Actividad</h2>
            <form action="actividades.php" method="post" class="dashboard-form">
                <input type="hidden" name="accion" value="agregar_actividad">
                
                <div class="form-group">
                    <label for="titulo">Título de la Actividad:</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción:</label>
                    <textarea id="descripcion" name="descripcion"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="fecha_inicio">Fecha de Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                
                <div class="form-group">
                    <label for="fecha_fin">Fecha de Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" required>
                </div>

                <div class="form-group">
                    <label for="id_grado_actividad">Grado de la Actividad:</label>
                    <select class="dashboard-select" id="id_grado_actividad" name="id_grado_actividad" required>
                        <?php foreach ($grados_actividad as $grado): ?>
                            <option class="dashboard-option" value="<?php echo htmlspecialchars($grado['id_grado_actividad']); ?>">
                                <?php echo htmlspecialchars($grado['grado']); ?> 
                                <!-- Se eliminó la referencia a 'recompensa' aquí -->
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="dashboard-button">Añadir Actividad</button>
            </form>
        </div>

        <div class="dashboard-section your-activities-section">
            <h2 class="dashboard-h2">Tus Actividades</h2>
            <?php if (empty($todas_las_actividades)): // Se usa $todas_las_actividades para la lista principal ?>
                <p class="no-items-message">No tienes actividades programadas. ¡Añade algunas para empezar a ganar monedas!</p>
            <?php else: ?>
                <ul class="item-grid">
                    <?php foreach ($todas_las_actividades as $actividad): ?>
                        <li class="item-card activity-card">
                            <div class="item-header">
                                <h3><?php echo htmlspecialchars($actividad['titulo']); ?></h3>
                                <span class="status-badge status-<?php echo strtolower($actividad['estado']); ?>">
                                    <?php echo htmlspecialchars($actividad['estado']); ?>
                                </span>
                            </div>
                            <p class="item-description"><?php echo htmlspecialchars($actividad['descripcion']); ?></p>
                            <p class="item-details">
                                Grado: <span><?php echo htmlspecialchars($actividad['grado']); ?> 
                                <!-- Se eliminó la referencia a 'recompensa' aquí -->
                                </span><br>
                                Inicio: <?php echo htmlspecialchars($actividad['fecha_inicio']); ?><br>
                                Fin: <?php echo htmlspecialchars($actividad['fecha_fin']); ?>
                            </p>
                            <div class="item-actions">
                                <?php if ($actividad['estado'] !== 'Completada'): ?>
                                    <form action="actividades.php" method="post" class="inline-form">
                                        <input type="hidden" name="accion" value="completar_actividad">
                                        <input type="hidden" name="id_actividad" value="<?php echo $actividad['id_actividad']; ?>">
                                        <button type="submit" class="action-button complete-button">Completar</button>
                                    </form>
                                <?php endif; ?>
                                <form action="actividades.php" method="post" class="inline-form">
                                    <input type="hidden" name="accion" value="eliminar_actividad">
                                    <input type="hidden" name="id_actividad" value="<?php echo $actividad['id_actividad']; ?>">
                                    <button type="submit" class="action-button delete-button">Eliminar</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>