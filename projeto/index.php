<?php
session_start(); // Iniciar a sessão

// Incluir a conexão com o banco de dados
include 'db.php';

// Definir tema padrão como claro
$tema = "light"; // Tema padrão

if (isset($_SESSION['tema'])) {
    $tema = $_SESSION['tema']; // Recuperar tema da sessão se estiver definido
}

// Processar a alteração de tema
if (isset($_POST['salvar_tema'])) {
    $tema = $_POST['tema'];
    setcookie('tema', $tema, time() + (86400 * 30), "/"); // 86400 = 1 dia
}

$erro_mensagem = ""; // Variável para armazenar mensagem de erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

    // Verificar se os campos estão preenchidos
    if (empty($cpf) || empty($senha)) {
        $erro_mensagem = "Por favor, preencha todos os campos.";
    } else {
        // Verificar se é o CPF e senha específicos para redirecionar para 'funcionario.php'
        if ($cpf === '12345678900' && $senha === 'adm') {
            $_SESSION['usuario_id'] = $cpf; // Armazenar um identificador fictício para o usuário

            // Adicionar uma mensagem de depuração
            error_log("Redirecionando para funcionario.php com CPF: $cpf");

            header("Location: funcionario.php"); // Redirecionar para a página de funcionários
            exit();
        }

        // Consulta para obter o ID do usuário
        $stmt = $conn->prepare("SELECT usuario_id, Senha FROM usuarios WHERE CPF = ?");
        $stmt->bind_param("s", $cpf);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($usuario_id, $senhaHash);
            $stmt->fetch();

            // Verificar a senha
            if (password_verify($senha, $senhaHash)) {
                $_SESSION['usuario_id'] = $usuario_id; // Armazenar ID do usuário na sessão
                header("Location: pagina_principal.php"); // Redirecionar para a página principal
                exit();
            } else {
                $erro_mensagem = "Credenciais inválidas."; // Mensagem de erro
            }
        } else {
            $erro_mensagem = "Credenciais inválidas."; // Mensagem de erro
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> 
</head>
<body class="<?php echo $tema; ?>">

<nav class="navbar navbar-expand-lg fixed-top">
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
                    <a class="dropdown-item" href="#" id="light-theme">Claro</a>
                    <a class="dropdown-item" href="#" id="dark-theme">Escuro</a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<div class="login-container" style="margin-top: 80px;">
    <div class="logo-wrapper">
        <img src="logos/logofundoinvisivel.png" alt="Logo" class="logo">
        <div class="border-animation">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <form method="post" action="">
        <input type="text" id="cpf" name="cpf" placeholder="Digite seu CPF" required>
        <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
        <input type="checkbox" onclick="mostrarSenha()"> Mostrar Senha
        <input type="submit" value="Entrar">
    </form>
    <p class="cadastro-link">Não tem uma conta? <a href="cadastro.php">Cadastre-se aqui</a>.</p>
    <?php if (!empty($erro_mensagem)): ?>
        <p class="erro-mensagem"><?php echo $erro_mensagem; ?></p>
    <?php endif; ?>
</div>

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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Função para aplicar o tema
    function applyTheme(theme) {
        document.body.classList.remove('light', 'dark');
        document.body.classList.add(theme);

        const loginContainer = document.querySelector('.login-container');
        const navbar = document.querySelector('.navbar');
        const dropdownToggle = document.querySelector('.navbar .dropdown-toggle');

        if (theme === 'dark') {
            loginContainer.classList.add('dark');
            navbar.classList.add('navbar-dark', 'bg-dark');
            dropdownToggle.classList.add('text-light');
        } else {
            loginContainer.classList.remove('dark');
            navbar.classList.remove('navbar-dark', 'bg-dark');
            navbar.classList.add('navbar-light', 'bg-light');
            dropdownToggle.classList.remove('text-light');
        }

        const inputs = document.querySelectorAll('input[type="text"], input[type="password"], input[type="submit"]');
        inputs.forEach(input => {
            if (theme === 'dark') {
                input.classList.add('dark');
            } else {
                input.classList.remove('dark');
            }
        });

        localStorage.setItem('theme', theme);

        // Salvar o tema na sessão
        fetch('salvar_tema.php', { 
            method: 'POST', 
            body: JSON.stringify({ tema: theme }),
            headers: {
                'Content-Type': 'application/json'
            }
        });
    }

    // Função para carregar o tema armazenado
    function loadTheme() {
        const theme = localStorage.getItem('theme') || '<?php echo $tema; ?>';
        applyTheme(theme);
    }

    // Inicializa o tema ao carregar a página
    loadTheme();

    // Eventos para mudança de tema
    document.getElementById('light-theme').addEventListener('click', function() {
        applyTheme('light');
    });

    document.getElementById('dark-theme').addEventListener('click', function() {
        applyTheme('dark');
    });

    // Função para mostrar/esconder a senha
    function mostrarSenha() {
        const senhaInput = document.getElementById("senha");
        if (senhaInput.type === "password") {
            senhaInput.type = "text";
        } else {
            senhaInput.type = "password";
        }
    }
</script>
</body>
</html>