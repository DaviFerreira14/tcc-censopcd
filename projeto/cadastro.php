<?php
session_start(); // Iniciar a sessão

// Incluir o arquivo de conexão com o banco de dados
include 'db.php'; // Incluindo a conexão

$error_message = ""; // Mensagem de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização e validação dos dados
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $cipcd = filter_input(INPUT_POST, 'cipcd', FILTER_SANITIZE_STRING);
    $dataNascimento = filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'];
    $senhaRepetida = $_POST['senha_repetida'];

    // Validar campos
    if (empty($nome) || empty($cpf) || empty($cipcd) || empty($dataNascimento) || empty($email) || empty($senha) || empty($senhaRepetida)) {
        $error_message = "Todos os campos devem ser preenchidos.";
    } elseif (strlen($cpf) !== 11) {
        $error_message = "O CPF deve conter 11 dígitos.";
    } elseif (strlen($cipcd) !== 12) {
        $error_message = "O CIPcD deve conter 12 dígitos.";
    } elseif ($email === false) {
        $error_message = "Email inválido.";
    } else {
        // Verificar se o CPF, CIPcD ou email já estão cadastrados
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE CPF = ? OR CIPcD = ? OR Email = ?");
        $stmt->bind_param("sss", $cpf, $cipcd, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "CPF, CIPcD ou email já cadastrados.";
        } elseif ($senha !== $senhaRepetida) {
            $error_message = "As senhas não conferem.";
        } else {
            // Inserir novo usuário
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (Nome, CPF, CIPcD, DataNascimento, Email, Senha) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $nome, $cpf, $cipcd, $dataNascimento, $email, $senhaHash);

            if (!$stmt->execute()) {
                $error_message = "Erro ao salvar o usuário: " . $stmt->error;
            } else {
                // Redirecionar após cadastro bem-sucedido
                header("Location: index.php");
                exit();
            }
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon">
    <link rel="stylesheet" href="cadastro.css">
    <script>
        function mostrarSenha() {
            var senhaInput = document.getElementById("senha");
            var senhaRepetidaInput = document.getElementById("senha_repetida");
            senhaInput.type = senhaInput.type === "password" ? "text" : "password";
            senhaRepetidaInput.type = senhaRepetidaInput.type === "password" ? "text" : "password";
        }

        function toggleTheme(theme) {
            const body = document.body;
            const cadastroContainer = document.querySelector('.cadastro-container');
            const navbar = document.querySelector('.navbar');
            const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="date"]');

            if (theme === 'dark') {
                body.classList.add('dark-mode');
                cadastroContainer.classList.add('dark-mode');
                navbar.classList.remove('navbar-light', 'bg-light');
                navbar.classList.add('navbar-dark');
                inputs.forEach(input => input.classList.add('dark-mode'));
            } else {
                body.classList.remove('dark-mode');
                cadastroContainer.classList.remove('dark-mode');
                navbar.classList.remove('navbar-dark');
                navbar.classList.add('navbar-light', 'bg-light');
                inputs.forEach(input => input.classList.remove('dark-mode'));
            }

            // Salvar o tema no localStorage
            localStorage.setItem('theme', theme);
        }

        // Carregar o tema salvo ao iniciar a página
        document.addEventListener('DOMContentLoaded', (event) => {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                toggleTheme(savedTheme);
            }
        });
    </script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg fixed-top navbar-light bg-light">
        <a class="navbar-brand" href="#">CensoPCD+</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Tema
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#" onclick="toggleTheme('light')">Claro</a>
                        <a class="dropdown-item" href="#" onclick="toggleTheme('dark')">Escuro</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <div class="outer-container">
    <div class="cadastro-container" style="margin-top: 70px;">
        <div class="logo-wrapper">
            <img src="logos/logofundoinvisivel.png" alt="Logo" class="logo">
            <div class="border-animation">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <?php if ($error_message): ?>
            <div class="erro-mensagem"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" id="nome" name="nome" required placeholder="Nome Completo">
            <input type="text" id="cpf" name="cpf" required pattern="\d{11}" title="O CPF deve conter 11 dígitos." placeholder="CPF">
            <input type="text" id="cipcd" name="cipcd" required pattern="\d{12}" title="O CIPcD deve conter 12 dígitos." placeholder="CIPcD">
            <input type="date" id="data_nascimento" name="data_nascimento" required>
            <input type="email" id="email" name="email" required placeholder="Email">
            <input type="password" id="senha" name="senha" required placeholder="Senha">
            <input type="password" id="senha_repetida" name="senha_repetida" required placeholder="Repita sua Senha">
            <input type="checkbox" onclick="mostrarSenha()"> Mostrar Senha
            <input type="submit" value="Cadastrar" class="btncadastro">
        </form>
        <p>Já tem sua conta? <a href="index.php">Acesse aqui</a> para voltar para a página de login.</p>
    </div>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    </div>
      
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
      new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.11/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>