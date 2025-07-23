<?php
session_start();
if(!isset($_SESSION['id_usuario'])) {
    header("location: index.php");
    exit;
}
require_once 'Classes/Reserva.php';
require_once 'Classes/Renovacao.php';
$reserva = new Reserva();
$renovacao = new Renovacao();
$reserva->conectar("projetologin", "localhost", "root", "");
$renovacao->conectar("projetologin", "localhost", "root", "");

$id_usuario = $_SESSION['id_usuario'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancelar']) && isset($_POST['id_reserva'])) {
        $id_reserva = intval($_POST['id_reserva']);
        if($reserva->cancelarReserva($id_reserva, $id_usuario)) {
            $msg = '<div class="msgSucesso">Reserva cancelada com sucesso!</div>';
        } else {
            $msg = '<div class="msgErro">Não foi possível cancelar. Verifique se a reserva já foi cancelada ou não pertence a você.</div>';
        }
    }
    if (isset($_POST['renovar']) && isset($_POST['id_reserva']) && isset($_POST['novo_inicio']) && isset($_POST['novo_fim'])) {
        $id_reserva = intval($_POST['id_reserva']);
        $novo_inicio = $_POST['novo_inicio'];
        $novo_fim = $_POST['novo_fim'];
        if($renovacao->solicitarRenovacao($id_reserva, $novo_inicio, $novo_fim)) {
            $msg = '<div class="msgSucesso">Renovação realizada! O horário está agora ocupado.</div>';
        } else {
            $msg = '<div class="msgErro">Não foi possível renovar. O horário escolhido pode estar ocupado ou inválido.</div>';
        }
    }
}
$reservas = $reserva->listarReservasPorUsuario($id_usuario);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minhas Reservas</title>
    <link rel="stylesheet" href="./src/css/styles.css">
    <style>
        .msgErro, .msgSucesso, .msgInfo {
            margin-bottom: 12px;
            padding: 12px 14px;
            border-radius: 9px;
            font-size: 1.04rem;
            font-weight: 500;
        }
        .msgErro { background: #ffd6d6; color: #a94442; border: 1px solid #f5c6cb;}
        .msgSucesso { background: #d6f5d6; color: #256029; border: 1px solid #b9e6b9;}
        .msgInfo { background: #e6f0fa; color: #23527c; border: 1px solid #bcdff1;}
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
            <a href="reservas.php" class="nav-btn">Reservas</a>
            <a href="panorama.php" class="nav-btn">Panorama</a>
            <a href="minhas_reservas.php" class="nav-btn active">Minhas Reservas</a>
            <a href="sair.php" class="nav-btn">Sair</a>
        </nav>
    </div>
    <main>
        <section class="form-container">
            <h2>Minhas Reservas</h2>
            <?= $msg ?>
            <?php if(empty($reservas)) { ?>
                <div class="msgInfo">Você não possui reservas cadastradas.</div>
            <?php } ?>
            <?php foreach($reservas as $r) { ?>
                <div class="reserva-card" style="background:#fff; border-radius:12px; margin-bottom:18px; padding:18px 20px;">
                    <strong><?= ($r['status']=='Confirmada'?'Reserva Ativa':'Próxima Reserva') ?></strong><br>
                    <b>Cabine:</b> <?= htmlspecialchars($r['cabine_nome']) ?><br>
                    <b>Data:</b> <?= date('d/m/Y', strtotime($r['data_reserva'])) ?><br>
                    <b>Horário:</b> <?= substr($r['horario_inicio'],0,5) ?> - <?= substr($r['horario_fim'],0,5) ?><br>
                    <b>Status:</b> <?= $r['status'] ?><br>
                    <form method="POST" style="margin-top:10px;display:inline;">
                        <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                        <?php if($r['status']!='Cancelada' && $r['status']!='Concluída') { ?>
                            <button type="submit" name="cancelar" class="btn-reserva-cancelar" style="background:#d84343;color:#fff;border-radius:18px;padding:8px 18px;border:none;font-weight:bold;cursor:pointer;">CANCELAR</button>
                        <?php } ?>
                    </form>
                    <?php
                    // Só exibe o botão de renovar para reservas confirmadas ou agendadas
                    if($r['status'] == 'Confirmada' || $r['status'] == 'Agendada') {
                    ?>
                    <form method="POST" style="margin-top:8px;display:inline;">
                        <input type="hidden" name="id_reserva" value="<?= $r['id_reserva'] ?>">
                        <label style="margin-right:5px;">Novo Início:</label>
                        <input type="time" name="novo_inicio" required style="margin-right:5px;">
                        <label style="margin-right:5px;">Novo Fim:</label>
                        <input type="time" name="novo_fim" required style="margin-right:5px;">
                        <button type="submit" name="renovar" class="btn-reserva-renovar" style="background:#2196f3;color:#fff;border-radius:18px;padding:8px 18px;border:none;font-weight:bold;cursor:pointer;">RENOVAR</button>
                    </form>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>
    </main>
</body>
</html>