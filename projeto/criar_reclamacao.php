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

// Lógica para buscar informações de endereço
$cep = $logradouro = $bairro = $cidade = $estado = '';
$stmt = $conn->prepare("SELECT cep, logradouro, bairro, cidade, estado FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($cep, $logradouro, $bairro, $cidade, $estado);
$stmt->fetch();
$stmt->close();

// Lógica para adicionar uma reclamação
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitização e validação dos dados de entrada
    $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);

    // Validar se os campos estão vazios
    if (empty($titulo) || empty($descricao)) {
        echo "<script>alert('Todos os campos são obrigatórios!'); window.history.back();</script>";
        exit();
    }

    // Captura a data e hora atual
    $data = date("Y-m-d H:i:s");

    // Insere a reclamação no banco de dados
    $stmt = $conn->prepare("INSERT INTO reclamacoes (titulo, data, endereco, descricao, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $enderecoCompleto = "$logradouro, $bairro, $cidade - $estado, CEP: $cep"; // Formatação do endereço
    $stmt->bind_param("ssssi", $titulo, $data, $enderecoCompleto, $descricao, $usuario_id);

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
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon">
    <link rel="stylesheet" href="criar_reclamacao.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .microphone-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            color: green;
        }
        .recording {
            color: red; 
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

      <nav class="sidebar">
        <ul class="sidebarul">
            <li class="sidebarli">
                <a href="pagina_principal.php" class="sidebar-a"><i class="fas fa-home"></i> Incío</a>
            </li>
            <li class="sidebarli">
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

        <h1>Criar Reclamação</h1>
        
        <div class="content">
            <div class="box">
                <form method="POST">
                <label for="titulo">
                    <i class="fas fa-pencil-alt"></i> Título da Reclamação:
                    <button type="button" class="microphone-btn" id="btn_gravar_titulo" title="Gravar Título">
                        <i class="fas fa-microphone"></i>
                    </button>
                </label>
                <input type="text" id="titulo" name="titulo" required>

                <label for="cep">
                    <i class="fas fa-mail-bulk"></i> CEP:
                </label>
                <input type="text" id="cep" name="cep" value="<?php echo htmlspecialchars($cep); ?>" required>

                <label for="logradouro">
                    <i class="fas fa-map-marker-alt"></i> Rua:
                </label>
                <input type="text" id="logradouro" name="logradouro" value="<?php echo htmlspecialchars($logradouro); ?>" required>

                <label for="bairro">
                    <i class="fas fa-home"></i> Bairro:
                </label>
                <input type="text" id="bairro" name="bairro" value="<?php echo htmlspecialchars($bairro); ?>" required>

                <label for="cidade">
                    <i class="fas fa-city"></i> Cidade:
                </label>
                <input type="text" id="cidade" name="cidade" value="<?php echo htmlspecialchars($cidade); ?>" required>

                <label for="estado">
                    <i class="fas fa-map"></i> Estado:
                </label>
                <input type="text" id="estado" name="estado" value="<?php echo htmlspecialchars($estado); ?>" required>

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

    <script>
        const btnGravarTitulo = document.getElementById('btn_gravar_titulo');
        const btnGravarDescricao = document.getElementById('btn_gravar_descricao');

        const campoTitulo = document.getElementById('titulo');
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
        btnGravarDescricao.onclick = () => startRecognition(campoDescricao, btnGravarDescricao);
    </script>
</body>
</html>