<?php
session_start();
require_once "../db/conexao.php";

$erro = "";

if ($_POST) {
    $email = $_POST["email"];
    $senha = $_POST["senha"];


    $sql = "SELECT * FROM clientes WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["email" => $email]);

    $user = $stmt->fetch();

    // Verificar se o usuário existe
    if ($user) {
        // Verificar a senha
        if (password_verify($senha, $user["senha"])) {
            $_SESSION["cliente"] = $user["nome"];
            $_SESSION["cliente_id"] = $user["id"];
            header("Location: chamados.php");
            exit();
        } else {
            $erro = "Senha incorreta!";
        }
    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Sistema OS</title>

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
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #2c5364;
            color: white;
            border: none;
            border-radius: 6px;
        }

        button:hover {
            background: #203a43;
        }

        .erro {
            color: red;
            margin-bottom: 10px;
        }

        .link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="left">
        <h1>Sistema de Ordem de Serviços</h1>
        <p>Gerencie atendimentos, controle tarefas e acompanhe serviços em tempo real.</p>
    </div>

    <div class="right">
        <div class="box">
            <h2>Login</h2>

            <?php if ($erro != "") { ?>
                <p class="erro"><?php echo $erro; ?></p>
            <?php } ?>

            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>

            <div class="link">
                <a href="cadastroCliente.php">Criar conta</a>
            </div>
        </div>
    </div>

</body>

</html>