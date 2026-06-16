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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso do Cliente</title>

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

            --shadow:
                0 10px 30px rgba(0, 0, 0, 0.08);

            --radius: 22px;
        }

        html {
            font-size: 16px;
            scroll-behavior: smooth;
        }

        body {

            font-family: "Segoe UI", sans-serif;

            background:
                linear-gradient(to bottom right, #eef2f7, #f8fafc);

            min-height: 100dvh;

            display: flex;

            overflow-x: hidden;
        }

        /* =========================
   LEFT
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
                clamp(2.2rem, 5vw, 4.2rem);

            line-height: 1.1;

            margin-bottom: 20px;

            font-weight: 700;

            position: relative;
            z-index: 2;
        }

        .left p {

            max-width: 550px;

            font-size:
                clamp(1rem, 2vw, 1.2rem);

            line-height: 1.7;

            opacity: .95;

            position: relative;
            z-index: 2;
        }

        /* =========================
   RIGHT
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
   CARD LOGIN
========================= */

        .box {

            width: 100%;
            max-width: 430px;

            background: var(--card);

            border-radius: var(--radius);

            padding:
                clamp(25px, 4vw, 50px);

            box-shadow: var(--shadow);

            border: 1px solid rgba(255, 255, 255, .7);

            backdrop-filter: blur(10px);

            animation: fadeUp .5s ease;
        }

        @keyframes fadeUp {

            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .box h2 {

            color: var(--text);

            font-size:
                clamp(1.8rem, 3vw, 2.2rem);

            margin-bottom: 10px;
        }

        .sub {

            color: var(--text-light);

            line-height: 1.6;

            margin-bottom: 30px;
        }

        /* =========================
   ERRO
========================= */

        .erro {

            background: #fee2e2;

            color: #b91c1c;

            padding: 14px;

            border-radius: 12px;

            margin-bottom: 18px;

            font-size: .95rem;

            border: 1px solid #fecaca;
        }

        /* =========================
   INPUTS
========================= */

        input {

            width: 100%;

            height: 54px;

            padding: 0 16px;

            border-radius: 14px;

            border: 1px solid var(--border);

            background: #fff;

            font-size: 15px;

            transition: .25s ease;

            margin-bottom: 16px;
        }

        input:focus {

            outline: none;

            border-color: var(--primary);

            box-shadow:
                0 0 0 4px rgba(37, 99, 235, .12);
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

            right: 16px;
            top: 50%;

            transform: translateY(-50%);

            cursor: pointer;

            font-size: 18px;

            user-select: none;
        }

        /* =========================
   BOTÃO
========================= */

        button {

            width: 100%;

            min-height: 54px;

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

            margin-top: 24px;

            text-align: center;

            color: var(--text-light);

            font-size: .95rem;
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

        /* TABLETS */

        @media (max-width: 992px) {

            body {

                flex-direction: column;
            }

            .left {

                min-height: 35vh;
            }

            .right {

                padding-top: 0;
            }
        }

        /* CELULARES */

        @media (max-width: 576px) {

            .left {

                min-height: auto;

                text-align: center;

                align-items: center;
            }

            .left p {

                max-width: 100%;
            }

            .box {

                border-radius: 20px;
            }

            body {

                overflow-y: auto;
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

                <div class="senha-box">
                    <input
                        type="password"
                        name="senha"
                        placeholder="Senha"
                        id="senha"
                        required>
                    <span class="toggle" onclick="toggleSenha('senha', this)">👁️</span>
                </div>

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