<?php
session_start();
require_once "../db/conexao.php";

if (!isset($_SESSION["cliente_id"])) {
    header("Location: loginCliente.php");
    exit();
}

$cliente_id = $_SESSION["cliente_id"];

// ================== CONTADORES ==================

$sqlCards = "
SELECT status, COUNT(*) as total
FROM chamado
WHERE cliente_id = :id
GROUP BY status
";

$stmtCards = $pdo->prepare($sqlCards);
$stmtCards->bindParam(":id", $cliente_id);
$stmtCards->execute();

$cards = $stmtCards->fetchAll(PDO::FETCH_ASSOC);


// valores padrão
$abertos = 0;
$andamento = 0;
$finalizados = 0;


// monta contadores
foreach ($cards as $card) {

    if ($card["status"] == "Aberto") {
        $abertos = $card["total"];
    }

    if ($card["status"] == "Em andamento") {
        $andamento = $card["total"];
    }

    if ($card["status"] == "Finalizado") {
        $finalizados = $card["total"];
    }
}

// ================== FILTRO ==================

$statusFiltro = $_GET["status"] ?? "";

// status permitidos
$statusPermitidos = [
    "Aberto",
    "Em andamento",
    "Finalizado"
];

// validação
if (!in_array($statusFiltro, $statusPermitidos)) {
    $statusFiltro = "";
}

