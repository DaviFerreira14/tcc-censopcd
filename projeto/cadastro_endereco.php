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

// Verificar se o usuário já possui um endereço cadastrado ao acessar a página
$stmt = $conn->prepare("SELECT COUNT(*) FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($enderecoCount);
$stmt->fetch();
$stmt->close();

if ($enderecoCount > 0) {
    // Caso o usuário já tenha um endereço cadastrado, mostrar a mensagem
    $message = "Você já possui um endereço cadastrado! Caso queira alterá-lo, vá até seu perfil e clique em 'Trocar Endereço'.";
}

// Se o formulário for enviado (caso o usuário não tenha endereço cadastrado)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $bairro = $_POST['bairro'];
    $cidade = $_POST['cidade'];
    $estado = $_POST['estado'];

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
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CensoPCD+</title>
    <link id="themeStylesheet" rel="stylesheet" href="cadastro_endereco.css">
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

</head>
<body>
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

    <!-- Exibir mensagem assim que a página é carregada -->
    <?php if ($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- Se o usuário não tem endereço cadastrado, exibe o formulário -->
    <?php if ($enderecoCount == 0): ?>
    <form method="POST" action="">
        <label for="cep">
            <i class="fas fa-mail-bulk"></i> CEP:
        </label>
        <input type="text" id="cep" name="cep" required maxlength="10" placeholder="00000-000" oninput="formatCep()" /><br><br>

        <button type="button" id="buscar">Buscar</button><br><br>

        <label for="logradouro">
            <i class="fas fa-map-marker-alt"></i> Rua:
        </label>
        <input type="text" id="logradouro" name="logradouro" required readonly><br><br>

        <label for="bairro">
            <i class="fas fa-home"></i> Bairro:
        </label>
        <input type="text" id="bairro" name="bairro" required readonly><br><br>

        <label for="cidade">
            <i class="fas fa-city"></i> Cidade:
        </label>
        <input type="text" id="cidade" name="cidade" required readonly><br><br>

        <label for="estado">
            <i class="fas fa-map"></i> Estado:
        </label>
        <input type="text" id="estado" name="estado" required readonly><br><br>

        <button type="submit">Cadastrar Endereço</button>
    </form>
    <?php endif; ?>

    <script>
        // Função para formatar o CEP automaticamente
        function formatCep() {
            var cep = document.getElementById("cep").value;
            cep = cep.replace(/\D/g, ''); // Remove tudo que não for número
            cep = cep.replace(/^(\d{5})(\d{3})$/, '$1-$2'); // Adiciona o hífen
            document.getElementById("cep").value = cep;
        }

        // Script para buscar as informações do CEP
        $('#buscar').click(function() {
            var cep = $('#cep').val().replace(/\D/g, ''); // Remove caracteres não numéricos
            if (cep.length != 8) {
                $('.message').text('CEP inválido!').addClass('error');
                return;
            }
            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                type: 'GET',
                success: function(data) {
                    if (!data.erro) {
                        $('#logradouro').val(data.logradouro).prop('readonly', true);
                        $('#bairro').val(data.bairro).prop('readonly', true);
                        $('#cidade').val(data.localidade).prop('readonly', true);
                        $('#estado').val(data.uf).prop('readonly', true);
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

        $(document).ready(function() {
            var savedTheme = localStorage.getItem('theme') || 'light'; // Tema padrão
            if (savedTheme === 'dark') {
                $('#themeStylesheet').attr('href', 'cadastro_endereco_escuro.css');
            }
        });
    </script>
</body>
</html>