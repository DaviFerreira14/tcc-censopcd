<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sobre o CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> <!-- ícone da aba -->
    <link rel="stylesheet" id="themeStylesheet" href="sobre.css"> <!-- Link para o CSS específico, ID para facilitar a troca -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Link para ícones -->
    <script>
        // Função para alternar entre os temas
        function toggleTheme(theme) {
            // Carregar o CSS correspondente ao tema
            const themeStylesheet = document.getElementById('themeStylesheet');
            
            if (theme === 'dark') {
                themeStylesheet.setAttribute('href', 'sobre_escuro.css'); // Mudar para o CSS escuro
            } else {
                themeStylesheet.setAttribute('href', 'sobre.css'); // Mudar para o CSS claro
            }

            // Armazenar a preferência do tema no LocalStorage
            localStorage.setItem('theme', theme);
        }

        // Carregar o tema salvo no LocalStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            toggleTheme(savedTheme);
        });

        // Função para alternar o dropdown
        function toggleDropdown() {
            var dropdown = document.getElementById("themeDropdown");
            dropdown.classList.toggle("show");
        }
    </script>
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

<!-- Menu Lateral/Sidebar -->
<nav class="sidebar" id="sidebar">
    <ul class="sidebarul">
        <li class="sidebarli">
            <a href="pagina_principal.php" class="sidebar-a"><i class="fas fa-home"></i> Incício</a>
        </li>
        <li class="sidebarli">
            <a href="perfil.php" class="sidebar-a"><i class="fas fa-user"></i> Perfil </a>
        </li>
        <li class="sidebarli">
            <a href="cadastro_endereco.php" class="sidebar-a"><i class="fas fa-map-marker-alt"></i> Cadastro de endereço </a>
        </li>
        <li id="fundo" class="sidebarli">
            <a href="sobre.php" class="sidebar-a"><i class="fas fa-info-circle"></i> Sobre </a>
        </li>
        <!-- Nova opção de Tema com dropdown -->
        <li class="sidebarli">
            <a href="javascript:void(0);" class="sidebar-a" onclick="toggleDropdown()">
                <i class="fas fa-paint-brush"></i> Tema <i class="fas fa-chevron-down"></i>
            </a>
            <div id="themeDropdown" class="dropdown-content">
                <a href="javascript:void(0);" onclick="toggleTheme('light')">Claro</a>
                <a href="javascript:void(0);" onclick="toggleTheme('dark')">Escuro</a>
            </div>
        </li>
    </ul>
</nav>

<!-- Conteúdo principal -->
<div class="content">
    <h2>Sobre o CensoPCD+</h2>
    <p>O CensoPCD+ é um aplicativo dedicado a fornecer um centro de reclamações para pessoas com deficiência. Nosso objetivo é oferecer uma plataforma onde os usuários possam registrar suas preocupações e obter suporte eficaz para suas necessidades específicas.</p>

    <div class="faq">
        <h3>Principais Dúvidas</h3>
        <p><strong>1. Como posso registrar uma reclamação?</strong><br>
        Basta acessar a página de reclamações e preencher o formulário com os detalhes da sua reclamação.</p>
        <p><strong>2. O que devo fazer se a minha reclamação não for resolvida?</strong><br>
        Você pode verificar o status da sua reclamação ou entrar em contato conosco para obter mais assistência.</p>
        <p><strong>3. Como posso acompanhar o status da minha reclamação?</strong><br>
        Na página da reclamação, você pode verificar o status.</p>
    </div>

    <div class="faq">
        <h3>Como Usar</h3>
        <p><strong>Para utilizar o CensoPCD+, siga estas etapas:</strong></p>
        <ul>
            <li>Faça login no sistema.</li>
            <li>Vá para a página de reclamações e preencha o formulário.</li>
            <li>Envie a reclamação e acompanhe o status na página.</li>
            <li>Você pode editar seu perfil e acompanhar o progresso das suas reclamações através das opções disponíveis no menu.</li>
        </ul>
    </div>
</div>

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

<style>
    /* Estilos do Dropdown */
    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #ddd;
    }

    /* Exibir o dropdown quando for ativado */
    .show {
        display: block;
    }
</style>
</body>
</html>