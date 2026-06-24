<?php
session_start();
require_once "db/conexao.php";

// Verifica login
if (!isset($_SESSION["operador_id"])) {
    header("Location: login.php");
    exit();
}

$operadorId = $_SESSION["operador_id"];

if (isset($_SESSION["operador_nome"])) {
    $nomeUsuario = $_SESSION["operador_nome"];
} elseif (isset($_SESSION["usuario"])) {
    $nomeUsuario = $_SESSION["usuario"];
} else {
    $nomeUsuario = "Operador";
}

$primeiraLetra = strtoupper(substr($nomeUsuario, 0, 1));

if ($nomeUsuario === "Kauê Gabriel Magarinos") {
    $primeiraLetra = "🧑‍💻";
}

// ================== PEGAR CHAMADO ==================
if (isset($_GET["pegar"])) {

    $id = (int) $_GET["pegar"];

    $sql = "UPDATE chamado 
            SET operador_id = :operador_id 
            WHERE id = :id AND operador_id IS NULL";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":operador_id" => $operadorId,
        ":id" => $id
    ]);

    if ($stmt->rowCount() === 0) {
        // já foi pego por alguém
    }
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
        ":operador_id" => $operadorId
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
        ":operador_id" => $operadorId
    ]);
    if ($stmt->rowCount() === 0) {
        // não deletou nada
    }
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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --bg: #f5f7fb;
            --sidebar: #111827;
            --sidebar-hover: #1f2937;
            --card: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --muted: #6b7280;
            --primary: #2563eb;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --radius: 18px;
        }

        * {
            margin: 0;
            padding: 0;
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
            position: fixed;
            background: #111827;
            color: #fff;
            border-right: 1px solid rgba(255, 255, 255, 0.04);
            padding: 24px 18px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar a {
            display: flex;
            padding: 10px;
            color: #9ca3af;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #1f293781;
            color: #fff;
            border-radius: 8px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 40px;
            padding: 0 10px;
        }

        .logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .logo-text h2 {
            color: white;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .logo-text span {
            color: #9ca3af;
            font-size: 12px;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 14px;
            color: #d1d5db;
            padding: 13px 14px;
            border-radius: 14px;
            transition: 0.2s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar-link:hover {
            background: #1f293781;
            color: white;
        }

        .sidebar-link i {
            font-size: 18px;
        }

        /* MAIN */
        .main {
            margin-left: 220px;
        }

        /* =======================
           HEADER
        ======================== */

        .topbar {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 18px 28px;
            position: sticky;
            top: 0;
            z-index: 900;
        }

        .topbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .welcome small {
            color: var(--muted);
            font-size: 13px;
        }

        .welcome h1 {
            font-size: 24px;
            margin-top: 4px;
            font-weight: 700;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .icon-btn {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: white;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text);
            font-size: 18px;
            transition: 0.2s ease;
        }

        .icon-btn:hover {
            transform: translateY(-2px);
            background: #f9fafb;
        }

        .user-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 8px 12px;
            text-decoration: none;
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .user-info strong {
            display: block;
            font-size: 14px;
        }

        .user-info span {
            color: var(--muted);
            font-size: 12px;
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

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
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

        /* ACTIONS */
        .actions a {
            text-decoration: none;
            margin-right: 10px;
            color: #374151;
        }

        .actions a:hover {
            color: #2563eb;
        }

        /* OVERLAY */
        .os-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .55);
            backdrop-filter: blur(6px);

            display: flex;
            align-items: center;
            justify-content: center;

            z-index: 9999;
            padding: 20px;
        }

        /* MODAL CONTAINER */
        .os-modal {
            width: min(100%, 900px);
            max-height: calc(100vh - 40px);
            overflow-y: auto;

            border-radius: 24px;
            background: #fff;
            box-shadow: 0 25px 60px rgba(15, 23, 42, .18);

            animation: modalShow .25s ease;
        }

        /* HEADER */
        .os-modal-header {
            padding: 28px 32px;
            background: #1f2937;
            color: #fff;

            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .os-modal-header small {
            font-size: 13px;
            opacity: .75;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 6px;
        }

        .os-modal-header h2 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
        }

        /* FECHAR */
        .os-close-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            opacity: .85;
            transition: .2s;
        }

        .os-close-btn:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        /* BADGES */
        .os-modal-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;

            padding: 18px 32px 0 32px;
        }

        .os-modal-badges .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            padding: 10px 14px;
            border-radius: 999px;

            background: #f1f5f9;
            color: #0f172a;

            font-size: 13px;
            font-weight: 500;
        }

        /* STATUS BADGE (DESTAQUE) */

        /* BODY */
        .os-modal-body {
            padding: 30px;
        }

        /* SEÇÃO */
        .os-section {
            padding: 24px;
            border-radius: 20px;

            border: 1px solid #e2e8f0;
            background: #fff;
        }

        .os-section h4 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 18px;
            color: #0f172a;
        }

        .os-section p {
            font-size: 15px;
            line-height: 1.8;
            color: #475569;
            white-space: pre-line;
        }

        .status-badge.locked {
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .action-icons a {
            text-decoration: none;
        }

        .icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: 34px;
            height: 34px;

            border-radius: 10px;

            color: #334155;
            background: #f1f5f9;

            margin-right: 6px;

            transition: .2s;
        }

        .icon-btn:hover {
            background: #e2e8f0;
            transform: translateY(-1px);
        }

        .icon-btn i {
            font-size: 16px;
        }

        /* perigo (excluir) */
        .icon-btn.danger {
            color: #dc2626;
            background: #fee2e2;
        }

        .icon-btn.danger:hover {
            background: #fecaca;
        }

        /* ANIMAÇÃO */
        @keyframes modalShow {
            from {
                transform: translateY(10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* cabeçalho */
        table th {
            text-align: left;
            padding: 12px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            color: #0f172a;
        }

        /* células */
        table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        /* ZEBRA (LINHAS ALTERNADAS) */
        table tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        /* hover opcional */
        table tbody tr:hover {
            background: #eef2ff;
            transition: .2s;
        }
    </style>

    <script>
        function confirmarExclusao() {
            return confirm("Tem certeza que deseja excluir?");
        }
    </script>

</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <!-- Logo -->
        <div class="logo">
            <div class="logo-icon">
                <i class="bi bi-grid-1x2-fill"></i>
            </div>

            <div class="logo-text">
                <h2>OS Manager</h2>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="inicial.php" class="sidebar-link active">
                <i class="bi bi-house-door"></i>
                Inicio
            </a>

            <a href="clientes.php" class="sidebar-link">
                <i class="bi bi-people"></i>
                Clientes
            </a>
        </div>
    </aside>

    <div class="main">

        <!-- TOPBAR -->
        <header class="topbar">

            <div class="topbar-content">

                <div class="d-flex align-items-center gap-3">
                    <div class="welcome">
                        <small>Painel de Gestão de Ordens de Serviço</small>
                        <h1>Olá, <?php echo explode(" ", $nomeUsuario)[0]; ?> 👋</h1>
                    </div>

                </div>

                <div class="topbar-actions">

                    <a href="editar_perfil.php" class="user-box">
                        <div class="avatar">
                            <?php echo $primeiraLetra; ?>
                        </div>

                        <div class="user-info">
                            <strong><?php echo $nomeUsuario; ?></strong>
                            <span>Operador</span>
                        </div>
                    </a>

                </div>

            </div>

        </header>

        <div class="content">

            <!-- DETALHES -->
            <?php if ($detalhe): ?>

                <div id="modalOS" class="os-modal-overlay" onclick="fecharModal(event)">

                    <div class="os-modal">

                        <!-- HEADER -->
                        <div class="os-modal-header">

                            <div>
                                <small>Ordem de Serviço #<?= $detalhe["id"] ?></small>
                                <h2><?= htmlspecialchars($detalhe["titulo"]) ?></h2>
                            </div>

                            <button class="os-close-btn" onclick="fecharModal()">
                                ✕
                            </button>

                        </div>

                        <!-- BADGES -->
                        <div class="os-modal-badges">
                            <?php
                            $statusClass = '';

                            if ($detalhe["status"] === "Aberto") {
                                $statusClass = "aberto";
                            } elseif ($detalhe["status"] === "Em andamento") {
                                $statusClass = "andamento";
                            } elseif ($detalhe["status"] === "Finalizado") {
                                $statusClass = "concluido";
                            }
                            ?>

                            <span class="status <?= $statusClass ?>">
                                <?= htmlspecialchars($detalhe["status"]) ?>
                            </span>

                            <span class="badge">
                                👤 <?= htmlspecialchars($detalhe["nome"]) ?>
                            </span>
                        </div>

                        <!-- BODY -->
                        <div class="os-modal-body">

                            <div class="os-section">
                                <h4>📝 Descrição</h4>
                                <p><?= nl2br(htmlspecialchars($detalhe["descricao"])) ?></p>
                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

            <!-- TABELA -->
            <div class="card">
                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>
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

                                <td class="action-icons">

                                    <?php if (empty($o["operador_id"])): ?>

                                        <!-- PEGAR -->
                                        <a href="?pegar=<?= $o["id"] ?>" class="icon-btn" title="Pegar OS">
                                            <i class="bi bi-hand-index-thumb-fill"></i>
                                        </a>

                                        <!-- DETALHES -->
                                        <a href="?ver=<?= $o["id"] ?>" class="icon-btn" title="Ver detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                    <?php elseif ($o["operador_id"] == $operadorId): ?>

                                        <!-- DETALHES -->
                                        <a href="?ver=<?= $o["id"] ?>" class="icon-btn" title="Ver detalhes">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <!-- INICIAR -->
                                        <a href="?id=<?= $o["id"] ?>&status=Em andamento" class="icon-btn" title="Iniciar">
                                            <i class="bi bi-play-circle-fill"></i>
                                        </a>

                                        <!-- FINALIZAR -->
                                        <a href="?id=<?= $o["id"] ?>&status=Finalizado" class="icon-btn" title="Finalizar">
                                            <i class="bi bi-check-circle-fill"></i>
                                        </a>

                                        <!-- EXCLUIR -->
                                        <a href="?excluir=<?= $o["id"] ?>" class="icon-btn danger"
                                            onclick="return confirmarExclusao()" title="Excluir">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>

                                    <?php else: ?>

                                        <!-- OUTRO OPERADOR -->
                                        <span class="status-badge locked">Em atendimento</span>

                                    <?php endif; ?>

                                </td>

                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        function fecharModal(e) {
            if (!e || e.target.id === "modalOS") {
                document.getElementById("modalOS").style.display = "none";
            }
        }
    </script>
</body>

</html>