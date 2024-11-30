<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

// Incluir o arquivo de conexão com o banco de dados
include 'db.php';

// Buscar informações do usuário logado
$usuario_id = $_SESSION['usuario_id'];
$stmt = $conn->prepare("SELECT Nome FROM usuarios WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Usuário não encontrado.'); window.location.href='pagina_principal.php';</script>";
    exit();
}

$usuario = $result->fetch_assoc();
$stmt->close();

// Buscar informações de endereço do usuário
$stmt = $conn->prepare("SELECT cep, logradouro, bairro, cidade, estado FROM enderecos WHERE usuario_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$endereco = null;
if ($result->num_rows > 0) {
    $endereco = $result->fetch_assoc();
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Trocar Endereço - CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon">
    <link rel="stylesheet" href="trocar_endereco.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    <div class="menu-toggle" onclick="toggleSidebar()">☰</div>
    <script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('active');
    }
    </script>


    <!-- Menu Lateral/Sidebar -->
    <nav class="sidebar">
        <ul class="sidebarul">
            <li class="sidebarli">
                <a href="pagina_principal.php" class="sidebar-a"><i class="fas fa-home"></i> Incío</a>
            </li>
            <li id="fundo" class="sidebarli">
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
            <div class="profile-info">
                <img src="logos/user.jpg" alt="user" class="user">
                
                <?php if ($endereco): ?>
                    <h2>Endereço Atual</h2>
                    <p><strong>CEP:</strong> <?php echo htmlspecialchars(substr($endereco['cep'], 0, 5) . '-' . substr($endereco['cep'], 5)); ?></p>
                    <p><strong>Rua:</strong> <?php echo htmlspecialchars($endereco['logradouro']); ?></p>
                    <p><strong>Bairro:</strong> <?php echo htmlspecialchars($endereco['bairro']); ?></p>
                    <p><strong>Cidade:</strong> <?php echo htmlspecialchars($endereco['cidade']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($endereco['estado']); ?></p>
                <?php else: ?>
                    <p><strong>Endereço:</strong> Nenhum endereço cadastrado.</p>
                <?php endif; ?>
            </div>

            <h1>Buscar Novo Endereço</h1>
            <div class="form-container">
            <form id="formBuscarEndereco">
                <label for="cep">CEP:</label>
                <input type="text" id="cep" name="cep" required>
                <button type="button" id="btnBuscar">Buscar</button>

                <h3>Informações do Novo Endereço</h3>
                <label for="logradouro">Rua:</label>
                <input type="text" id="logradouro" name="logradouro" readonly>
                <label for="bairro">Bairro:</label>
                <input type="text" id="bairro" name="bairro" readonly>
                <label for="cidade">Cidade:</label>
                <input type="text" id="cidade" name="cidade" readonly>
                <label for="estado">Estado:</label>
                <input type="text" id="estado" name="estado" readonly>

                <button type="button" id="btnTrocarEndereco">Trocar Endereço</button>
            </form>
        </main>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#btnBuscar').click(function() {
                const cep = $('#cep').val().replace(/\D/g, '');
                if (cep.length === 8) {
                    $('#cep').val(cep.slice(0, 5) + '-' + cep.slice(5)); // Formata o CEP com o traço
                    $.get(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
                        if (!data.erro) {
                            $('#logradouro').val(data.logradouro);
                            $('#bairro').val(data.bairro);
                            $('#cidade').val(data.localidade);
                            $('#estado').val(data.uf);
                        } else {
                            alert('CEP não encontrado!');
                        }
                    }).fail(function() {
                        alert('Erro ao buscar informações do CEP.');
                    });
                } else {
                    alert('CEP inválido!');
                }
            });

            $('#btnTrocarEndereco').click(function() {
                const logradouro = $('#logradouro').val();
                const bairro = $('#bairro').val();
                const cidade = $('#cidade').val();
                const estado = $('#estado').val();
                const cep = $('#cep').val().replace(/\D/g, ''); // Captura o CEP sem traço
                const usuario_id = <?php echo $usuario_id; ?>; // Passando o ID do usuário

                if (logradouro && bairro && cidade && estado && cep) {
                    $.post('trocar_endereco_action.php', {
                        usuario_id: usuario_id,
                        logradouro: logradouro,
                        bairro: bairro,
                        cidade: cidade,
                        estado: estado,
                        cep: cep // Inclui o CEP na requisição
                    }, function(response) {
                        alert(response.message);
                        if (response.success) {
                            window.location.href = 'pagina_principal.php'; // Redirecionar após sucesso
                        }
                    }, 'json');
                } else {
                    alert('Preencha todos os campos antes de trocar o endereço.');
                }
            });
        });
    </script>
</body>
</html>