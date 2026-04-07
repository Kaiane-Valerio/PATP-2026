<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION["ordens"])) {
    $_SESSION["ordens"] = [];
}

if (isset($_POST["nova_os"])) {
    $titulo = htmlspecialchars($_POST["titulo"]);
    $descricao = htmlspecialchars($_POST["descricao"]);

    $nova = [
        "id" => uniqid(),
        "titulo" => $titulo,
        "descricao" => $descricao,
        "status" => "Aberto"
    ];

    $_SESSION["ordens"][] = $nova;
}

if (isset($_GET["status"], $_GET["id"])) {
    foreach ($_SESSION["ordens"] as &$o) {
        if ($o["id"] == $_GET["id"]) {
            $o["status"] = $_GET["status"];
        }
    }
}

if (isset($_GET["excluir"])) {
    foreach ($_SESSION["ordens"] as $key => $o) {
        if ($o["id"] == $_GET["excluir"]) {
            unset($_SESSION["ordens"][$key]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>OS Manager</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
body {
    font-family: "Poppins", sans-serif;
    margin: 0;
    display: flex;
    background: linear-gradient(135deg, #eef2ff, #f8fafc);
}

.sidebar {
    width: 240px;
    height: 100vh;
    background: linear-gradient(180deg, #0f172a, #1e293b);
    color: white;
    padding: 25px;
    position: fixed;
    backdrop-filter: blur(10px);
}

.sidebar h2 {
    margin-bottom: 30px;
    font-weight: 600;
}

.sidebar a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    color: #cbd5f5;
    text-decoration: none;
    border-radius: 10px;
    margin-bottom: 10px;
    transition: 0.3s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    transform: translateX(5px);
}

.main {
    margin-left: 240px;
    width: 100%;
}

.header {
    background: rgba(255,255,255,0.7);
    backdrop-filter: blur(10px);
    padding: 20px 30px;
    border-bottom: 1px solid #e5e7eb;
    
}

.header h2 {
    margin: 0;
    font-weight: 600;
}

.content {
    padding: 30px;
}

.card {
    background: rgba(255,255,255,0.8);
    backdrop-filter: blur(12px);
    padding: 10px;
    border-radius: 16px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-3px);
}

input, textarea {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    transition: 0.3s;
}

input:focus, textarea:focus {
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    background: linear-gradient(90deg, #0a038b);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    transform: scale(1.03);
    box-shadow: 0 5px 15px rgba(99,102,241,0.4);
}

table {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
    border-radius: 12px;
}

th {
    background: #0a038b;
    color: white;
    text-align: left;
    padding: 14px;
}

td {
    padding: 14px;
    border-bottom: 1px solid #e5e7eb;
}

tr:hover {
    background: #f1f5f9;
}

.status {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}

.aberto { background: #e5e7eb; color: #374151; }
.andamento { background: #fde68a; color: #92400e; }
.concluido { background: #bbf7d0; color: #065f46; }

.actions a {
    margin-right: 10px;
    font-size: 16px;
    transition: 0.2s;
}

.actions a:hover {
    transform: scale(1.2);
}

::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 10px;
}
</style>

<script>
function confirmarExclusao() {
    return confirm("Tem certeza que deseja excluir esta ordem?");
}
</script>

</head>
<body>

<div class="sidebar">
    <h2>🛠 Sistema de Ordem de Serviços 🛠</h2>
    <a href="#"><i class="fas fa-home"></i> Inicio</a>
    <a href="#"><i class="fas fa-list"></i> Ordens</a>
    <a href="inicial.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
</div>

<div class="main">

<div class="header">
    <h2>Ordens de Serviço</h2>
</div>

<div class="content">

<div class="card">
    <h3><i class="fas fa-plus"></i> Nova Ordem</h3>
    <form method="POST">
        <input type="text" name="titulo" placeholder="Título" required>
        <textarea name="descricao" placeholder="Descrição" required></textarea>
        <button name="nova_os">Criar Ordem</button>
    </form>
</div>

<div class="card">
<table>
<tr>
    <th>ID</th>
    <th>Título</th>
    <th>Status</th>
    <th>Ações</th>
</tr>

<?php foreach ($_SESSION["ordens"] as $o): ?>
<tr>
<td><?= $o["id"] ?></td>
<td><?= $o["titulo"] ?></td>

<td>
<?php 
$classe = "aberto";
if ($o["status"] == "Em andamento") $classe = "andamento";
if ($o["status"] == "Concluído") $classe = "concluido";
?>
<span class="status <?= $classe ?>">
    <?= $o["status"] ?>
</span>
</td>

<td class="actions">
    <a href="?id=<?= $o["id"] ?>&status=Em andamento" title="Iniciar">
        ▶️
    </a>

    <a href="?id=<?= $o["id"] ?>&status=Concluído" title="Concluir">
        ✅
    </a>

    <a href="?excluir=<?= $o["id"] ?>" onclick="return confirmarExclusao()" title="Excluir">
        🗑️
    </a>
</td>

</tr>
<?php endforeach; ?>

</table>
</div>

</div>
</div>

</body>
</html>