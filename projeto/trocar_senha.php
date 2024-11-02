<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php'; // Incluindo a conexão

$error_message = "";
$success_message = "";

// Lógica de troca de senha
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $senhaAtual = $_POST['senha_atual'];
    $novaSenha = $_POST['nova_senha'];
    $confirmarNovaSenha = $_POST['confirmar_nova_senha'];

    // Verificar se a senha atual está correta
    $stmt = $conn->prepare("SELECT Senha FROM usuarios WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario && password_verify($senhaAtual, $usuario['Senha'])) {
        if ($novaSenha === $confirmarNovaSenha) {
            // Atualizar senha
            $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT); // Hash da nova senha
            $stmt = $conn->prepare("UPDATE usuarios SET Senha = ? WHERE usuario_id = ?");
            $stmt->bind_param("si", $senhaHash, $usuario_id);
            if ($stmt->execute()) {
                $success_message = "Senha trocada com sucesso!";
            } else {
                $error_message = "Erro ao trocar a senha.";
            }
        } else {
            $error_message = "As novas senhas não conferem.";
        }
    } else {
        $error_message = "Senha atual incorreta.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> <!-- ícone da aba -->
    <link rel="stylesheet" href="trocar_senha.css"> <!-- Link para o CSS específico -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <img src="logos/logoblue.jpg" alt="Logo" class="logo">
        <div class="header-title">CensoPCD+</div>
        <nav class="header-menu">
            <ul>
                <li><a href="pagina_principal.php"><i class="fas fa-arrow-left"></i></a></li>
            </ul>
        </nav>
    </header>

    <!-- Menu Lateral/Sidebar -->
    <nav class="sidebar">
        <ul class="sidebarul">
            <li class="sidebarli">
                <a href="pagina_principal.php" class="sidebar-a"><i class="fas fa-home"></i> Incío</a>
            </li>
            <li id="fundo" class="sidebarli">
                <a href="perfil.php" class="sidebar-a"><i class="fas fa-user"></i> Perfil </a>
            </li>
            <li class="sidebarli">
                <a href="cadastro_endereco.php"class="sidebar-a"><i class="fas fa-map-marker-alt"></i> Cadastro de endereço </a>
            </li>
            <li class="sidebarli">
                <a href="sobre.php" class="sidebar-a"><i class="fas fa-info-circle"></i> Sobre </a>
            </li>
        </ul>
    </nav>
        
        <h1>Trocar Senha</h1>
        <main>  
            <?php if ($error_message): ?>
                <div style="color: red;"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div style="color: green;"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <label for="senha_atual">Senha Atual:</label>
                <input type="password" id="senha_atual" name="senha_atual" required placeholder="Digite sua senha atual...">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" required placeholder="Digite sua nova senha...">
                <label for="confirmar_nova_senha">Confirmar Nova Senha:</label>
                <input type="password" id="confirmar_nova_senha" name="confirmar_nova_senha" required placeholder="Digite sua nova senha...">
                <input type="submit" value="Trocar Senha">
            </form>
        </main>
    </div>

    
    <!-- Codigo Vlibras -->
    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
        </div>
      </div>
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
      new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
    <style>
    .acess-button{
    width: 60px!important;
    height: 60px!important;
    z-index: 1!important;
    border-radius: 50px!important;
    border: 1px solid black!important; 
    }
    </style>
</body>
</html>