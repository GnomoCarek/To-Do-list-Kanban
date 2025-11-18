<?php
$servidor = "localhost";
$usuario = "root";
$password = ""; // senha padrão
$BD = "kanban";

$conn = mysqli_connect($servidor, $usuario, $password, $BD);

if (!$conn){
    die("Falha ao se conecrtar com o banco !" . mysqli_connect_error());
}

?>