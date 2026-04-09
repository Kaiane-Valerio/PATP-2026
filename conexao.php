<?php
// 🔌 CONEXÃO
$host = "localhost";
$dbname = "ordens";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// 🔔 MENSAGENS
$mensagem = "";
$erro = "";

// 📥 RECEBER E SALVAR DADOS (AQUI FICA O SEU CÓDIGO)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $telefone = $_POST["telefone"];
    $email = $_POST["email"];
    $identidade = $_POST["identidade"];

    if ($nome && $senha && $telefone && $email && $identidade) {

        $sql = "INSERT INTO operadores (nome, senha, telefone, email, identidade)
                VALUES ('$nome', '$senha', '$telefone', '$email', '$identidade')";

        if ($conn->query($sql) === TRUE) {
            $mensagem = "Cadastro realizado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar: " . $conn->error;
        }

    } else {
        $erro = "Preencha todos os campos!";
    }
}
?>