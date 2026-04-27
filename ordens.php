<?php
session_start();
require_once "db/conexao.php";

// Verifica login
if (!isset($_SESSION["operador_id"])) {
    header("Location: login.php");
    exit();
}

$operador_id = $_SESSION["operador_id"]; // ajuste se tiver operador separado

// ================== CRIAR ORDEM ==================
if (isset($_POST["nova_os"])) {

    $titulo = trim($_POST["titulo"]);
    $descricao = trim($_POST["descricao"]);

    if ($titulo && $descricao) {

        $sql = "INSERT INTO chamado 
                (chamado, motivo, data_criacao, status, cliente_id)
                VALUES (:titulo, :descricao, NOW(), 'Aberto', :cliente_id)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":titulo" => $titulo,
            ":descricao" => $descricao,
            ":cliente_id" => $cliente_id
        ]);
    }
}

// ================== PEGAR CHAMADO ==================
if (isset($_GET["pegar"])) {

    $id = (int) $_GET["pegar"];

    $sql = "UPDATE chamado 
            SET operador_id = :operador_id 
            WHERE id = :id AND operador_id IS NULL";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":operador_id" => $operador_id,
        ":id" => $id
    ]);
}

// ================== ALTERAR STATUS ==================
if (isset($_GET["status"], $_GET["id"])) {

    $status = $_GET["status"];
    $id = (int) $_GET["id"];

    $sql = "UPDATE chamado 
            SET status = :status 
            WHERE id = :id 
            AND (operador_id IS NULL OR operador_id = :operador_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":status" => $status,
        ":id" => $id,
        ":operador_id" => $operador_id
    ]);
}

// ================== EXCLUIR ==================
if (isset($_GET["excluir"])) {

    $id = (int) $_GET["excluir"];

    $sql = "DELETE FROM chamado 
            WHERE id = :id 
            AND (operador_id IS NULL OR operador_id = :operador_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id" => $id,
        ":operador_id" => $operador_id
    ]);
}

// ================== DETALHES ==================
$detalhe = null;

if (isset($_GET["ver"])) {

    $id = (int) $_GET["ver"];

    $sql = "SELECT c.*, cli.nome 
            FROM chamado c
            JOIN clientes cli ON c.cliente_id = cli.id
            WHERE c.id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id" => $id
    ]);

    $detalhe = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ================== LISTAR TODOS ==================
$sql = "SELECT c.*, cli.nome 
        FROM chamado c
        JOIN clientes cli ON c.cliente_id = cli.id
        ORDER BY c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$ordens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
<title>OS Manager</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
* { box-sizing: border-box; }

body {
    font-family: "Inter", sans-serif;
    margin: 0;
    background: #f5f7fb;
    color: #1f2937;
}

/* SIDEBAR */
.sidebar {
    width: 220px;
    height: 100vh;
    background: #111827;
    color: #fff;
    padding: 25px 20px;
    position: fixed;
}

.sidebar a {
    display: flex;
    padding: 10px;
    color: #9ca3af;
    text-decoration: none;
}

.sidebar a:hover {
    background: #1f2937;
    color: #fff;
}

/* MAIN */
.main {
    margin-left: 220px;
}

/* HEADER */
.header {
    background: #fff;
    padding: 20px 30px;
    border-bottom: 1px solid #e5e7eb;
}

/* CONTENT */
.content {
    padding: 30px;
}

/* CARD */
.card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 25px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
}

/* STATUS */
.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
}

.aberto { background: #e5e7eb; }
.andamento { background: #fef3c7; }
.concluido { background: #d1fae5; }

/* ACTIONS */
.actions a {
    text-decoration: none;
    margin-right: 10px;
    color: #374151;
}

.actions a:hover {
    color: #2563eb;
}
</style>

<script>
function confirmarExclusao(){
    return confirm("Tem certeza que deseja excluir?");
}
</script>

</head>

<body>

<div class="sidebar">
    <h2>OS Manager</h2>
    <a href="#">Início</a>
    <a href="#">Ordens</a>
</div>

<div class="main">

<div class="header">
    <h2>Ordens de Serviço</h2>
</div>

<div class="content">

<!-- DETALHES -->
<?php if ($detalhe): ?>
<div class="card">
    <h3>Chamado #<?= $detalhe["id"] ?></h3>

    <p><strong>Título:</strong> <?= htmlspecialchars($detalhe["titulo"]) ?></p>
    <p><strong>Cliente:</strong> <?= htmlspecialchars($detalhe["nome"]) ?></p>

    <p><strong>Descrição:</strong><br>
        <?= nl2br(htmlspecialchars($detalhe["descricao"])) ?>
    </p>

    <p><strong>Status:</strong> <?= $detalhe["status"] ?></p>

    <a href="?">Fechar</a>
</div>
<?php endif; ?>

<!-- TABELA -->
<div class="card">
<table>

<tr>
<th>ID</th>
<th>Título</th>
<th>Cliente</th>
<th>Status</th>
<th>Ações</th>
</tr>

<?php foreach ($ordens as $o): ?>
<tr>

<td><?= $o["id"] ?></td>
<td><?= htmlspecialchars($o["titulo"]) ?></td>
<td><?= htmlspecialchars($o["nome"]) ?></td>

<td>
<?php
$classe = "aberto";
if ($o["status"] == "Em andamento") $classe = "andamento";
if ($o["status"] == "Finalizado") $classe = "concluido";
?>
<span class="status <?= $classe ?>">
<?= $o["status"] ?>
</span>
</td>

<td class="actions">

<?php if (empty($o["operador_id"])): ?>
<a href="?pegar=<?= $o["id"] ?>"><i class="fas fa-hand-paper"></i></a>
<?php endif; ?>

<?php if ($o["operador_id"] == $operador_id || empty($o["operador_id"])): ?>

<a href="?ver=<?= $o["id"] ?>"><i class="fas fa-eye"></i></a>
<a href="?id=<?= $o["id"] ?>&status=Em andamento"><i class="fas fa-play"></i></a>
<a href="?id=<?= $o["id"] ?>&status=Finalizado"><i class="fas fa-check"></i></a>
<a href="?excluir=<?= $o["id"] ?>" onclick="return confirmarExclusao()"><i class="fas fa-trash"></i></a>

<?php else: ?>

<span style="color:red;">Em atendimento</span>

<?php endif; ?>

</td>

</tr>
<?php endforeach; ?>

</table>
</div>

</div>
</div>

</body>
</html>