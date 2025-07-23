<?php
class Horario {
    private $pdo;
    public $msgErro = "";

    public function conectar($nome, $host, $usuario, $senha) {
        try {
            $this->pdo = new PDO("mysql:dbname=".$nome.";host=".$host, $usuario, $senha);
        } catch (PDOException $e) {
            $this->msgErro = $e->getMessage();
        }
    }

    public function listarHorarios() {
        $sql = $this->pdo->prepare("SELECT * FROM horarios ORDER BY horario_inicio");
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>