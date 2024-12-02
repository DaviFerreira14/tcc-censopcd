<?php
// Conexão com o banco de dados
$conn = new mysqli('localhost', 'usuario', 'senha', 'banco');

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Manipulação de ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $acao = $_POST['acao'];

    if ($acao == 'responder') {
        // Lógica para responder (exemplo: enviar resposta por email ou salvar no banco)
        echo "Reclamação ID $id respondida.";
    } elseif ($acao == 'arquivar') {
        $conn->query("UPDATE reclamacoes SET status='arquivado' WHERE id=$id");
    } elseif ($acao == 'excluir') {
        $conn->query("DELETE FROM reclamacoes WHERE id=$id");
    }
}

// Busca as reclamações no banco
$result = $conn->query("SELECT * FROM reclamacoes WHERE status != 'arquivado'");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="gerenciar_reclamacao.css">
    <title>Funcionário</title>
</head>
<body>
<header>
    <h1 class="header-title">Painel de Reclamações</h1>
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
        <table border="1" style="width:100%; text-align:left;">
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['descricao'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" name="acao" value="responder">Responder</button>
                            <button type="submit" name="acao" value="arquivar">Arquivar</button>
                            <button type="submit" name="acao" value="excluir">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>
</body>
</html>
