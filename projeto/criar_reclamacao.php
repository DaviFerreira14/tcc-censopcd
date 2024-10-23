<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php'; // Incluindo a conexão

// Verificar se o usuário existe
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT usuario_id FROM usuarios WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Usuário não encontrado."); // Se não encontrar o usuário
}
$stmt->close();

// Lógica para adicionar uma reclamação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitização e validação dos dados de entrada
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $endereco = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

    // Validar se os campos estão vazios
    if (empty($titulo) || empty($endereco) || empty($descricao)) {
        echo "<script>alert('Todos os campos são obrigatórios!'); window.history.back();</script>";
        exit();
    }

    // Captura a data e hora atual
    $data = date("Y-m-d H:i:s");

    // Insere a reclamação no banco de dados
    $stmt = $conn->prepare("INSERT INTO reclamacoes (titulo, data, endereco, descricao, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $titulo, $data, $endereco, $descricao, $usuario_id);

    if ($stmt->execute()) {
        echo "<script>alert('Reclamação criada com sucesso!'); window.location.href='pagina_principal.php';</script>";
    } else {
        echo "<script>alert('Erro ao criar reclamação: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
}

// Fechar a conexão ao final do script
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> <!-- ícone da aba -->
    <link rel="stylesheet" href="criar_reclamacao.css"> <!-- Link para o CSS específico -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Link para ícones -->
    <style>
        .microphone-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            color: green; /* Cor padrão quando não está gravando */
        }
        .recording {
            color: red; /* Cor enquanto grava */
        }
    </style>
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

        <h1>Criar Reclamação</h1>
        
        <div class="content">
            <form method="POST">
                <label for="titulo">
                    <i class="fas fa-pencil-alt"></i> Título da Reclamação:
                    <button type="button" class="microphone-btn" id="btn_gravar_titulo" title="Gravar Título">
                        <i class="fas fa-microphone"></i>
                    </button>
                </label>
                <input type="text" id="titulo" name="titulo" required>

                <label for="endereco">
                    <i class="fas fa-map-marker-alt"></i> Seu Endereço:
                    <button type="button" class="microphone-btn" id="btn_gravar_endereco" title="Gravar Endereço">
                        <i class="fas fa-microphone"></i>
                    </button>
                </label>
                <input type="text" id="endereco" name="endereco" required>

                <label for="descricao">
                    <i class="fas fa-comment-dots"></i> Descrição da Reclamação:
                    <button type="button" class="microphone-btn" id="btn_gravar_descricao" title="Gravar Descrição">
                        <i class="fas fa-microphone"></i>
                    </button>
                </label>
                <textarea id="descricao" name="descricao" rows="4" required></textarea>

                <input type="submit" value="Criar Reclamação">
            </form>
        </div>
    </div>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
            <div class="vw-plugin-top-wrapper"></div>
        </div>
    </div>
    
    <!-- Codigo Vlibras -->
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>

    <script>
        const btnGravarTitulo = document.getElementById('btn_gravar_titulo');
        const btnGravarEndereco = document.getElementById('btn_gravar_endereco');
        const btnGravarDescricao = document.getElementById('btn_gravar_descricao');

        const campoTitulo = document.getElementById('titulo');
        const campoEndereco = document.getElementById('endereco');
        const campoDescricao = document.getElementById('descricao');

        const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
        recognition.lang = 'pt-BR';

        function startRecognition(field, button) {
            recognition.start();
            button.classList.add('recording'); // Muda a cor para vermelho
            button.classList.remove('not-recording'); // Remove a cor verde

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                field.value = transcript; // Preenche o campo correspondente com o texto transcrito
            };

            recognition.onerror = (event) => {
                console.error('Erro na gravação: ' + event.error);
            };

            recognition.onend = () => {
                button.classList.remove('recording'); // Volta a cor para verde
            };
        }

        btnGravarTitulo.onclick = () => startRecognition(campoTitulo, btnGravarTitulo);
        btnGravarEndereco.onclick = () => startRecognition(campoEndereco, btnGravarEndereco);
        btnGravarDescricao.onclick = () => startRecognition(campoDescricao, btnGravarDescricao);
    </script>
</body>
</html>