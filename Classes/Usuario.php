<?php
class Usuario {
    private $pdo;
    public $msgErro = "";

    public function conectar($nome, $host, $usuario, $senha) {
        global $pdo;
        global $msgErro;
        try {
            $pdo = new PDO("mysql:dbname=".$nome.";host=".$host, $usuario, $senha);
        } catch (PDOException $e) {
            $msgErro = $e->getMessage();
        }
    }

    public function cadastrar($nome, $telefone, $email, $senha) {
        global $pdo;
        global $msgErro;
        // Verificar se já existe o email cadastrado
        $sql = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :e");
        $sql->bindValue(":e", $email);
        $sql->execute();
        if($sql->rowCount() > 0){
            return false; // já cadastrado
        } else {
            $sql = $pdo->prepare("INSERT INTO usuarios (nome, telefone, email, senha) VALUES (:n, :t, :e, :s)");
            $sql->bindValue(":n", $nome);
            $sql->bindValue(":t", $telefone);
            $sql->bindValue(":e", $email);
            $sql->bindValue(":s", password_hash($senha, PASSWORD_DEFAULT));
            $sql->execute();
            return true; // cadastro ok
        }
    }

    public function logar($email, $senha) {
        global $pdo;
        global $msgErro;
        // Verificar se o email e senha estão cadastrados
        $sql = $pdo->prepare("SELECT id_usuario, senha FROM usuarios WHERE email = :e");
        $sql->bindValue(":e", $email);
        $sql->execute();
        if($sql->rowCount() > 0){
            $dado = $sql->fetch();
            if (password_verify($senha, $dado['senha'])) {
                session_start();
                $_SESSION['id_usuario'] = $dado['id_usuario'];
                return true;
            }
        }
        return false;
    }
}
?>