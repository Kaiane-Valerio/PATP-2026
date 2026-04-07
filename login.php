<?php
session_start();

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];


    if ($usuario == "admin" && $senha == "1234") {
        $_SESSION["usuario"] = $usuario;
        header("Location: inicial.php"); 
        exit();
    } else {
        $erro = "Usuário ou senha inválidos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Sistema OS - Login</title>

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

.login-box {
    background: #fff;
    padding: 40px;
    width: 350px;
    border-radius: 12px;
    box-shadow: 0px 10px 25px rgba(0,0,0,0.1);
}

.login-box h2 {
    margin-bottom: 25px;
    color: #333;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    transition: 0.3s;
}

input:focus {
    border-color: #2c5364;
    outline: none;
}

button {
    width: 100%;
    padding: 12px;
    background: #2c5364;
    border: none;
    color: white;
    font-size: 15px;
    border-radius: 6px;
    cursor: pointer;
}

button:hover {
    background: #203a43;
}

.erro {
    color: red;
    margin-bottom: 10px;
}
</style>

</head>
<body>

<div class="left">
    <h1>Sistema de Ordem de Serviços</h1>
    <p>Gerencie atendimentos, controle tarefas e acompanhe serviços em tempo real.</p>
</div>

<div class="right">
    <div class="login-box">
        <h2>Acesso ao Sistema</h2>

        <?php if ($erro != "") { ?>
            <p class="erro"><?php echo $erro; ?></p>
        <?php } ?>

        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuário" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>
    </div>
</div>

</body>
</html>