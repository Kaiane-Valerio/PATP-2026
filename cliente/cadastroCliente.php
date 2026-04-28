<?php
session_start();
require_once "../db/conexao.php";

$erro = "";
$mensagem = "";

if ($_POST) {

    $nome = $_POST["nome"];
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
    $telefone = $_POST["telefone"];
    $email = $_POST["email"];
    $identidade = $_POST["identidade"];

    $sql = "SELECT * FROM clientes WHERE email=:email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["email" => $email]);

    if ($stmt->rowCount() > 0) {

        $erro = "Email já cadastrado!";
    } else {

        $sql = "INSERT INTO clientes
(nome,senha,telefone,email,identidade)
VALUES
(:nome,:senha,:telefone,:email,:identidade)";

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
            $erro = "Erro ao cadastrar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Criar Conta de Cliente</title>

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
            font-size: 42px;
            margin-bottom: 18px;
        }

        .left p {
            font-size: 18px;
            max-width: 500px;
            line-height: 1.5;
        }

        .right {
            width: 45%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .box {
            background: #fff;
            width: 420px;

            padding: 42px;

            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, .08);
        }

        .box h2 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .sub {
            color: #64748b;
            margin-bottom: 25px;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px;
            margin-bottom: 12px;

            border: 1px solid #d6dbe3;
            border-radius: 10px;
        }

        input:focus {
            outline: none;
            border-color: #2a5298;
        }

        button {
            width: 100%;
            padding: 14px;

            background: #2a5298;
            color: white;

            border: none;
            border-radius: 10px;

            font-size: 16px;
            font-weight: 600;

            cursor: pointer;
            transition: .25s;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 82, 152, .25);
        }

        .msg {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .erro {
            background: #ffe5e5;
            color: #b91c1c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .link {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
        }

        .link a {
            color: #2a5298;
            text-decoration: none;
            font-weight: 600;
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
                max-width: 430px;
            }

        }
    </style>

</head>

<body>

    <div class="left">

        <h1>Criar conta de cliente</h1>

        <p>
            Cadastre-se para abrir e acompanhar suas ordens de serviço.
        </p>

    </div>


    <div class="right">

        <div class="box">

            <h2>Novo Cadastro</h2>

            <p class="sub">
                Preencha seus dados para acessar a área do cliente.
            </p>

            <?php if ($mensagem != "") { ?>
                <p class="msg"><?php echo $mensagem; ?></p>
            <?php } ?>

            <?php if ($erro != "") { ?>
                <p class="erro"><?php echo $erro; ?></p>
            <?php } ?>

            <form method="POST">

                <input
                    type="text"
                    name="nome"
                    placeholder="Nome"
                    required>

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

                <input
                    type="text"
                    name="telefone"
                    placeholder="Telefone"
                    required>

                <input
                    type="text"
                    name="identidade"
                    placeholder="Identidade (RG/CPF)"
                    required>

                <button type="submit">
                    Criar minha conta
                </button>

            </form>

            <div class="link">
                Já possui acesso?
                <a href="loginCliente.php">
                    Fazer login
                </a>
            </div>

        </div>

    </div>

</body>

</html>