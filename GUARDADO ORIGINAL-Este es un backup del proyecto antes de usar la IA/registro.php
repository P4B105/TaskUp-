<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
    <title>Registrarse</title>
</head>
<body class="registro-body">
    <?php 
        include 'encabezado.php';
    ?>
    <div class="registro-div-formulario">
        <h1 class="registro-h1">Registrarse</h1>
        <form action="crear_usuario_d.php" method="post">
            <div class="registro-div-nombre">
                <input type="text" name="nombre" class="registro-input-nombre" required>
                <label class="registro-label">Nombre</label>
            </div>
            <div class="registro-div-apellido">
                <input type="text" name="apellido" class="registro-input-apellido" required>
                <label class="registro-label">Apellido</label>
            </div>
            <div class="registro-div-alias">
                <input type="text" name="alias" class="registro-input-alias" required>
                <label class="registro-label">Nombre de usuario</label>
            </div>
            <div class="registro-div-contraseña">
                <input type="password" name="contraseña" class="registro-input-contraseña" required>
                <label class="registro-label">Contraseña</label>
            </div>
            <div class="registro-div-confirmar-contraseña">
                <input type="password" name="confirmar_contraseña" class="registro-input-confirmar-contraseña" required>
                <label class="registro-label">Confirmar Contraseña</label>
            </div>

            <input type="submit" value="Registrarse">
        </form>
    </div>

    <p class="registro-p">¿Ya te registraste?<a href="index.php" class="registro-a"> Inicia Sesion aqui</a></p>
</body>
</html>