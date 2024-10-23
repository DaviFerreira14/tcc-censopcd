<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php'; // Incluindo a conexão

// Buscar informações do usuário logado
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT Nome, CPF, DataNascimento, CIPcD, Email FROM usuarios WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Usuário não encontrado.'); window.location.href='pagina_principal.php';</script>";
    exit();
}

$usuario = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> <!-- ícone da aba -->
    <link rel="stylesheet" href="perfil.css">
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

    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul>
                <li><a href="pagina_principal.php"><i class="fas fa-home"></i> <strong>Início</strong></a></li>
                <li><a href="perfil.php"><i class="fas fa-user"></i> <strong>Perfil</strong></a></li>
                <li><a href="sobre.php"><i class="fas fa-info-circle"></i> <strong>Sobre</strong></a></li>
            </ul>
        </aside>

        <!-- Conteúdo principal -->
        <main>
            <h1>Perfil do Usuário</h1>
            <div class="profile-info">
                <img src="logos/user.jpg" alt="user" class="user">
                <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario['Nome']); ?></p>
                <p><strong>CPF:</strong> <?php echo htmlspecialchars($usuario['CPF']); ?></p>
                <p><strong>Data de Nascimento:</strong> <?php echo htmlspecialchars($usuario['DataNascimento']); ?></p>
                <p><strong>CIPcD:</strong> <?php echo htmlspecialchars($usuario['CIPcD']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['Email']); ?></p>
            </div>
            <a href="trocar_senha.php" class="btn-trocar-senha">Trocar Senha</a>
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