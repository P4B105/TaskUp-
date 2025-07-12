<?php
session_start();
require 'database.php';

$nombreUsuario = $_POST['alias'];
$contraseña = $_POST['contraseña'];

$_SESSION['nombre_usuario'] = $nombreUsuario;

$stmt = $conexion->prepare("SELECT id_usuario, contraseña FROM usuario WHERE alias = ?");//Esta es la consullta SQL que se envia a la base de datos
$stmt->bind_param("s", $nombreUsuario);//El metodo bind_param() sirve para enlazar parametros y la "s" especifica que tipo de parametros vamos a enlazar en este caso indica que es una "string" esta indicando a mysql que el valor que vamos a enlazar sera una "Cadena de texto" y sera la variable $nombreUsuario POR EJEMPLO: Si el nombre de usuario es "Juanito" entonces el signo de interrogacion (?) seria igual a "Juanito"
$stmt->execute();//El metodo execute() se encarga de ejecutar la consulta SQL que estamos especificando en las lineas de arriba, este metodo devuelve un balor booleano TRUE si la ejecucion fue exitosa y FALSE si no lo fue
$stmt->store_result();//El metodo store_result() se encarga de almacenar los resultados recibidos por la consulta MSQL y los almacena en la memoria del servidor PHP

if ($stmt->num_rows === 1){
    $stmt->bind_result($id, $contraseñaHasheada);
    $stmt->fetch();
    $contraseñaHasheada = trim($contraseñaHasheada);//Le quitamos los espacios vacios con funcion trim()
    $contraseña = trim($contraseña);//Le quitamos los espacios vacios con funcion trim()
    
    if(password_verify($contraseña, $contraseñaHasheada)){//La funcion password_verify() solo puede comparar una contraseña del login y la otra en la base de datos (Hasheada) si son iguales suelta TRUE si no FALSE
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $nombreUsuario;
        header("Location: home.php");
        exit;
    }else{
        header('Location: index.php?error=contInv');
        /*
        echo "Contraseña incorrecta.";
        echo $nombreUsuario . $contraseña;
        echo "Contraseña de base de datos:" . $contraseñaHasheada;
        */
    }
}else{
    /* echo "Usuario no encontrado."; */
    header('Location: index.php?error=noUser');
}
?>