// ================== BUSCAR CHAMADOS ==================
$stmt = $pdo->prepare("SELECT c.*, o.nome AS operador_nome
    FROM chamado c
    LEFT JOIN operadores o ON c.operador_id = o.id
    WHERE c.cliente_id = :id AND (:status = '' OR c.status = :status)
    ORDER BY c.data_criacao DESC");
$stmt->bindParam(":id", $cliente_id);
$stmt->bindParam(":status", $statusFiltro);
$stmt->execute();
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================== CRIAR CHAMADO ==================
if (isset($_POST["nova_os"])) {

    $titulo = trim($_POST["titulo"]);
    $descricao = trim($_POST["descricao"]);

    if ($titulo && $descricao) {

        $sql = "INSERT INTO chamado 
                (titulo, descricao, data_criacao, status, cliente_id)
                VALUES (:titulo, :descricao, NOW(), 'Aberto', :cliente_id)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":titulo" => $titulo,
            ":descricao" => $descricao,
            ":cliente_id" => $cliente_id
        ]);

        $_SESSION["success"] = "Chamado criado!";

        // 🔥 evita reenviar form ao atualizar
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Chamados</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            background: #eef2f7;
            color: #1e293b;
        }

        html,
        body {
            overflow-x: hidden;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #e2e8f0;
        }

        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 999px;
        }

        .card,
        .chamado,
        .detail-card {
            transition:
                transform .25s ease,
                box-shadow .25s ease,
                border-color .25s ease;
        }

        h1,
        h2,
        h3,
        p,
        span,
        div {
            overflow-wrap: break-word;
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
            transition: transform 0.2s ease;
        }

        /* HEADER MESMO PADRÃO LOGIN */
        .header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px clamp(16px, 4vw, 35px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .header h1 {
            font-size: clamp(22px, 5vw, 34px);
            font-weight: 700;
            line-height: 1.2;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 14px;
        }

        /* Estilização do link de perfil do cliente */
        .profile-link {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .profile-link:hover {
            opacity: 0.85;
        }

        .profile-link:hover .avatar {
            transform: scale(1.05);
        }

        .user a.btn-sair {
            text-decoration: none;
            color: white;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            transition: .25s;
        }

        .user a.btn-sair:hover {
            background: rgba(255, 255, 255, .22);
        }


        /* CONTAINER */
        .container {
            width: 100%;
            max-width: 1600px;
            margin-inline: auto;
            padding: clamp(16px, 3vw, 32px) clamp(14px, 3vw, 24px) 120px;
        }


        /* RESUMO */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
            gap: clamp(14px, 2vw, 24px);
            margin-bottom: 34px;
        }

        .card {
            background: #fff;
            padding: 26px;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .06);
        }

        .card h3 {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .card p {
            font-size: 34px;
            font-weight: 700;
            color: #1e3c72;
        }


        /* LISTA */
        .lista {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 340px), 1fr));
            gap: 22px;
        }


        /* CHAMADO CARD */
        .chamado {
            position: relative;
            background: #fff;
            padding: 24px;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .06);
            transition: .25s;
            cursor: pointer;
        }

        .chamado::after {
            content: "Ver detalhes";
            position: absolute;
            right: 20px;
            bottom: 20px;
            font-size: 12px;
            opacity: 0;
            transition: .2s;
        }

        .chamado:hover {
            transform: translateY(-6px) scale(1.01);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
        }

        .chamado:hover::after {
            opacity: .6;
        }

        .chamado h3 {
            font-size: 19px;
            margin-bottom: 14px;
        }

        .chamado p {
            color: #475569;
            line-height: 1.5;
            margin-bottom: 18px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* borda status */
        .chamado.status-aberto {
            border-left: 5px solid #ef4444;
        }

        .chamado.status-andamento {
            border-left: 5px solid #f59e0b;
        }

        .chamado.status-finalizado {
            border-left: 5px solid #22c55e;
        }


        /* STATUS CHIPS */
        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .status.aberto {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status.andamento {
            background: #fef3c7;
            color: #92400e;
        }

        .status.finalizado {
            background: #dcfce7;
            color: #166534;
        }


        /* DATA */
        .data {
            font-size: 13px;
            color: #64748b;
            margin-top: 6px;
        }


        /* BOTÃO PADRÃO LOGIN */
        .bottom-btn {
            position: fixed;
            left: 50%;
            bottom: max(20px, env(safe-area-inset-bottom));
            transform: translateX(-50%);
            width: min(92%, 420px);
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #2a5298, #1e3c72);
            color: white;
            padding: 16px 24px;
            border: none;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 12px 30px rgba(42, 82, 152, .30);
            transition: .25s;
            z-index: 999;
        }

        .bottom-btn:hover {
            transform: translateX(-50%) scale(1.03);
        }

        .bottom-btn:active {
            transform: translateX(-50%) scale(.97);
        }


        /* MODAL */
        .modal {
            position: fixed;
            inset: 0;
            padding: 20px;
            display: none;
            justify-content: center;
            align-items: center;
            overflow-y: auto;
            background: rgba(15, 23, 42, .45);
            backdrop-filter: blur(4px);
            z-index: 9999;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            box-shadow: 0 18px 45px rgba(0, 0, 0, .15);
            width: min(100%, 900px);
            padding: 35px;
            border-radius: 18px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
            border-radius: 24px;
            animation: modalShow .25s ease;
        }

        .modal-content h2 {
            font-size: 28px;
            margin-bottom: 18px;
            color: #1e293b;
        }

        .close {
            float: right;
            cursor: pointer;
            font-weight: bold;
            color: #64748b;
        }


        /* INPUTS MESMO PADRÃO LOGIN */
        .modal-content input,
        .modal-content textarea {
            width: 100%;
            padding: 14px;
            margin-bottom: 14px;
            border: 1px solid #d6dbe3;
            border-radius: 10px;
            font-size: 15px;
        }

        .modal-content input:focus,
        .modal-content textarea:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, .12);
        }

        .modal-content textarea {
            min-height: 120px;
            resize: vertical;
        }


        .btn-submit {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #2a5298;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: .25s;
        }

        .mbtn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(42, 82, 152, .20);
        }

        /* EMPTY STATE */
        .empty-state {
            background: #fff;
            padding: 50px 30px;
            text-align: center;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .06);
        }

        .empty-state h2 {
            margin-bottom: 12px;
        }

        .btn-empty {
            margin-top: 18px;
            background: #2a5298;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px 20px;
            cursor: pointer;
        }


        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(.94);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }


        @media(max-width:768px) {
            .header {
                padding: 22px;
                flex-direction: column;
                gap: 12px;
            }

            .container {
                padding: 24px 16px 120px;
            }

            .cards,
            .lista {
                gap: 18px;
            }
        }

        .operador {
            font-size: 13px;
            margin-top: 10px;
            color: #334155;
        }

        .operador.vazio {
            color: #94a3b8;
            font-style: italic;
        }

        .card-link {
            text-decoration: none;
            color: inherit;
        }

        .card {
            cursor: pointer;
            transition: .25s;
            border: 2px solid transparent;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card.active {
            border-color: #2a5298;
            background: #eff6ff;
        }

        /* =========================================
           MODAL DETALHES PROFISSIONAL
        ========================================= */

        .modal-chamado {
            width: min(100%, 900px);
            max-height: calc(100vh - 40px);
            padding: 0 !important;
            overflow-y: auto;
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 25px 60px rgba(15, 23, 42, .18);
            animation: modalShow .25s ease;
        }


        /* HEADER */
        .modal-chamado-header {
            padding: 28px 32px;
            background: linear-gradient(135deg, rgba(30, 60, 114, 1), rgba(42, 82, 152, 1));
            color: white;
            position: relative;
        }

        .modal-header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .modal-mini-label {
            font-size: 13px;
            opacity: .75;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .modal-chamado-header h2 {
            font-size: 30px;
            font-weight: 700;
            color: white;
            line-height: 1.2;
            margin: 0;
        }


        /* BOTÃO FECHAR */
        .close-btn {
            background: transparent;
            float: right;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }


        /* BADGES */
        .modal-badges {
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 22px;
        }

        .badge-soft {
            float: right;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            font-size: 13px;
            backdrop-filter: blur(10px);
        }


        /* BODY */
        .modal-chamado-body {
            padding: 30px;
        }


        /* GRID */
        .detalhes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }


        /* CARDS */
        .detail-card {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 20px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: .25s;
        }

        .detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(15, 23, 42, .06);
        }

        .detail-icon {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: #e0e7ff;
            font-size: 20px;
            flex-shrink: 0;
        }

        .detail-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
        }


        /* SECTIONS */
        .section-box {
            margin-top: 24px;
            padding: 24px;
            border-radius: 20px;
            background: #fff;
            border: 1px solid #e2e8f0;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 18px;
        }


        /* DESCRIÇÃO */
        .descricao-box {
            line-height: 1.8;
            color: #475569;
            white-space: pre-line;
            font-size: 15px;
        }


        /* TIMELINE */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .timeline-item {
            display: flex;
            gap: 14px;
        }

        .timeline-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            margin-top: 5px;
            background: #22c55e;
            flex-shrink: 0;
        }

        .timeline-dot.warning {
            background: #f59e0b;
        }

        .timeline-content {
            color: #334155;
            line-height: 1.6;
        }


        /* STATUS MODAL */
        .modal-badges #detStatus {
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }


        /* RESPONSIVO */
        @media(max-width:768px) {
            .modal-chamado {
                width: calc(100% - 20px);
                max-height: 90vh;
                overflow-y: auto;
            }

            .modal-chamado-header {
                padding: 24px;
            }

            .modal-chamado-body {
                padding: 22px;
            }

            .modal-chamado-header h2 {
                font-size: 24px;
            }

            .modal-header-top {
                align-items: flex-start;
            }
        }


        /* ANIMAÇÃO */
        @keyframes modalShow {
            from {
                opacity: 0;
                transform: translateY(10px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>

</head>

<body>

    <div class="header">
        <h1><i class="bi bi-inboxes-fill"></i> Minhas Ordens</h1>
        <div class="user">
            <?php
            $nome = $_SESSION["cliente"];
            $primeiraLetra = strtoupper(substr($nome, 0, 1));

            if ($nome === "Kauê Gabriel Magarinos") {
                $primeiraLetra = "🧑‍💻";
            }
            ?>

            <a href="editarPerfilCliente.php" class="profile-link" title="Editar meu perfil">
                <div class="avatar"><?php echo $primeiraLetra; ?></div>
                <span><?php echo htmlspecialchars($nome); ?></span>
            </a>
            
            |
            <a href="logout.php" class="btn-sair">Sair</a>
        </div>
    </div>

    <div class="container">

        <button class="bottom-btn" onclick="abrirModal()">
            + Abrir chamado
        </button>

        <div class="cards">
            <a href="<?php echo $statusFiltro == 'Aberto' ? '?' : '?status=Aberto'; ?>" class="card-link">
                <div class="card <?php echo $statusFiltro == 'Aberto' ? 'active' : ''; ?>">
                    <h3>Abertos</h3>
                    <p><?php echo $abertos; ?></p>
                </div>
            </a>

            <a href="<?php echo $statusFiltro == 'Em andamento' ? '?' : '?status=Em%20andamento'; ?>" class="card-link">
                <div class="card <?php echo $statusFiltro == 'Em andamento' ? 'active' : ''; ?>">
                    <h3>Em andamento</h3>
                    <p><?php echo $andamento; ?></p>
                </div>
            </a>

            <a href="<?php echo $statusFiltro == 'Finalizado' ? '?' : '?status=Finalizado'; ?>" class="card-link">
                <div class="card <?php echo $statusFiltro == 'Finalizado' ? 'active' : ''; ?>">
                    <h3>Finalizados</h3>
                    <p><?php echo $finalizados; ?></p>
                </div>
            </a>
        </div>

        <div class="lista">

            <?php if (count($chamados) > 0): ?>
                <?php foreach ($chamados as $c): ?>

                    <?php
                    $mapa = [
                        "Aberto" => "aberto",
                        "Em andamento" => "andamento",
                        "Finalizado" => "finalizado"
                    ];

                    $classe = $mapa[$c["status"]] ?? "";
                    ?>

                    <div
                        class="chamado status-<?php echo $classe; ?>"
                        onclick='abrirDetalhes(
                            "<?php echo htmlspecialchars($c["titulo"], ENT_QUOTES); ?>",
                            "<?php echo htmlspecialchars($c["descricao"], ENT_QUOTES); ?>",
                            "<?php echo htmlspecialchars($c["status"], ENT_QUOTES); ?>",
                            "<?php echo htmlspecialchars($c["operador_nome"] ?? "Não atribuído", ENT_QUOTES); ?>",
                            "<?php echo date("d/m/Y H:i", strtotime($c["data_criacao"])); ?>"
                        )'>
                        <h3><?php echo htmlspecialchars($c["titulo"]); ?></h3>

                        <p>
                            <?php
                            $desc = htmlspecialchars($c["descricao"]);
                            echo strlen($desc) > 100 ? substr($desc, 0, 100) . "..." : $desc; ?>
                        </p>

                        <?php if (!empty($c["operador_nome"])): ?>
                            <div class="operador">
                                👨‍🔧 Em atendimento por: <?php echo htmlspecialchars($c["operador_nome"]); ?>
                            </div>
                        <?php else: ?>
                            <div class="operador vazio">
                                ⏳ Aguardando operador
                            </div>
                        <?php endif; ?>

                        <span class="status <?php echo $classe; ?>">
                            <?php echo $c["status"]; ?>
                        </span>


                        <div class="data">
                            Criado em: <?php echo date("d/m/Y", strtotime($c["data_criacao"])); ?>
                        </div>

                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h2>📭 Você ainda não abriu nenhum chamado</h2>
                    <p>Clique no botão abaixo para criar seu primeiro chamado</p>

                    <button class="btn-empty" onclick="abrirModal()">
                        + Abrir meu primeiro chamado
                    </button>
                </div>
            <?php endif; ?>

        </div>

    </div>

    <div class="modal" id="modal">
        <div class="modal-content">
            <span class="close" onclick="fecharModal()">X</span>
            <h2>Novo Chamado</h2>

            <form method="POST">
                <input type="hidden" name="nova_os" value="1">

                <input type="text" name="titulo" placeholder="Título" required>
                <textarea name="descricao" placeholder="Descrição" required></textarea>

                <button type="submit" class="btn-submit">Enviar</button>
            </form>
        </div>
    </div>

    <div class="modal" id="modalDetalhes">
        <div class="modal-content modal-chamado">

            <div class="modal-chamado-header">
                <div class="modal-header-top">
                    <div>
                        <div class="modal-mini-label">Ordem do Serviço</div>
                        <h2 id="detTitulo">Carregando...</h2>
                    </div>
                    <button class="close-btn" onclick="fecharDetalhes()">✕</button>
                </div>

                <div class="modal-badges">
                    <span class="status" id="detStatus"></span>
                    <span class="badge-soft">
                        <i class="bi bi-calendar3"></i>
                        <span id="detData"></span>
                    </span>
                </div>
            </div>

            <div class="modal-chamado-body">
                <div class="detalhes-grid">
                    <div class="detail-card">
                        <div class="detail-icon">👨‍🔧</div>
                        <div>
                            <div class="detail-label">Técnico responsável</div>
                            <div class="detail-value" id="detOperador">Não atribuído</div>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-icon">📌</div>
                        <div>
                            <div class="detail-label">Situação atual</div>
                            <div class="detail-value" id="detStatusValue">Em acompanhamento</div>
                        </div>
                    </div>
                </div>

                <div class="section-box">
                    <div class="section-title">📝 Descrição do chamado</div>
                    <div class="descricao-box" id="detDescricao"></div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function abrirModal() {
            document.getElementById("modal").classList.add("active");
            document.body.style.overflow = "hidden";
        }

        function fecharModal() {
            document.getElementById("modal").classList.remove("active");
            document.body.style.overflow = "auto";
        }

        function abrirDetalhes(titulo, descricao, status, operador, data) {
            const detStatus = document.getElementById("detStatus");
            detStatus.innerText = status;
            
            // Remove as classes anteriores do status do modal para evitar conflito de cor
            detStatus.className = "status"; 
            if(status === "Aberto") detStatus.classList.add("aberto");
            if(status === "Em andamento") detStatus.classList.add("andamento");
            if(status === "Finalizado") detStatus.classList.add("finalizado");

            document.getElementById("detTitulo").innerText = titulo;
            document.getElementById("detDescricao").innerText = descricao;
            document.getElementById("detOperador").innerText = operador;
            document.getElementById("detStatusValue").innerText = status || "Aguardando atualização";
            document.getElementById("detData").innerText = data;
            document.getElementById("modalDetalhes").classList.add("active");
            document.body.style.overflow = "hidden";
        }

        function fecharDetalhes() {
            document.getElementById("modalDetalhes").classList.remove("active");
            document.body.style.overflow = "auto";
        }

        // 🔥 Correção unificada para fechar qualquer modal ao clicar fora
        window.onclick = function(e) {
            const modalNovo = document.getElementById("modal");
            const modalDet = document.getElementById("modalDetalhes");
            
            if (e.target === modalNovo) {
                fecharModal();
            }
            if (e.target === modalDet) {
                fecharDetalhes();
            }
        }

        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                fecharModal();
                fecharDetalhes();
            }
        });
    </script>

</body>
</html>