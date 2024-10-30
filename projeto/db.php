<?php
// db.php

$host = 'db'; // Nome do serviço do MySQL
$db = 'censopcd'; // Nome do banco de dados
$user = 'user'; // Usuário do MySQL
$pass = 'user'; // Senha do MySQL

// Estabelecendo a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Verificando a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Definindo o charset da conexão
$conn->set_charset("utf8");

?>