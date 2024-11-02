<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
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
include 'db.php'; // Incluindo a conexão

// Inicializa a variável de pesquisa
$pesquisa = '';

// Verifica se o formulário foi enviado
if (isset($_GET['pesquisa'])) {
    // Sanitiza a entrada
    $pesquisa = filter_input(INPUT_GET, 'pesquisa', FILTER_SANITIZE_STRING);
}

// Prepara a consulta SQL com base no termo de pesquisa
$query = "SELECT * FROM reclamacoes WHERE usuario_id = ? AND titulo LIKE ? ORDER BY data DESC";
$stmt = $conn->prepare($query);

// Adiciona os curingas para buscar qualquer parte do título
$pesquisa_param = "%" . $pesquisa . "%";
$stmt->bind_param("is", $_SESSION['usuario_id'], $pesquisa_param);
$stmt->execute();
$result = $stmt->get_result();

// Lógica para excluir reclamação
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id']; // Cast para garantir que é um inteiro
    $delete_query = "DELETE FROM reclamacoes WHERE id = ? AND usuario_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("ii", $delete_id, $_SESSION['usuario_id']);
    if ($delete_stmt->execute()) {
        // Redireciona após a exclusão
        header("Location: pagina_principal.php?msg=Reclamação excluída com sucesso."); 
        exit();
    } else {
        // Mensagem de erro em caso de falha na exclusão
        header("Location: pagina_principal.php?msg=Erro ao excluir reclamação."); 
        exit();
    }
}

// Fechar os prepared statements
$stmt->close();
if (isset($delete_stmt)) {
    $delete_stmt->close();
}

// Fechar a conexão ao final do script
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>CensoPCD+</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> 
    <link rel="stylesheet" href="principal.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    <!-- Menu Lateral/Sidebar -->
    <nav class="sidebar">
        <ul class="sidebarul">
            <li id="fundo" class="sidebarli">
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
    
    <main>
    <div class="principal-container">
        <h1 class="principal-title">Central de Reclamações do Censopcd!</h1>
        <div class="pesquisa-container">
            <form action="pagina_principal.php" method="get">
                <input type="text" placeholder="Pesquisar..." name="pesquisa" value="<?php echo htmlspecialchars($pesquisa); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </div>

            <h2>Suas Reclamações</h2>
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
                            <div class="endereco-reclamacao">
                            Endereço: <?php echo htmlspecialchars($reclamacao['endereco']); ?>
                            </div>
                            <br>
                            Descrição: <span class="reclamacao-descricao"><?php echo nl2br(htmlspecialchars($reclamacao['descricao'])); ?></span><br>
                            <a href="?delete_id=<?php echo $reclamacao['id']; ?>" onclick="return confirmarExclusao();" class="btn-excluir" aria-label="Excluir reclamação">
                                <i class="fas fa-trash-alt"></i> <!-- Apenas o ícone -->
                            </a>
                            <hr>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>Nenhuma reclamação encontrada com esse título.</p>
            <?php endif; ?>
        </main>
    </div>

    <a href="criar_reclamacao.php" class="btn-flutuante">+</a>

    
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