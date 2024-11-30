<?php
$senha = 'minhasenha123'; // Senha em texto plano
$senhaHash = password_hash($senha, PASSWORD_BCRYPT);
echo $senhaHash; // Exibe a senha criptografada
?>
