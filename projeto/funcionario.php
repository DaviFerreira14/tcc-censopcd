<?php
session_start();

// Verifica se o usuário está logado e tem permissões de funcionário
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['funcionario'])) {
    header("Location: index.php");
    exit();
}

// Lógica de logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php';

// Inicializa a variável de pesquisa
$pesquisa = '';

// Verifica se o formulário foi enviado
if (isset($_GET['pesquisa'])) {
    // Sanitiza a entrada
    $pesquisa = filter_input(INPUT_GET, 'pesquisa', FILTER_SANITIZE_STRING);
}

// Prepara a consulta SQL com base no termo de pesquisa
$query = "SELECT r.*, u.Nome AS usuario_nome FROM reclamacoes r JOIN usuarios u ON r.usuario_id = u.usuario_id WHERE r.titulo LIKE ? ORDER BY r.data DESC";
$stmt = $conn->prepare($query);

// Adiciona os curingas para buscar qualquer parte do título
$pesquisa_param = "%" . $pesquisa . "%";
$stmt->bind_param("s", $pesquisa_param);
$stmt->execute();
$result = $stmt->get_result();

// Fechar o prepared statement
$stmt->close();

// Fechar a conexão ao final do script
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CensoPCD+ - Reclamações</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="principal.css">
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon">
    <script>
        function confirmarExclusao() {
            return confirm("Tem certeza que deseja excluir esta reclamação?");
        }
    </script>
</head>
<body>
    <header>
        <img src="logos/logoblue.jpg" alt="Logo" class="logo">
        <div class="header-title">CensoPCD+</div>
        <nav class="header-menu">
            <ul>
                <li><a href="?action=logout"><i class="fas fa-sign-out-alt"></i></a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="principal-container">
            <h1 class="principal-title">Central de Reclamações - Funcionário</h1>
            <div class="pesquisa-container">
                <form action="pagina_funcionario.php" method="get">
                    <input type="text" placeholder="Pesquisar por título..." name="pesquisa" value="<?php echo htmlspecialchars($pesquisa); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <h2>Reclamações dos Usuários</h2>
            <?php if ($result->num_rows > 0): ?>
                <ul class="reclamacoes-lista">
                    <?php while ($reclamacao = $result->fetch_assoc()): ?>
                        <li class="reclamacao-item">
                            <div class="titulo-reclamacao">
                                <strong>Título: <?php echo htmlspecialchars($reclamacao['titulo']); ?></strong>
                                <span class="horario-reclamacao">
                                    <?php 
                                        // Define o fuso horário de Brasília
                                        date_default_timezone_set('America/Sao_Paulo');
                                        $data = new DateTime($reclamacao['data']);
                                        echo $data->format('d/m/Y á\s H:i'); // Formata a data e hora para DD/MM/AAAA às HH:MM
                                    ?>
                                </span>
                            </div>
                            <div class="usuario-reclamacao">
                                Usuário: <?php echo htmlspecialchars($reclamacao['usuario_nome']); ?>
                            </div>
                            <div class="endereco-reclamacao">
                                Endereço: <?php echo htmlspecialchars($reclamacao['endereco']); ?>
                            </div>
                            <br>
                            Descrição: <span class="reclamacao-descricao"><?php echo nl2br(htmlspecialchars($reclamacao['descricao'])); ?></span><br>
                            <hr>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Nenhuma reclamação encontrada com esse título.</p>
            <?php endif; ?>
        </div>
    </main>

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
</body>
</html>