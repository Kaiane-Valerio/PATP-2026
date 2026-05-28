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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta de Cliente</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {

            --primary: #2563eb;
            --primary-dark: #1d4ed8;

            --bg: #eef2f7;
            --card: #ffffff;

            --text: #0f172a;
            --text-light: #64748b;

            --border: #dbe2ea;

            --success-bg: #dcfce7;
            --success-text: #166534;

            --danger-bg: #fee2e2;
            --danger-text: #b91c1c;

            --shadow:
                0 12px 35px rgba(0, 0, 0, .08);

            --radius: 22px;
        }

        html {

            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {

            font-family: "Segoe UI", sans-serif;

            min-height: 100dvh;

            display: flex;

            background:
                linear-gradient(to bottom right,
                    #eef2f7,
                    #f8fafc);

            overflow-x: hidden;
        }

        /* =========================
   LADO ESQUERDO
========================= */

        .left {

            flex: 1;

            background:
                linear-gradient(135deg,
                    #1e3c72,
                    #2563eb);

            color: white;

            display: flex;
            flex-direction: column;
            justify-content: center;

            padding:
                clamp(30px, 6vw, 90px);

            position: relative;

            overflow: hidden;
        }

        .left h1 {

            font-size:
                clamp(2.2rem, 5vw, 4rem);

            line-height: 1.1;

            margin-bottom: 18px;

            font-weight: 700;

            position: relative;
            z-index: 2;
        }

        .left p {

            font-size:
                clamp(1rem, 2vw, 1.15rem);

            line-height: 1.7;

            max-width: 550px;

            opacity: .95;

            position: relative;
            z-index: 2;
        }

        /* =========================
   LADO DIREITO
========================= */

        .right {

            flex: 1;

            display: flex;
            justify-content: center;
            align-items: center;

            padding:
                clamp(20px, 5vw, 60px);
        }

        /* =========================
   CARD
========================= */

        .box {

            width: 100%;
            max-width: 460px;

            background: var(--card);

            padding:
                clamp(28px, 4vw, 50px);

            border-radius: var(--radius);

            box-shadow: var(--shadow);

            border: 1px solid rgba(255, 255, 255, .7);

            backdrop-filter: blur(10px);

            animation: fadeUp .5s ease;
        }

        @keyframes fadeUp {

            from {

                opacity: 0;
                transform: translateY(18px);
            }

            to {

                opacity: 1;
                transform: translateY(0);
            }
        }

        .box h2 {

            font-size:
                clamp(1.8rem, 3vw, 2.3rem);

            color: var(--text);

            margin-bottom: 10px;
        }

        .sub {

            color: var(--text-light);

            line-height: 1.6;

            margin-bottom: 28px;

            font-size: .95rem;
        }

        /* =========================
   ALERTAS
========================= */

        .msg,
        .erro {

            padding: 14px;

            border-radius: 14px;

            margin-bottom: 18px;

            font-size: .95rem;

            border: 1px solid transparent;
        }

        .msg {

            background: var(--success-bg);
            color: var(--success-text);

            border-color: #bbf7d0;
        }

        .erro {

            background: var(--danger-bg);
            color: var(--danger-text);

            border-color: #fecaca;
        }

        /* =========================
   INPUTS
========================= */

        input {

            width: 100%;

            height: 56px;

            padding: 0 16px;

            margin-bottom: 15px;

            border-radius: 14px;

            border: 1px solid var(--border);

            background: #fff;

            font-size: .95rem;

            transition:
                .25s ease;
        }

        input:focus {

            outline: none;

            border-color: var(--primary);

            box-shadow:
                0 0 0 4px rgba(37, 99, 235, .12);
        }

        input::placeholder {

            color: #94a3b8;
        }

        /* =========================
   SENHA
========================= */

        .senha-box {

            position: relative;
        }

        .senha-box input {

            padding-right: 50px;
        }

        .toggle {

            position: absolute;

            top: 50%;
            right: 16px;

            transform: translateY(-50%);

            cursor: pointer;

            user-select: none;

            font-size: 18px;
        }

        /* =========================
   BOTÃO
========================= */

        button {

            width: 100%;

            min-height: 56px;

            border: none;

            border-radius: 14px;

            background:
                linear-gradient(to right,
                    var(--primary),
                    var(--primary-dark));

            color: white;

            font-size: 1rem;
            font-weight: 600;

            cursor: pointer;

            transition:
                .25s ease;
        }

        button:hover {

            transform: translateY(-2px);

            box-shadow:
                0 10px 25px rgba(37, 99, 235, .25);
        }

        button:active {

            transform: scale(.98);
        }

        /* =========================
   LINKS
========================= */

        .link {

            text-align: center;

            margin-top: 24px;

            font-size: .95rem;

            color: var(--text-light);
        }

        .link a {

            color: var(--primary);

            text-decoration: none;

            font-weight: 600;
        }

        .link a:hover {

            text-decoration: underline;
        }

        /* =========================
   RESPONSIVIDADE
========================= */

        /* TABLET */

        @media (max-width: 992px) {

            body {

                flex-direction: column;
            }

            .left {

                min-height: 34vh;
            }

            .right {

                padding-top: 0;
            }
        }

        /* CELULAR */

        @media (max-width: 576px) {

            body {

                overflow-y: auto;
            }

            .left {

                min-height: auto;

                align-items: center;

                text-align: center;
            }

            .left p {

                max-width: 100%;
            }

            .box {

                border-radius: 20px;
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
                <div class="senha-box">
                    <input
                        type="password"
                        name="senha"
                        placeholder="Senha"
                        id="senha"
                        required>
                    <span class="toggle" onclick="toggleSenha('senha', this)">👁️</span>
                </div>

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

    <script>
        function toggleSenha(id, el) {
            const input = document.getElementById(id);

            if (input.type === "password") {
                input.type = "text";
                el.textContent = "🙈"; // olho fechado
            } else {
                input.type = "password";
                el.textContent = "👁️"; // olho aberto
            }
        }
    </script>

</body>

</html>