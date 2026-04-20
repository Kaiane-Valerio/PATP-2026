<?php
session_start();
require_once "db/conexao.php";

// Verifica login
if (!isset($_SESSION["cliente_id"])) {
    header("Location: cliente/loginCliente.php");
    exit();
}

$cliente_id = $_SESSION["cliente_id"];

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

// ================== ALTERAR STATUS ==================
if (isset($_GET["status"], $_GET["id"])) {

    $status = $_GET["status"];
    $id = (int) $_GET["id"];

    $sql = "UPDATE chamado 
            SET status = :status 
            WHERE id = :id AND cliente_id = :cliente_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":status" => $status,
        ":id" => $id,
        ":cliente_id" => $cliente_id
    ]);
}

// ================== EXCLUIR ==================
if (isset($_GET["excluir"])) {

    $id = (int) $_GET["excluir"];

    $sql = "DELETE FROM chamado 
            WHERE id = :id AND cliente_id = :cliente_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id" => $id,
        ":cliente_id" => $cliente_id
    ]);
}

// ================== BUSCAR ORDENS ==================
$sql = "SELECT c.*, cli.nome 
        FROM chamado c
        JOIN clientes cli ON c.cliente_id = cli.id
        WHERE c.cliente_id = :cliente_id
        ORDER BY c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([":cliente_id" => $cliente_id]);
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
        * {
            box-sizing: border-box;
        }

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
            z-index: 1000;
        }

        .sidebar h2 {
            font-size: 16px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            color: #9ca3af;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 8px;
        }

        .sidebar a:hover {
            background: #1f2937;
            color: #fff;
        }

        /* MAIN */
        .main {
            margin-left: 220px;
            width: calc(100% - 220px);
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
            border: 1px solid #e5e7eb;
        }

        /* FORM */
        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 12px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
        }

        button {
            margin-top: 15px;
            width: 100%;
            padding: 10px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            font-size: 13px;
            color: #6b7280;
        }

        /* STATUS */
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .aberto {
            background: #e5e7eb;
        }

        .andamento {
            background: #fef3c7;
        }

        .concluido {
            background: #d1fae5;
        }

        .actions a {
            margin-right: 10px;
            color: #374151;
        }

        .actions a:hover {
            color: #2563eb;
        }
    </style>

    <script>
        function confirmarExclusao() {
            return confirm("Tem certeza que deseja excluir?");
        }
    </script>

</head>

<body>

    <div class="sidebar">
        <h2><i class="fas fa-tools"></i> OS Manager</h2>
        <a href="#"><i class="fas fa-home"></i> Início</a>
        <a href="#"><i class="fas fa-list"></i> Ordens</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
    </div>

    <div class="main">

        <div class="header">
            <h2><i class="fas fa-clipboard-list"></i> Ordens de Serviço</h2>
        </div>

        <div class="content">

            <div class="card">
                <h3><i class="fas fa-plus"></i> Nova Ordem</h3>
                <form method="POST">
                    <input type="text" name="titulo" placeholder="Título" required>
                    <textarea name="descricao" placeholder="Descrição" required></textarea>
                    <button name="nova_os">
                        <i class="fas fa-plus"></i> Criar Ordem
                    </button>
                </form>
            </div>

            <div class="card">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>

                    <?php if (empty($ordens)): ?>
                        <tr>
                            <td colspan="6">Nenhuma ordem encontrada.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($ordens as $o): ?>
                        <tr>
                            <td><?= $o["id"] ?></td>
                            <td><?= htmlspecialchars($o["chamado"]) ?></td>
                            <td><?= htmlspecialchars($o["nome"]) ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($o["data_criacao"])) ?></td>

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
                                <a href="?id=<?= $o["id"] ?>&status=Em andamento"><i class="fas fa-play"></i></a>
                                <a href="?id=<?= $o["id"] ?>&status=Concluído"><i class="fas fa-check"></i></a>
                                <a href="?excluir=<?= $o["id"] ?>" onclick="return confirmarExclusao()"><i class="fas fa-trash"></i></a>
                            </td>

                        </tr>
                    <?php endforeach; ?>

                </table>
            </div>

        </div>
    </div>

</body>

</html>