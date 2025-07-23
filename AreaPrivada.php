<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>CabUFERSA AG - Área Privada</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./src/css/styles.css">
    <style>
        .msgInfo {
            margin-bottom: 12px;
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 1.04rem;
            font-weight: 500;
            background: #e6f0fa; color: #23527c; border: 1px solid #bcdff1;
        }
    </style>
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
            <a href="AreaPrivada.php" class="nav-btn active">Início</a>
            <a href="reservas.php" class="nav-btn">Reservas</a>
            <a href="panorama.php" class="nav-btn">Panorama</a>
            <a href="minhas_reservas.php" class="nav-btn">Minhas Reservas</a>
            <a href="sair.php" class="nav-btn">Sair</a>
        </nav>
    </div>
    <main>
        <section class="form-container">
            <h2>Bem-vindo à sua área privada!</h2>
            <div class="msgInfo">Você está autenticado no sistema. Utilize os botões acima para navegar entre as funcionalidades.</div>
        </section>
    </main>
</body>
</html>