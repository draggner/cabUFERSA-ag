<?php
class Reserva {
    private $pdo;
    public $msgErro = "";

    public function conectar($nome, $host, $usuario, $senha) {
        try {
            $this->pdo = new PDO("mysql:dbname=".$nome.";host=".$host, $usuario, $senha);
        } catch (PDOException $e) {
            $this->msgErro = $e->getMessage();
        }
    }

    public function reservar($id_usuario, $id_cabine, $id_horario, $data_reserva) {
        // Verifica se já existe reserva para essa cabine, horário e data
        $sql = $this->pdo->prepare("SELECT * FROM reservas WHERE id_cabine=? AND id_horario=? AND data_reserva=? AND status IN ('Agendada','Confirmada')");
        $sql->execute([$id_cabine, $id_horario, $data_reserva]);
        if($sql->rowCount() > 0) {
            return false;
        }
        $sql = $this->pdo->prepare("INSERT INTO reservas (id_usuario, id_cabine, id_horario, data_reserva, status) VALUES (?, ?, ?, ?, 'Agendada')");
        return $sql->execute([$id_usuario, $id_cabine, $id_horario, $data_reserva]);
    }

    public function listarReservasPorUsuario($id_usuario) {
        $sql = $this->pdo->prepare(
            "SELECT r.*, c.nome as cabine_nome, c.capacidade, h.horario_inicio, h.horario_fim
            FROM reservas r
            JOIN cabines c ON r.id_cabine = c.id_cabine
            JOIN horarios h ON r.id_horario = h.id_horario
            WHERE r.id_usuario = ?
            ORDER BY r.data_reserva DESC, h.horario_inicio"
        );
        $sql->execute([$id_usuario]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cancelarReserva($id_reserva, $id_usuario) {
        $sql = $this->pdo->prepare("UPDATE reservas SET status='Cancelada' WHERE id_reserva=? AND id_usuario=?");
        return $sql->execute([$id_reserva, $id_usuario]);
    }

    public function renovarReserva($id_reserva, $novo_inicio, $novo_fim) {
        $sql = $this->pdo->prepare("UPDATE reservas SET status='Confirmada' WHERE id_reserva=?");
        return $sql->execute([$id_reserva]);
    }

    // NOVO: Listar reservas para o panorama por data
    public function listarReservasPorDia($data) {
        $sql = $this->pdo->prepare("SELECT * FROM reservas WHERE data_reserva = ? AND status IN ('Agendada','Confirmada','Renovável')");
        $sql->execute([$data]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function panorama($data) {
        // Não é mais utilizada, mas mantida para compatibilidade.
        return [];
    }

    public function listarCabinesStatus($data) {
        $sql = $this->pdo->prepare(
            "SELECT c.*, 
                SUM(r.status IN ('Agendada','Confirmada')) as ocupada,
                SUM(r.status='Renovável') as renovavel
            FROM cabines c
            LEFT JOIN reservas r ON r.id_cabine = c.id_cabine AND r.data_reserva = ?
            GROUP BY c.id_cabine"
        );
        $sql->execute([$data]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>