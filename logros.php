<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Mis Logros - Gamificación</title>
</head>
<body class="home-body">
    <?php
        include 'encabezado.php';
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }
        require 'database.php';

        $id_usuario = $_SESSION['user_id'];
        $mensaje = '';

        // Procesar añadir nuevo logro
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'agregar_logro') {
            $titulo = $_POST['titulo'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $recompensa = $_POST['recompensa'] ?? 0;

            if (!empty($titulo) && $recompensa >= 0) {
                // Estado por defecto: "Incompleto" (asumiendo id_estado_logro = 2 para incompleto)
                // Y lista_actividades por defecto: "Tareas Personales" (asumiendo id_lista_actividades = 1 para personal)
                // Asumiendo que un logro creado por el usuario está asociado a una lista de actividades,
                // por simplicidad lo asociaremos a la lista personal por ahora.
                $id_estado_logro_incompleto = 2; // Asegúrate de que este ID coincida con tu base de datos
                $id_lista_actividades_personal = 1; // Asegúrate de que este ID coincida con tu base de datos

                $stmt = $conexion->prepare("INSERT INTO logro (id_usuario, titulo, descripcion, recompensa_dinero, fecha_creacion, id_estado_logro, id_lista_actividades) VALUES (?, ?, ?, ?, CURDATE(), ?, ?)");
                $stmt->bind_param("sssiii", $id_usuario, $titulo, $descripcion, $recompensa, $id_estado_logro_incompleto, $id_lista_actividades_personal);

                if ($stmt->execute()) {
                    $mensaje = "<p class='mensaje-exito'>Logro '$titulo' añadido con éxito.</p>";
                } else {
                    $mensaje = "<p class='mensaje-error'>Error al añadir logro: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                $mensaje = "<p class='mensaje-error'>Por favor, completa el título y la recompensa del logro (debe ser un número positivo).</p>";
            }
        }

        // Obtener todos los logros del usuario
        $logros = [];
        $stmt_logros = $conexion->prepare("SELECT L.id_logro, L.titulo, L.descripcion, L.recompensa_dinero, ES.estado FROM logro L JOIN estado_logro ES ON L.id_estado_logro = ES.id_estado_logro WHERE id_usuario = ? ORDER BY L.id_logro DESC");
        $stmt_logros->bind_param("i", $id_usuario);
        $stmt_logros->execute();
        $result_logros = $stmt_logros->get_result();
        while ($row = $result_logros->fetch_assoc()) {
            $logros[] = $row;
        }
        $stmt_logros->close();

        // Procesar eliminar logro (opcional, pero útil si pueden crear sus propios logros)
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'eliminar_logro') {
            $id_logro = $_POST['id_logro'] ?? 0;

            $stmt = $conexion->prepare("DELETE FROM logro WHERE id_logro = ? AND id_usuario = ?");
            $stmt->bind_param("ii", $id_logro, $id_usuario);

            if ($stmt->execute()) {
                $mensaje = "<p class='mensaje-exito'>Logro eliminado con éxito.</p>";
                // Recargar logros para reflejar el cambio
                $logros = [];
                $stmt_logros = $conexion->prepare("SELECT L.id_logro, L.titulo, L.descripcion, L.recompensa_dinero, ES.estado FROM logro L JOIN estado_logro ES ON L.id_estado_logro = ES.id_estado_logro WHERE id_usuario = ? ORDER BY L.id_logro DESC");
                $stmt_logros->bind_param("i", $id_usuario);
                $stmt_logros->execute();
                $result_logros = $stmt_logros->get_result();
                while ($row = $result_logros->fetch_assoc()) {
                    $logros[] = $row;
                }
                $stmt_logros->close();
            } else {
                $mensaje = "<p class='mensaje-error'>Error al eliminar logro: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }


        $conexion->close();
    ?>

    <div class="main-content-container">
        <h1 class="page-title">Mis Logros</h1>
        <?php echo $mensaje; ?>

        <div class="form-section">
            <h2 class="section-title">Crear Nuevo Logro</h2>
            <form action="logros.php" method="post" class="add-form">
                <input type="hidden" name="accion" value="agregar_logro">
                <div class="form-group">
                    <label for="titulo_logro">Título del Logro:</label>
                    <input type="text" id="titulo_logro" name="titulo" required>
                </div>
                <div class="form-group">
                    <label for="descripcion_logro">Descripción:</label>
                    <textarea id="descripcion_logro" name="descripcion"></textarea>
                </div>
                <div class="form-group">
                    <label for="recompensa_logro">Recompensa (monedas):</label>
                    <input type="number" id="recompensa_logro" name="recompensa" min="0" value="0" required>
                </div>
                <button type="submit" class="submit-button">Crear Logro</button>
            </form>
        </div>

        <div class="list-section">
            <h2 class="section-title">Todos mis Logros</h2>
            <?php if (empty($logros)): ?>
                <p class="no-items-message">Aún no tienes logros registrados. ¡Crea algunos o completa tareas para desbloquearlos!</p>
            <?php else: ?>
                <ul class="item-list">
                    <?php foreach ($logros as $logro): ?>
                        <li class="item-card logro-card">
                            <div class="item-header">
                                <h3><?php echo htmlspecialchars($logro['titulo']); ?></h3>
                                <span class="status-badge status-<?php echo strtolower($logro['estado']); ?>">
                                    <?php echo htmlspecialchars($logro['estado']); ?>
                                </span>
                            </div>
                            <p class="item-description"><?php echo htmlspecialchars($logro['descripcion']); ?></p>
                            <p class="item-reward">Recompensa: <span><?php echo htmlspecialchars($logro['recompensa_dinero']); ?> monedas</span></p>
                            <div class="item-actions">
                                <form action="logros.php" method="post" class="inline-form">
                                    <input type="hidden" name="accion" value="eliminar_logro">
                                    <input type="hidden" name="id_logro" value="<?php echo $logro['id_logro']; ?>">
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