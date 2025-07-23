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
if (isset($_GET['data']) && $_GET['data'] != "") {
    $data = $_GET['data'];
}

$cabines = $cabine->listarCabines();
$horarios = $horario->listarHorarios();

// Montar mapa de status: [id_horario][id_cabine] = status
$statusMap = [];
foreach ($horarios as $h) {
    foreach ($cabines as $c) {
        $statusMap[$h['id_horario']][$c['id_cabine']] = 'Disponível';
    }
}

// Buscar status reais das reservas para a data selecionada
$reservasDoDia = $reserva->listarReservasPorDia($data);
foreach ($reservasDoDia as $r) {
    $status = ($r['status'] == "Agendada" || $r['status'] == "Confirmada") ? "Ocupada" : ($r['status'] == "Renovável" ? "Renovável" : "Disponível");
    $statusMap[$r['id_horario']][$r['id_cabine']] = $status;
}

// Função para cor dos status
function statusColor($status) {
    if ($status == 'Disponível') return 'style="background:#d6f5d6"';
    if ($status == 'Ocupada')     return 'style="background:#ffd6d6"';
    if ($status == 'Renovável')   return 'style="background:#fffac6"';
    return '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Panorama de Horários</title>
    <link rel="stylesheet" href="./src/css/styles.css">
    <style>
        .panorama-table { width:100%; border-collapse: collapse; }
        .panorama-table th,.panorama-table td { padding:10px 7px; text-align:center; }
        .panorama-table th { background:#f1f1f9; }
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
            <a href="panorama.php" class="nav-btn active">Panorama</a>
            <a href="minhas_reservas.php" class="nav-btn">Minhas Reservas</a>
            <a href="sair.php" class="nav-btn">Sair</a>
        </nav>
    </div>
    <main>
        <section class="form-container">
            <h2>Panorama de Horários</h2>
            <form method="GET" action="" style="margin-bottom:18px;">
                <label for="data">Selecionar Data:</label>
                <input type="date" name="data" id="data" value="<?= $data ?>" required>
                <button type="submit" class="submit-btn" style="margin-left:10px;">Ver</button>
            </form>
            <div style="overflow-x:auto;">
                <table class="panorama-table" border="1">
                    <tr>
                        <th>Horário</th>
                        <?php foreach($cabines as $cab) { ?>
                            <th><?= htmlspecialchars($cab['nome']) ?></th>
                        <?php } ?>
                    </tr>
                    <?php foreach($horarios as $hor) { ?>
                        <tr>
                            <td><?= substr($hor['horario_inicio'],0,5) ?>-<?= substr($hor['horario_fim'],0,5) ?></td>
                            <?php foreach($cabines as $cab) {
                                $status = $statusMap[$hor['id_horario']][$cab['id_cabine']];
                                ?>
                                <td <?= statusColor($status) ?>>
                                    <?php if($status == 'Disponível') { ?>
                                        <span style="color:#217a27;font-weight:600;"><?= $status ?></span>
                                    <?php } else if ($status == 'Ocupada') { ?>
                                        <span style="color:#b52626;font-weight:600;"><?= $status ?></span>
                                    <?php } else { ?>
                                        <span style="color:#b6a50b;font-weight:600;"><?= $status ?></span>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </section>
    </main>
</body>
</html>