<?php
//ESTE ARCHIVO PARTE VA A IMPLEMENTARSE PARA EL REGISTRO DE USUARIO

//Conectamos con la base de datos (Lo que hacemos es llamar al archivo que se llama "db.php" para que se ejecute en este archivo que tiene el codigo para conectarse con la base de datos "crear_usuario.php" para que se pueda conectar con la base de datos)
require 'database.php';

//Guardamos los datos ingresados por el usuario en "registro.html"
$nombre = $_POST['nombre'] ?? "";//Estos signos de interrogacion lo que hacen es validar que estas variables reciban algun tipo de dato del html, ya que si el usuario por algun motivo no ingresa algun valor va a ocasionar un error, lo que hace el operador (??) ternario es decir: si esta variable ($nombre) es NULL (No esta contiene ningun valor esto tambien incluye el valor del espacio " ") entonces agregale un valor de espacio " " (Espacio en blanco) 
$apellido = $_POST['apellido'] ?? "";
$alias = $_POST['alias'] ?? "";
$contraseña = $_POST['contraseña'] ?? "";
$confirmar_contraseña = $_POST['confirmar_contraseña'] ?? "";

//Validamos la confirmacion de la contraseña
if($contraseña != $confirmar_contraseña){
    header("location: registro.php?error=contDif");
    $conexion->close();
}

//--verificar que alias no exista
if($alias==" " or str_contains($alias, "*")){
    header("Location: registro.php?error=invAlias");
}
$verif = $conexion->query("SELECT alias FROM `usuario` where usuario.alias like '$alias'");
$result = $verif->fetch_assoc()['alias'] ?? ""; //Con el fetch_assoc() obtenemos el resultado de la consulta y con el ['alias'] obtenemos el valor del alias que se encuentra en la base de datos, si no existe nos devuelve un valor nulo (NULL) gracias al operador de coalescencia nula (??) que le pusimos arriba a la variable $result
if($result==$alias){
    header("Location: registro.php?error=usrExiste");
    $conexion->close();
}


//Convertimos la contraseña del usuario en una contraseña hash ejemplo: Pepe = (#/$&!)#=(!"ROJQIOWJHE()#"Y)!12039821083e2jioqw esto es gracias a la funcion password_hash()
$contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);

$stmt = $conexion->prepare("INSERT INTO usuario (nombre, apellido, alias, contraseña, fecha_registro) VALUES (?, ?, ?, ?, CURDATE())"); //Los signos interrogatorios nos indican que los valores (VALUES) a ingresar no se estan indicando aca si no que se indicaran mas adelante y ahora el CURDATE() lo que hace es agregar la fecha de hoy en la base de datos
$stmt->bind_param("ssss", $nombre, $apellido, $alias, $contraseña_hash);//Aca con la funcion bind_param() estamos insertando los valores ahora si los que el usuario ingreso en "registro.html"

if ($stmt->execute()) {
    /*echo "Usuario creado correctamente."; */
    header("Location: index.php?error=usrCreado");
} else {
    echo "Error al insertar: " . $stmt->error;
}
?>
