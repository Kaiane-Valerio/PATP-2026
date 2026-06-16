<?php
session_start();
require_once "db/conexao.php";

// VERIFICA ID
if (!isset($_GET['id'])) {
    die("ID do cliente não informado.");
}

$id = $_GET['id'];

// BUSCA CLIENTE
$sql = "SELECT * FROM clientes WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    die("Cliente não encontrado.");
}

// ATUALIZAR CLIENTE
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    $identidade = trim($_POST['identidade']);
    $senha = trim($_POST['senha']);

    // CRIPTOGRAFA SENHA
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $update = "UPDATE clientes 
               SET nome = :nome,
                   email = :email,
                   telefone = :telefone,
                   identidade = :identidade,
                   senha = :senha
               WHERE id = :id";

    $stmtUpdate = $pdo->prepare($update);

    $stmtUpdate->execute([
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
        'identidade' => $identidade,
        'senha' => $senhaHash,
        'id' => $id
    ]);

    header("Location: clientes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", sans-serif;

            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: #eef2f7;

            padding: 30px;
        }

        .box {
            background: white;

            width: 100%;
            max-width: 550px;

            padding: 40px;

            border-radius: 18px;

            box-shadow: 0 12px 35px rgba(0, 0, 0, .08);
        }

        h2 {
            font-size: 30px;
            color: #1e293b;

            margin-bottom: 10px;
        }

        .sub {
            color: #64748b;
            font-size: 14px;

            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;

            font-weight: 600;
            color: #334155;
        }

        input {
            width: 100%;

            padding: 14px;

            border: 1px solid #d6dbe3;
            border-radius: 10px;

            margin-bottom: 20px;

            font-size: 15px;
        }

        input:focus {
            outline: none;
            border-color: #2a5298;

            box-shadow: 0 0 0 3px rgba(42, 82, 152, .12);
        }

        .acoes {
            display: flex;
            gap: 12px;
        }

        button,
        .voltar {
            flex: 1;

            padding: 14px;

            border: none;
            border-radius: 10px;

            font-size: 15px;
            font-weight: 600;

            cursor: pointer;

            text-align: center;
            text-decoration: none;

            transition: .2s;
        }

        button {
            background: #2563eb;
            color: white;
        }

        button:hover {
            opacity: .9;
        }

        .voltar {
            background: #e2e8f0;
            color: #1e293b;
        }

        .voltar:hover {
            background: #cbd5e1;
        }

        @media(max-width:600px) {

            .acoes {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="box">

        <h2>Editar Cliente</h2>

        <p class="sub">
            Atualize as informações do cliente.
        </p>

        <form method="POST">

            <label>Nome</label>

            <input
                type="text"
                name="nome"
                value="<?= htmlspecialchars($cliente['nome']); ?>"
                required>

            <label>E-mail</label>

            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars($cliente['email']); ?>"
                required>

            <label>Telefone</label>

            <input
                type="text"
                name="telefone"
                value="<?= htmlspecialchars($cliente['telefone']); ?>"
                required>

            <label>Identidade</label>

            <input
                type="text"
                name="identidade"
                value="<?= htmlspecialchars($cliente['identidade']); ?>"
                required>

            <label>Senha</label>

            <input
                type="password"
                name="senha"
                placeholder="Digite uma nova senha"
                required>

            <div class="acoes">

                <button type="submit">
                    Salvar Alterações
                </button>

                <a href="clientes.php" class="voltar">
                    Voltar
                </a>

            </div>

        </form>

    </div>

</body>

</html>