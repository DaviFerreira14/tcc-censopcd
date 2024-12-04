<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php';

// Variável para manipulação de ações
$reclamacaoParaResponder = null;

if ($acao == 'responder' && isset($_POST['resposta'])) {
    $resposta = $_POST['resposta'];
    $stmt = $conn->prepare("UPDATE reclamacoes SET resposta = ?, status = 3 WHERE id = ?");
    $stmt->bind_param("si", $resposta, $id);
    $stmt->execute();
}


// Processar ações do formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $acao = isset($_POST['acao']) ? $_POST['acao'] : null;

    // Valida o ID antes de executar qualquer ação
    if (empty($id) || !is_numeric($id)) {
        die("ID inválido.");
    }

    if ($acao == 'responder') {
        // Carregar a reclamação para responder
        $stmt = $conn->prepare("SELECT * FROM reclamacoes WHERE id = ?");
        if (!$stmt) {
            die("Erro no prepare: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reclamacaoParaResponder = $result->fetch_assoc();
        } else {
            echo "<script>alert('Reclamação não encontrada.');</script>";
        }
        $stmt->close();
    } elseif ($acao == 'salvar_resposta') {
        // Salvar a resposta no banco de dados
        $resposta = trim($_POST['resposta']);
        if (!empty($resposta)) {
            $stmt = $conn->prepare("UPDATE reclamacoes SET resposta = ?, status = 'respondida' WHERE id = ?");
            if (!$stmt) {
                die("Erro no prepare: " . $conn->error);
            }
            $stmt->bind_param("si", $resposta, $id);
            if ($stmt->execute()) {
                echo "<script>alert('Resposta enviada com sucesso!');</script>";
            } else {
                echo "<script>alert('Erro ao enviar a resposta: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('A resposta não pode estar vazia.');</script>";
        }
    } elseif ($acao == 'arquivar') {
        $stmt = $conn->prepare("UPDATE reclamacoes SET status = 'arquivado' WHERE id = ?");
        if (!$stmt) {
            die("Erro no prepare: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($acao == 'excluir') {
        $stmt = $conn->prepare("DELETE FROM reclamacoes WHERE id = ?");
        if (!$stmt) {
            die("Erro no prepare: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Busca as reclamações no banco
$result = $conn->query("SELECT * FROM reclamacoes WHERE status != 'arquivado'");
if (!$result) {
    die("Erro na consulta SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="gerenciar_reclamacao.css">
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>CensoPCD+</title>
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
        <aside class="sidebar">
            <ul>
                <li><a href="criar_reclamacao.php">Criar Reclamação</a></li>
                <li><a href="#">Painel de Controle</a></li>
            </ul>
        </aside>
        <div class="content">
            <h2>Reclamações</h2>

            <?php if ($reclamacaoParaResponder): ?>
                <h3>Responder Reclamação</h3>
                <p><strong>ID:</strong> <?= htmlspecialchars($reclamacaoParaResponder['id']) ?></p>
                <p><strong>Descrição:</strong> <?= htmlspecialchars($reclamacaoParaResponder['descricao']) ?></p>
                <form method="post">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($reclamacaoParaResponder['id']) ?>">
                    <textarea name="resposta" rows="5" style="width: 100%;" placeholder="Escreva sua resposta aqui"></textarea>
                    <button type="submit" name="acao" value="salvar_resposta">Enviar Resposta</button>
                </form>
                <hr>
            <?php endif; ?>

            <table border="1" style="width:100%; text-align:left;">
                <tr>
                    <th>ID</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['descricao']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <button type="submit" name="acao" value="responder">Responder</button>
                                    <button type="submit" name="acao" value="arquivar">Arquivar</button>
                                    <button type="submit" name="acao" value="excluir">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Nenhuma reclamação encontrada.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
