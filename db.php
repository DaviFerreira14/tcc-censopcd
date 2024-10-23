<?php
// db.php

$host = 'localhost'; // Endereço do servidor MySQL
$db = 'censopcd'; // Nome do banco de dados
$user = 'root'; // Usuário do MySQL
$pass = ''; // Senha do MySQL (se houver)

// Estabelecendo a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Definindo o charset da conexão
$conn->set_charset("utf8");

?>