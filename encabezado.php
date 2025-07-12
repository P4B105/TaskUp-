<?php
    $documentoRoot = $_SERVER['DOCUMENT_ROOT'];
    $rutaAbsolutaLogo = $documentoRoot . '/imagenes/logo.png';
    $relativa = '../imagenes/logo.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <header class="encabezado-header">
            <?php
                session_start();
                if(isset($_SESSION['nombre_usuario'])){
                    echo '
                        <div class="encabezado-div-logo">
                            <a href="dashboard.php"><img src="Imagenes/logo.png" alt="Logo de empresa" class="encabezado-img-logo"></a>
                        </div>
                        <nav>
                            <ul class="encabezado-ul">
                                <li class="encabezado-li"><a class="encabezado-a">'.$_SESSION['nombre_usuario'].'</a></li>
                                <li class="encabezado-li"><a class="encabezado-a" href="logout_d.php">Cerrar Sesion</a></li>   
                            </ul>
                        </nav>
                    ';
                }elseif(!isset($_SESSION['nombre_usuario'])){
                    echo '
                        <div class="encabezado-div-logo">
                            <a href="#"><img src="imagenes/logo.png" alt="Logo de empresa" class="encabezado-img-logo"></a>
                        </div>
                        <nav>
                            <ul class="encabezado-ul">
                                <li class="encabezado-li"><a href="registro.php" class="encabezado-a">Registrarse</a></li>
                                <li class="encabezado-li"><a href="index.php" class="encabezado-a">Iniciar Sesion</a></li>
                            </ul>
                        </nav>
                    ';
                }
            ?>
    </header>


</body>
</html>


