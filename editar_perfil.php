<?php
session_start();

$host = "localhost";
$dbname = "ordens";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}


if (!isset($_SESSION["operador_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["operador_id"];
$stmt = $conn->prepare("SELECT * FROM operadores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if (!$usuario) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Sistema OS</title>

    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 380px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #1e3c72;
            font-size: 24px;
        }

        form {
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-sizing: border-box; 
            transition: 0.3s;
        }

        input:focus {
            border-color: #1e3c72;
            outline: none;
            box-shadow: 0 0 5px rgba(30,60,114,0.2);
        }

        button {
            width: 100%;
            padding: 14px;
            background: #1e3c72;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background: #2a5298;
            transform: translateY(-2px);
        }

        .voltar {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #666;
            font-size: 14px;
            transition: 0.3s;
        }

        .voltar:hover {
            color: #1e3c72;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>👤 Editar Perfil</h2>

    
    <form action="salvar_edicao.php" method="POST">

        <label>Nome Completo</label>
        <input type="text" name="nome" value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>

        <label>E-mail</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

        <label>Telefone</label>
        <input type="text" name="telefone" value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>">

        <label>Identidade (RG/CPF)</label>
        <input type="text" name="identidade" value="<?php echo htmlspecialchars($usuario['identidade'] ?? ''); ?>">

        <label>Alterar Senha</label>
        <input type="password" name="senha" placeholder="Deixe vazio para manter a atual">

        <button type="submit">💾 Salvar Alterações</button>
    </form>

    <a href="inicial.php" class="voltar">⬅ Voltar ao Painel</a>
</div>

</body>
</html>