<?php
session_start();
require_once "../db/conexao.php";

// Cadastro de cliente
$erro = "";
$mensagem = "";
if ($_POST) {
    $nome = $_POST["nome"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $telefone = $_POST["telefone"];
    $email = $_POST["email"];
    $identidade = $_POST["identidade"];

    // Verificar se o email já existe
    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["email" => $email]);

    if ($stmt->rowCount() > 0) {
        $erro = "Email já cadastrado!";
    } else {
        // Inserir novo cliente
        $sql = "INSERT INTO clientes (nome, senha, telefone, email, identidade) VALUES (:nome, :senha, :telefone, :email, :identidade)";
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute([
            "nome" => $nome,
            "senha" => $senha,
            "telefone" => $telefone,
            "email" => $email,
            "identidade" => $identidade
        ]);

        if ($resultado) {
            $mensagem = "Cadastro realizado com sucesso!";

            header("refresh:2;url=loginCliente.php");
        } else {
            $erro = "Erro ao cadastrar. Tente novamente.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Sistema OS - Cadastro</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", sans-serif;
    height: 100vh;
    display: flex;
}

.left {
    width: 55%;
    background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
}

.left h1 {
    font-size: 40px;
    margin-bottom: 15px;
}

.left p {
    font-size: 18px;
    opacity: 0.9;
}

.right {
    width: 45%;
    background: #f4f6f9;
    display: flex;
    justify-content: center;
    align-items: center;
}

.box {
    background: #fff;
    padding: 40px;
    width: 350px;
    border-radius: 12px;
    box-shadow: 0px 10px 25px rgba(0,0,0,0.1);
}

.box h2 {
    margin-bottom: 20px;
    color: #333;
}

input {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
}

button {
    width: 100%;
    padding: 12px;
    background: #2c5364;
    border: none;
    color: white;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #203a43;
}

.msg {
    color: green;
    margin-bottom: 10px;
}

.erro {
    color: red;
    margin-bottom: 10px;
}
</style>

</head>
<body>

<div class="left">
    <h1>Cadastro de Operador</h1>
    <p>Crie um novo usuário para acessar o sistema.</p>
</div>

<div class="right">
    <div class="box">
        <h2>Novo Cadastro</h2>

        <?php if ($mensagem != "") { ?>
            <p class="msg"><?php echo $mensagem; ?></p>
        <?php } ?>

        <?php if ($erro != "") { ?>
            <p class="erro"><?php echo $erro; ?></p>
        <?php } ?>

        <form method="POST">
            <input type="text" name="nome" placeholder="Nome completo" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="text" name="telefone" placeholder="Telefone" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="identidade" placeholder="Identidade (RG/CPF)" required>

            <button type="submit">Cadastrar</button>
        </form>
    </div>
</div>

</body>
</html>