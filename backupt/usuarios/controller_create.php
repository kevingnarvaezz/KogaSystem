<?php

include ('../app/config/config.php');

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$ci = $_POST['ci'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$celular = $_POST['celular'];
$email = $_POST['email'];
$password = $_POST['password'];
$cargo = $_POST['cargo'];
$genero = $_POST['genero'];

$user_creacion = 'ppflorenciano@gmail.com';
date_default_timezone_set("America/Argentina/Buenos_Aires");
$fecha_creacion = date("Y-m-d H:i:s");
$estado = "1";

$nombreperfil = "KogaSystem-".date("Y-m-d-h-i-s");
$filename = $nombreperfil."_".$_FILES['file']['name'];
$location = "update_usuarios/".$filename;
move_uploaded_file($_FILES['file']['tmp_name'], $location);



$sentencia = $pdo->prepare("INSERT INTO usuarios
    (nombre, apellido, ci, fecha_nacimiento, celular, email, password, cargo, perfil, genero, user_creacion, fecha_creacion, estado)
    VALUES (:nombre,:apellido,:ci,:fecha_nacimiento,:celular,:email,:password,:cargo,:perfil, :genero, :user_creacion,:fecha_creacion,:estado)");

$sentencia->bindParam(':nombre', $nombre);
$sentencia->bindParam(':apellido', $apellido);
$sentencia->bindParam(':ci', $ci);
$sentencia->bindParam(':fecha_nacimiento', $fecha_nacimiento);
$sentencia->bindParam(':celular', $celular);
$sentencia->bindParam(':email', $email);
$sentencia->bindParam(':password', $password);
$sentencia->bindParam(':cargo', $cargo);
$sentencia->bindParam(':perfil', $filename);
$sentencia->bindParam(':genero', $genero);
$sentencia->bindParam(':user_creacion', $user_creacion);
$sentencia->bindParam(':fecha_creacion',$fecha_creacion);
$sentencia->bindParam(':estado', $estado);

if($sentencia->execute()){
    echo "El usuario se registro correctamente";
    header('location: '.$URL.'/usuarios/');
}else{
    echo "No se pudo registrar el usuario";
}