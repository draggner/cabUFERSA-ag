<?php
class Renovacao {
    private $pdo;
    public $msgErro = "";

    public function conectar($nome, $host, $usuario, $senha) {
        try {
            $this->pdo = new PDO("mysql:dbname=".$nome.";host=".$host, $usuario, $senha);
        } catch (PDOException $e) {
            $this->msgErro = $e->getMessage();
        }
    }

    // Solicita a renovação e efetiva a reserva para o novo horário
    public function solicitarRenovacao($id_reserva, $novo_inicio, $novo_fim) {
        // Busca a reserva original para pegar cabine, usuário e data
        $sql = $this->pdo->prepare("SELECT * FROM reservas WHERE id_reserva=? LIMIT 1");
        $sql->execute([$id_reserva]);
        $reserva = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$reserva) return false;

        // Busca o id_horario correspondente ao intervalo solicitado
        $sql = $this->pdo->prepare("SELECT id_horario FROM horarios WHERE horario_inicio=? AND horario_fim=? LIMIT 1");
        $sql->execute([$novo_inicio, $novo_fim]);
        $horario = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$horario) return false;

        $id_horario_novo = $horario['id_horario'];
        $id_cabine = $reserva['id_cabine'];
        $id_usuario = $reserva['id_usuario'];
        $data_reserva = $reserva['data_reserva'];

        // Verifica se já existe reserva para esse novo horário/cabine/data
        $sql = $this->pdo->prepare("SELECT * FROM reservas WHERE id_cabine=? AND id_horario=? AND data_reserva=? AND status IN ('Agendada','Confirmada')");
        $sql->execute([$id_cabine, $id_horario_novo, $data_reserva]);
        if($sql->rowCount() > 0) {
            // Não pode renovar para horário já ocupado
            return false;
        }

        // Insere registro na tabela de renovação
        $sql = $this->pdo->prepare("INSERT INTO renovacoes (id_reserva, novo_horario_inicio, novo_horario_fim, status) VALUES (?, ?, ?, 'Aprovada')");
        $ok = $sql->execute([$id_reserva, $novo_inicio, $novo_fim]);

        if ($ok) {
            // Marca reserva original como "Concluída"
            $sql = $this->pdo->prepare("UPDATE reservas SET status='Concluída' WHERE id_reserva=?");
            $sql->execute([$id_reserva]);

            // Cria nova reserva para o novo horário (considera como 'Confirmada')
            $sql = $this->pdo->prepare("INSERT INTO reservas (id_usuario, id_cabine, id_horario, data_reserva, status) VALUES (?, ?, ?, ?, 'Confirmada')");
            return $sql->execute([$id_usuario, $id_cabine, $id_horario_novo, $data_reserva]);
        }
        return false;
    }

    public function listarRenovacoesPorReserva($id_reserva) {
        $sql = $this->pdo->prepare("SELECT * FROM renovacoes WHERE id_reserva = ?");
        $sql->execute([$id_reserva]);
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>