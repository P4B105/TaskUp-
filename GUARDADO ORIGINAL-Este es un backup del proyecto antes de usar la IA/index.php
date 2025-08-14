<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>iniciar sesion</title>
</head>
<body class="index-body">
    <?php 
        include 'encabezado.php';
    ?>

    <div class="index-div-formulario">
        <h1 class="index-h1">Inicio de sesion</h1>
        <form action="login_d.php" method="post" class="index-form-formulario">
            <div class="index-div-usuario">
                <input type="text" name="alias" class="index-input-usuario" required>
                <label class="index-label">Usuario</label>
            </div>

            <div class="index-div-contraseña">
                <input type="password" name="contraseña" class="index-input-contraseña" required>
                <label class="index-label">Contraseña</label>
            </div>
            
            <input type="submit" value="Iniciar" class="index-input-submit">
        </form>
    </div>

    <p class="index-p">¿No se ha registrado?<a href="registro.php" class="index-a"> Registrese aqui</a></p>
</body>
</html>