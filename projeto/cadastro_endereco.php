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
    <title>CensoPCD+</title>
    <link rel="stylesheet" href="cadastro_endereco.css">
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>
    

    <!-- Cabeçalho/Header -->
        <header>
        <img src="logos/logoblue.jpg" alt="Logo" class="logo">
        <div class="header-title">CensoPCD+</div>
        <nav class="header-menu">
            <ul class="header-ul">
            <li><a href="pagina_principal.php"><i class="fas fa-arrow-left"></i></a></li>
            </ul>
        </nav>
        </header>
        
    
    <h1>Cadastrar Endereço</h1>

    <!-- Exibir mensagens -->
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="cep">
            <i class="fas fa-mail-bulk"></i> CEP:
        </label>
        <input type="text" id="cep" name="cep" required>
        <button type="button" id="buscar">Buscar</button><br><br>

        <label for="logradouro">
            <i class="fas fa-map-marker-alt"></i> Rua:
        </label>
        <input type="text" id="logradouro" name="logradouro" required><br><br>

        <label for="bairro">
            <i class="fas fa-home"></i> Bairro:
        </label>
        <input type="text" id="bairro" name="bairro" required><br><br>

        <label for="cidade">
            <i class="fas fa-city"></i> Cidade:
        </label>
        <input type="text" id="cidade" name="cidade" required><br><br>

        <label for="estado">
            <i class="fas fa-map"></i> Estado:
        </label>
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