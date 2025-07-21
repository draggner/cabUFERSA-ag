<?php
require_once __DIR__ . '/Classes/Usuario.php';
$u = new Usuario;
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = addslashes($_POST['email'] ?? '');
    $senha = addslashes($_POST['senha'] ?? '');
    if (!empty($email) && !empty($senha)) {
        $u->conectar("projetologin", "localhost", "root", "");
        if ($u->msgErro == "") {
            if ($u->logar($email, $senha)) {
                header("location: AreaPrivada.php");
                exit;
            } else {
                $msg = '<div class="msgErro">Email e/ou senha estão incorretos!</div>';
            }
        } else {
            $msg = '<div class="msgErro">Erro: ' . $u->msgErro . '</div>';
        }
    } else {
        $msg = '<div class="msgErro">Preencha todos os campos!</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CabUFERSA AG - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/css/styles.css">
    <link rel="stylesheet" href="./src/css/login.css">
</head>
<body>
    <div class="header-container">
        <header class="main-header">
            <h1>CabUFERSA AG</h1>
            <p class="subtitle">
                Sistema de Controle de Reservas de Cabines de Estudos<br>
                Universidade Federal Rural do Semi-Árido - Campus Angicos
            </p>
        </header>
        <nav class="main-nav">
            <a href="cadastrar.php" class="nav-btn">Cadastro</a>
            <a href="index.php" class="nav-btn active">Login</a>
        </nav>
    </div>
    <main>
        <section class="form-container" id="login-form">
            <h2>Login no Sistema</h2>
            <?php if($msg) echo $msg; ?>
            <form method="POST" action="">
                <label for="email">Usuário ou E-mail:</label>
                <input type="text" id="email" name="email" placeholder="Digite seu usuário ou e-mail institucional" required>

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                <div class="login-btn-group">
                    <button type="submit" class="login-btn">ENTRAR</button>
                    <button type="button" class="recover-btn" onclick="alert('Função de recuperação de senha em breve!')">RECUPERAR SENHA</button>
                </div>
                <p style="margin-top:16px;">Ainda não é inscrito? <a href="cadastrar.php"><strong>Cadastre-se!</strong></a></p>
            </form>
        </section>
    </main>
</body>
</html>