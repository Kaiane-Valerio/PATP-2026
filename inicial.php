<?php
session_start();

if (!isset($_SESSION["operador_id"])) {
    header("Location: login.php");
    exit();
}


if (isset($_SESSION["operador_nome"])) {
    $nomeUsuario = $_SESSION["operador_nome"];
} elseif (isset($_SESSION["usuario"])) {
    $nomeUsuario = $_SESSION["usuario"];
} else {
    $nomeUsuario = "Operador";
}


$primeiraLetra = strtoupper(substr($nomeUsuario, 0, 1));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema OS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            background: #f4f6f9;
        }

        .sidebar {
            width: 240px;
            height: 100vh;
            background: #1e3c72;
            color: white;
            padding: 20px;
            position: fixed;
        }

        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 22px;
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 12px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #2a5298;
        }

        .main {
            margin-left: 240px;
            width: calc(100% - 240px);
        }

        .header {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

       
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #333;
            padding: 5px 10px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .user-profile:hover {
            background: #f0f2f5;
        }

        .avatar {
            width: 38px;
            height: 38px;
            background: #1e3c72;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .user-name {
            font-weight: 600;
            font-size: 15px;
        }

       
        .content {
            padding: 30px;
        }

        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            flex: 1;
            min-width: 220px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .card h3 {
            color: #777;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .card p {
            font-size: 28px;
            font-weight: bold;
            color: #1e3c72;
        }

        .activity {
            margin-top: 30px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>🛠 Sistema OS</h2>
        <a href="inicial.php">🏠 Início</a>
        <a href="ordens.php">📋 Ordens de Serviço</a>
        <a href="clientes.php">👥 Clientes</a>
        <a href="cadastro.php">➕ Criar novo operador</a>
        <a href="login.php" style="margin-top: 20px; color: #ff9999;">🚪 Sair</a>
    </div>

    <div class="main">
        <div class="header">
            <h1>Painel Inicial</h1>
            
            <a href="editar_perfil.php" class="user-profile" title="Editar Perfil">
                <div class="avatar"><?php echo $primeiraLetra; ?></div>
                <span class="user-name"><?php echo $nomeUsuario; ?></span>
            </a>
        </div>

        <div class="content">
            <div class="cards">
                <div class="card">
                    <h3>Ordens Abertas</h3>
                    <p>0</p>
                </div>
                <div class="card">
                    <h3>Em Andamento</h3>
                    <p>0</p>
                </div>
                <div class="card">
                    <h3>Concluídas</h3>
                    <p>0</p>
                </div>
            </div>

            <div class="activity">
                <h3>📌 Últimas atividades</h3>
                <p style="color: #888; margin-top: 10px;">Nenhuma ordem cadastrada até o momento.</p>
            </div>
        </div>
    </div>

</body>
</html>