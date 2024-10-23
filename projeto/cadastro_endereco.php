<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php'; // Incluindo a conexão

// Inicializa as variáveis
$cep = $logradouro = $bairro = $cidade = $estado = '';

// Lógica para inserir o endereço
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cep = filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING);
    $logradouro = filter_input(INPUT_POST, 'logradouro', FILTER_SANITIZE_STRING);
    $bairro = filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING);
    $cidade = filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);
    
    $usuario_id = $_SESSION['usuario_id'];

    // Preparar a consulta para inserir o endereço
    $query = "INSERT INTO enderecos (usuario_id, cep, logradouro, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $usuario_id, $cep, $logradouro, $bairro, $cidade, $estado);

    if ($stmt->execute()) {
        // Redireciona após o cadastro
        header("Location: pagina_principal.php?msg=Endereço cadastrado com sucesso.");
        exit();
    } else {
        // Mensagem de erro em caso de falha na inserção
        echo "Erro ao cadastrar endereço: " . $stmt->error;
    }

    // Fechar o prepared statement
    $stmt->close();
}

// Fechar a conexão ao final do script
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Endereço</title>
    <link rel="stylesheet" href="cadastro_endereco.css"> <!-- Link para o CSS específico -->
    <script>
        function buscarEndereco() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('logradouro').value = data.logradouro;
                            document.getElementById('bairro').value = data.bairro;
                            document.getElementById('cidade').value = data.localidade;
                            document.getElementById('estado').value = data.uf;
                        } else {
                            alert('CEP não encontrado.');
                        }
                    })
                    .catch(error => alert('Erro ao buscar o CEP.'));
            } else {
                alert('CEP inválido. Deve conter 8 dígitos.');
            }
        }
    </script>
</head>
<body>
    <h1>Cadastro de Endereço</h1>
    <form method="POST" action="">
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" required>
        <button type="button" onclick="buscarEndereco()">Buscar</button>

        <label for="logradouro">Logradouro:</label>
        <input type="text" id="logradouro" name="logradouro" required>

        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" required>

        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" required>

        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" required>

        <button type="submit">Cadastrar Endereço</button>
    </form>
</body>
</html>