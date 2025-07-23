<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    header("location: index.php");
    exit;
}
require_once 'Classes/Reserva.php';
require_once 'Classes/Cabine.php';
require_once 'Classes/Horario.php';
$reserva = new Reserva();
$cabine = new Cabine();
$horario = new Horario();
$reserva->conectar("projetologin", "localhost", "root", "");
$cabine->conectar("projetologin", "localhost", "root", "");
$horario->conectar("projetologin", "localhost", "root", "");

$data = date('Y-m-d');
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cabine'], $_POST['horario'], $_POST['data_reserva'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $id_cabine = intval($_POST['cabine']);
    $id_horario = intval($_POST['horario']);
    $data_reserva = $_POST['data_reserva'];
    if ($reserva->reservar($id_usuario, $id_cabine, $id_horario, $data_reserva)) {
        $msg = '<div class="msgSucesso">Reserva realizada com sucesso!</div>';
    } else {
        $msg = '<div class="msgErro">Já existe reserva para essa cabine/horário/data!</div>';
    }
}
$cabines = $cabine->listarCabines();
$horarios = $horario->listarHorarios();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Solicitar Reserva de Cabine</title>
    <link rel="stylesheet" href="./src/css/styles.css">
    <style>
        .msgErro, .msgSucesso {
            margin-bottom: 12px;
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 1.04rem;
            font-weight: 500;
        }
        .msgErro { background: #ffd6d6; color: #a94442; border: 1px solid #f5c6cb;}
        .msgSucesso { background: #d6f5d6; color: #256029; border: 1px solid #b9e6b9;}
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
            <a href="AreaPrivada.php" class="nav-btn">Início</a>
            <a href="reservas.php" class="nav-btn active">Reservas</a>
            <a href="panorama.php" class="nav-btn">Panorama</a>
            <a href="minhas_reservas.php" class="nav-btn">Minhas Reservas</a>
            <a href="sair.php" class="nav-btn">Sair</a>
        </nav>
    </div>
    <main>
        <section class="form-container">
            <h2>Solicitar Reserva de Cabine</h2>
            <?= $msg ?>
            <form method="POST" action="">
                <label for="data_reserva">Data da Reserva:</label>
                <input type="date" id="data_reserva" name="data_reserva" value="<?= $data ?>" required>
                <label for="cabine">Cabine:</label>
                <select name="cabine" id="cabine" required>
                    <option value="">Selecione a cabine</option>
                    <?php foreach($cabines as $cab) { ?>
                        <option value="<?= $cab['id_cabine'] ?>"><?= $cab['nome'] ?> (Capacidade: <?= $cab['capacidade'] ?>)</option>
                    <?php } ?>
                </select>
                <label for="horario">Horário:</label>
                <select name="horario" id="horario" required>
                    <option value="">Selecione o horário</option>
                    <?php foreach($horarios as $hor) { ?>
                        <option value="<?= $hor['id_horario'] ?>"><?= substr($hor['horario_inicio'],0,5) ?> - <?= substr($hor['horario_fim'],0,5) ?></option>
                    <?php } ?>
                </select>
                <button type="submit" class="submit-btn">SOLICITAR RESERVA</button>
            </form>
        </section>
    </main>
</body>
</html>