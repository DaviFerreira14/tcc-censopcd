<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
require 'db.php';

// Obter o usuario_id da sessão
$usuario_id = $_SESSION['usuario_id'];

// Mensagem de feedback
$message = '';

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];

    // Verificar se o usuário existe
    $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($userExists);
    $stmt->fetch();
    $stmt->close();

    if ($userExists == 0) {
        $message = "Usuário não encontrado!";
    } else {
        // Verificar se o CEP já está cadastrado para o usuário
        $stmt = $conn->prepare("SELECT COUNT(*) FROM enderecos WHERE cep = ? AND usuario_id = ?");
        $stmt->bind_param("si", $cep, $usuario_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $message = "Esse CEP já está cadastrado por você!";
        } else {
            // Inserir o endereço no banco de dados
            $stmt = $conn->prepare("INSERT INTO enderecos (usuario_id, cep, logradouro, bairro, cidade, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $usuario_id, $cep, $logradouro, $bairro, $cidade, $estado);

            if ($stmt->execute()) {
                $message = "Endereço cadastrado com sucesso!";
            } else {
                $message = "Erro ao cadastrar endereço: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Endereço</title>
    <link rel="stylesheet" href="cadastro_endereco.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Cadastrar Endereço</h1>
    
    <!-- Exibir mensagens -->
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="cep">CEP:</label>
        <input type="text" id="cep" name="cep" required>
        <button type="button" id="buscar">Buscar</button><br><br>

        <label for="logradouro">Rua:</label>
        <input type="text" id="logradouro" name="logradouro" required><br><br>

        <label for="bairro">Bairro:</label>
        <input type="text" id="bairro" name="bairro" required><br><br>

        <label for="cidade">Cidade:</label>
        <input type="text" id="cidade" name="cidade" required><br><br>

        <label for="estado">Estado:</label>
        <input type="text" id="estado" name="estado" required><br><br>

        <button type="submit">Cadastrar Endereço</button>
    </form>

    <script>
        $('#buscar').click(function() {
            var cep = $('#cep').val().replace(/\D/g, '');
            if (cep.length != 8) {
                $('.message').text('CEP inválido!').addClass('error');
                return;
            }
            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                type: 'GET',
                success: function(data) {
                    if (!data.erro) {
                        $('#logradouro').val(data.logradouro);
                        $('#bairro').val(data.bairro);
                        $('#cidade').val(data.localidade);
                        $('#estado').val(data.uf);
                        $('.message').text('Informações do CEP encontradas!').removeClass('error').addClass('success');
                    } else {
                        $('.message').text('CEP não encontrado!').addClass('error');
                    }
                },
                error: function() {
                    $('.message').text('Erro ao buscar informações do CEP.').addClass('error');
                }
            });
        });
    </script>
</body>
</html>