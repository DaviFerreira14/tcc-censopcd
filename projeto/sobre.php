<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sobre o CensoPCD+</title>
    <link rel="icon" href="logos/logofundoinvisivel.ico" type="image/x-icon"> <!-- ícone da aba -->
    <link rel="stylesheet" href="sobre.css"> <!-- Link para o CSS específico -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Link para ícones -->
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
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul>
                <li><a href="pagina_principal.php"><i class="fas fa-home"></i> <strong>Início</strong></a></li>
                <li><a href="perfil.php"><i class="fas fa-user"></i> <strong>Perfil</strong></a></li>
                <li><a href="sobre.php"><i class="fas fa-info-circle"></i> <strong>Sobre</strong></a></li>
            </ul>
        </aside>

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
                <p>Para utilizar o CensoPCD+, siga estas etapas:</p>
                <ul>
                    <li>Faça login no sistema.</li>
                    <li>Vá para a página de reclamações e preencha o formulário.</li>
                    <li>Envie a reclamação e acompanhe o status na página.</li>
                    <li>Você pode editar seu perfil e acompanhar o progresso das suas reclamações através das opções disponíveis no menu.</li>
                </ul>
            </div>
        </div>
    </div>

    <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
        </div>
      </div>
      
    <!-- Codigo Vlibras -->
    <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
    <script>
      new window.VLibras.Widget('https://vlibras.gov.br/app');
    </script>
    <style>
    .acess-button{
    width: 60px!important;
    height: 60px!important;
    z-index: 1!important;
    border-radius: 50px!important;
    border: 1px solid black!important; 
    }
    </style>
</body>
</html>