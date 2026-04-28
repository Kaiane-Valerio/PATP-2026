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

    if ($user) {

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
    <title>Acesso do Cliente</title>

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
            background: #eef2f7;
        }

        /* LADO ESQUERDO */
        .left {
            width: 55%;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;

            display: flex;
            flex-direction: column;
            justify-content: center;

            padding: 70px;
        }

        .left h1 {
            font-size: 46px;
            margin-bottom: 18px;
            line-height: 1.1;
        }

        .left p {
            font-size: 19px;
            opacity: .95;
            max-width: 500px;
            line-height: 1.5;
        }

        /* LADO DIREITO */
        .right {
            width: 45%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .box {
            background: white;
            width: 390px;

            padding: 45px;
            border-radius: 18px;

            box-shadow: 0 12px 35px rgba(0, 0, 0, .08);
        }

        .box h2 {
            font-size: 30px;
            margin-bottom: 10px;
            color: #1e293b;
        }

        .sub {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 28px;
        }

        .erro {
            background: #ffe5e5;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 14px 15px;
            margin-bottom: 14px;

            border: 1px solid #d6dbe3;
            border-radius: 10px;

            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, .12);
        }

        button {
            width: 100%;
            padding: 14px;

            border: none;
            border-radius: 10px;

            background: #2a5298;
            color: white;

            font-weight: 600;
            font-size: 16px;

            cursor: pointer;
            transition: .25s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 82, 152, .25);
        }

        .link {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .link a {
            text-decoration: none;
            font-weight: 600;
            color: #2a5298;
        }

        .link a:hover {
            text-decoration: underline;
        }

        @media(max-width:900px) {

            body {
                flex-direction: column;
            }

            .left,
            .right {
                width: 100%;
            }

            .left {
                padding: 40px 30px;
                min-height: 35vh;
            }

            .box {
                width: 100%;
                max-width: 420px;
            }

        }
    </style>

</head>

<body>

    <div class="left">
        <h1>Acesso do Cliente</h1>

        <p>
            Entre para acompanhar suas ordens de serviço e abrir novas solicitações.
        </p>

    </div>


    <div class="right">

        <div class="box">

            <h2>Entrar</h2>

            <p class="sub">
                Acesse sua área de atendimento.
            </p>

            <?php if ($erro != "") { ?>
                <p class="erro"><?php echo $erro; ?></p>
            <?php } ?>

            <form method="POST">

                <input
                    type="email"
                    name="email"
                    placeholder="E-mail ou Login"
                    required>

                <input
                    type="password"
                    name="senha"
                    placeholder="Senha"
                    required>

                <button type="submit">
                    Acessar área do cliente
                </button>

            </form>

            <div class="link">
                Ainda não possui cadastro?
                <a href="cadastroCliente.php">
                    Criar conta
                </a>
            </div>

        </div>

    </div>

</body>

</html>