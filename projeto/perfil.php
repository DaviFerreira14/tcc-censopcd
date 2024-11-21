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

// Buscar informações de endereço do usuário
$stmt = $conn->prepare("SELECT cep, logradouro, bairro, cidade, estado FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$endereco = null;
if ($result->num_rows > 0) {
    $endereco = $result->fetch_assoc();
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon">
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
            
            <?php if ($endereco): ?>
                <h2>Endereço</h2>
                <p><strong>CEP:</strong> <?php echo htmlspecialchars($endereco['cep']); ?></p>
                <p><strong>Rua:</strong> <?php echo htmlspecialchars($endereco['logradouro']); ?></p>
                <p><strong>Bairro:</strong> <?php echo htmlspecialchars($endereco['bairro']); ?></p>
                <p><strong>Cidade:</strong> <?php echo htmlspecialchars($endereco['cidade']); ?></p>
                <p><strong>Estado:</strong> <?php echo htmlspecialchars($endereco['estado']); ?></p>
            <?php else: ?>
                <p><strong>Endereço:</strong> Nenhum endereço cadastrado.</p>
            <?php endif; ?>
        </div>
        <a href="trocar_senha.php" class="btn-trocar-senha">Trocar Senha</a>
        <a href="trocar_endereco.php" class="btn-trocar-endereco">Trocar Endereço</a>
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
    .acess-button {
        width: 60px!important;
        height: 60px!important;
        z-index: 1!important;
        border-radius: 50px!important;
        border: 1px solid black!important; 
    }
    </style>
</body>
</html>