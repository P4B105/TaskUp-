<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Tienda de Recompensas</title>
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
        $balance_actual = 0;

        // Obtener balance actual del usuario
        $stmt_balance = $conexion->prepare("SELECT total_moneda FROM balance WHERE id_usuario = ?");
        $stmt_balance->bind_param("i", $id_usuario);
        $stmt_balance->execute();
        $stmt_balance->bind_result($balance_actual);
        $stmt_balance->fetch();
        $stmt_balance->close();

        // Procesar compra de ítem
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'comprar_item') {
            $id_item = $_POST['id_item'] ?? 0;
            // Cambiado de 'costo_item' a 'valor_item' para coincidir con la base de datos
            $valor_item = $_POST['valor_item'] ?? 0;

            if ($balance_actual >= $valor_item) {
                // Iniciar transacción
                $conexion->begin_transaction();
                try {
                    // 1. Restar el valor del balance del usuario
                    $stmt_restar = $conexion->prepare("UPDATE balance SET total_moneda = total_moneda - ? WHERE id_usuario = ?");
                    // Usamos $valor_item en lugar de $costo_item
                    $stmt_restar->bind_param("ii", $valor_item, $id_usuario);
                    $stmt_restar->execute();
                    $stmt_restar->close();

                    // 2. Añadir el ítem al inventario del usuario (Necesitarás una tabla 'inventario' en tu BD)
                    // Por ejemplo: CREATE TABLE inventario (id_inventario INT AUTO_INCREMENT PRIMARY KEY, id_usuario INT, id_item INT, cantidad INT DEFAULT 1, fecha_adquisicion DATE);
                    $stmt_inventario = $conexion->prepare("INSERT INTO inventario (id_usuario, id_item, cantidad, fecha_adquisicion) VALUES (?, ?, 1, CURDATE()) ON DUPLICATE KEY UPDATE cantidad = cantidad + 1");
                    $stmt_inventario->bind_param("ii", $id_usuario, $id_item);
                    $stmt_inventario->execute();
                    $stmt_inventario->close();

                    $conexion->commit();
                    // Actualizar balance mostrado
                    $balance_actual -= $valor_item; 
                    $mensaje = "<p class='mensaje-exito'>¡Compra realizada con éxito! Se han restado $valor_item monedas de tu balance.</p>";

                } catch (mysqli_sql_exception $exception) {
                    $conexion->rollback();
                    $mensaje = "<p class='mensaje-error'>Error al procesar la compra: " . $exception->getMessage() . "</p>";
                }
            } else {
                $mensaje = "<p class='mensaje-error'>No tienes suficientes monedas para comprar este ítem.</p>";
            }
        }

        // Obtener todos los ítems disponibles en la tienda
        $items = [];
        // Cambiado I.costo a I.valor en la consulta SQL
        $stmt_items = $conexion->prepare("SELECT I.id_item, I.nombre, I.descripcion, I.valor, TI.tipo_nombre FROM item I JOIN tipo_item TI ON I.id_tipo_item = TI.id_tipo_item ORDER BY TI.tipo_nombre, I.valor ASC");
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        while ($row = $result_items->fetch_assoc()) {
            $items[] = $row;
        }
        $stmt_items->close();
        $conexion->close();
    ?>

    <div class="main-content-container">
        <h1 class="page-title">Tienda de Recompensas</h1>
        <?php echo $mensaje; ?>

        <div class="shop-balance">
            <h2 class="section-title">Tu Balance de Monedas: <span class="balance-value"><?php echo htmlspecialchars($balance_actual); ?></span></h2>
        </div>

        <div class="list-section">
            <?php if (empty($items)): ?>
                <p class="no-items-message">No hay ítems disponibles en la tienda en este momento.</p>
            <?php else: ?>
                <ul class="item-grid">
                    <?php foreach ($items as $item): ?>
                        <li class="item-card shop-item-card">
                            <div class="item-header">
                                <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                                <span class="item-type-badge"><?php echo htmlspecialchars($item['tipo_nombre']); ?></span>
                            </div>
                            <p class="item-description"><?php echo htmlspecialchars($item['descripcion']); ?></p>
                            <p class="item-cost">Costo: <span><?php echo htmlspecialchars($item['valor']); ?> monedas</span></p>
                            <form action="shop.php" method="post">
                                <input type="hidden" name="accion" value="comprar_item">
                                <input type="hidden" name="id_item" value="<?php echo $item['id_item']; ?>">
                                <input type="hidden" name="valor_item" value="<?php echo $item['valor']; ?>">
                                <button type="submit" class="action-button buy-button" <?php echo ($balance_actual < $item['valor']) ? 'disabled' : ''; ?>>
                                    Comprar
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>