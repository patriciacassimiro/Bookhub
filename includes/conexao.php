<?php
$host    = "localhost";
$usuario = "root";
$senha   = "1234"; 
$banco   = "bookhub"; 

$conexao = mysqli_connect($host, $usuario, $senha, $banco);

if (!$conexao) {
    die("Erro crítico: Não foi possível ligar à base de dados. " . mysqli_connect_error());
}

mysqli_set_charset($conexao, "utf8mb4");
?>
