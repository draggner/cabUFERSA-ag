<?php
require_once __DIR__ . '/Classes/Usuario.php';
$u = new Usuario;
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = addslashes($_POST['nome'] ?? '');
    $telefone = addslashes($_POST['telefone'] ?? '');
    $email = addslashes($_POST['email'] ?? '');
    $senha = addslashes($_POST['senha'] ?? '');
    $confSenha = addslashes($_POST['confSenha'] ?? '');
    if (!empty($nome) && !empty($telefone) && !empty($email) && !empty($senha) && !empty($confSenha)) {
        $u->conectar("projetologin", "localhost", "root", "");
        if ($u->msgErro == "") {
            if ($senha == $confSenha) {
                if ($u->cadastrar($nome, $telefone, $email, $senha)) {
                    $msg = '<div class="msgSucesso">Cadastrado com sucesso! <a href="index.php">Clique aqui para entrar.</a></div>';
                } else {
                    $msg = '<div class="msgErro">Email já cadastrado!</div>';
                }
            } else {
                $msg = '<div class="msgErro">Senha e Confirmar senha não correspondem!</div>';
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
    <title>CabUFERSA AG - Cadastro</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/css/styles.css">
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
            <a href="cadastrar.php" class="nav-btn active">Cadastro</a>
            <a href="index.php" class="nav-btn">Login</a>
        </nav>
    </div>
    <main>
        <section class="form-container">
            <h2>Cadastro de Estudante</h2>
            <?php if($msg) echo $msg; ?>
            <form method="POST" action="">
                <label for="nome">Nome Completo:</label>
                <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required maxlength="30">

                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" placeholder="Digite seu telefone" required maxlength="30">

                <label for="email">E-mail Institucional:</label>
                <input type="email" id="email" name="email" placeholder="exemplo@ufersa.edu.br" required maxlength="40">

                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required minlength="8" maxlength="15">

                <label for="confSenha">Confirmar Senha:</label>
                <input type="password" id="confSenha" name="confSenha" placeholder="Confirme sua senha" required minlength="8" maxlength="15">

                <button type="submit" class="submit-btn">CADASTRAR</button>
                <p style="margin-top:16px;">Já tem cadastro? <a href="index.php">Entrar</a></p>
            </form>
        </section>
    </main>
</body>
</html>