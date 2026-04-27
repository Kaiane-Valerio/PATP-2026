<?php
session_start();

if (!isset($_SESSION["operador_id"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard - Sistema OS</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", sans-serif;
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
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    margin: 10px 0;
    padding: 10px;
    border-radius: 6px;
    transition: 0.3s;
}

.sidebar a:hover {
    background: #2a5298;
}

.main {
    margin-left: 240px;
    width: 100%;
}

.header {
    background: white;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.header h1 {
    font-size: 20px;
}

.user {
    font-size: 14px;
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
    min-width: 200px;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.card h3 {
    margin-bottom: 10px;
    color: #555;
}

.card p {
    font-size: 22px;
    font-weight: bold;
    color: #1e3c72;
}

.activity {
    margin-top: 30px;
    background: white;
    padding: 20px;
    border-radius: 10px;
}
</style>

</head>
<body>

<div class="sidebar">
    <h2>🛠 Sistema OS</h2>
    <a href="inicio.php">🏠 Inicio</a>
    <a href="ordens.php">📋 Ordens de Serviço</a>
    <a href="clientes.php">👥 Clientes</a>
    <a href="cadastro.php">➕ Criar novo operador</a> <!-- BOTÃO ADICIONADO -->
    <a href="login.php">🚪 Sair</a>
</div>

<div class="main">

    <div class="header">
        <h1>Inicio</h1>
        <div class="user">
            👤 <?php echo $_SESSION["usuario"]; ?>
        </div>
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
            <h3>Últimas atividades</h3>
            <p>Nenhuma ordem cadastrada ainda.</p>
        </div>

    </div>

</div>

</body>
</html>