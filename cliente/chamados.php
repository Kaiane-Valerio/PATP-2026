<?php
session_start();
require_once "../db/conexao.php";

if (!isset($_SESSION["cliente_id"])) {
    header("Location: loginCliente.php");
    exit();
}

$cliente_id = $_SESSION["cliente_id"];

// ================== BUSCAR CHAMADOS ==================
$stmt = $pdo->prepare("SELECT * FROM chamado WHERE cliente_id = :id ORDER BY data_criacao DESC");
$stmt->bindParam(":id", $cliente_id);
$stmt->execute();
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CONTADORES
$abertos = 0;
$andamento = 0;
$finalizados = 0;

foreach ($chamados as $c) {
    if ($c["status"] == "Aberto") $abertos++;
    if ($c["status"] == "Em andamento") $andamento++;
    if ($c["status"] == "Finalizado") $finalizados++;
}

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
    <title>Meus Chamados</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            background: #f4f6f9;
        }

        /* HEADER */
        .header {
            background: #1e3c72;
            color: white;
            padding: 20px 30px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 20px;
        }

        .user {
            font-size: 14px;
        }

        /* CONTAINER */
        .container {
            max-width: 1200px;
            margin: 0 auto;

            padding: 30px 20px 120px 20px;
        }

        /* CARDS RESUMO */
        .cards {
            display: flex;
            gap: 20px;
            margin: 30px 0;
            flex-wrap: wrap;
        }

        .card {
            flex: 1;
            min-width: 180px;
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            font-size: 14px;
            color: #777;
        }

        .card p {
            font-size: 22px;
            font-weight: bold;
        }

        /* LISTA DE CHAMADOS */
        .lista {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 10px;
        }

        .chamado {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            cursor: pointer;
        }

        .chamado:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* borda lateral por status */
        .chamado h3 {
            margin-bottom: 12px;
        }

        .chamado p {
            color: #555;
            margin-bottom: 14px;
            line-height: 1.4;
        }

        /* STATUS */
        .status {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }

        .status.aberto {
            background: #dc3545;
            color: white;
        }

        .status.andamento {
            background: #ffc107;
            color: #333;
        }

        .status.finalizado {
            background: #28a745;
            color: white;
        }

        /* DATA */
        .data {
            font-size: 12px;
            color: #999;
            margin-top: 8px;
        }


        /* CARD ESTILO FEED */
        .chamado:hover {
            transform: translateY(-5px);
        }

        .chamado.status-aberto {
            border-left: 5px solid #dc3545;
        }

        .chamado.status-andamento {
            border-left: 5px solid #ffc107;
        }

        .chamado.status-finalizado {
            border-left: 5px solid #28a745;
        }

        /* DESCRIÇÃO LIMITADA */
        .chamado p {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* BOTÃO FLUTUANTE */
        .bottom-btn {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);

            background: #28a745;
            color: white;

            padding: 14px 28px;
            font-size: 16px;
            font-weight: bold;

            border: none;
            border-radius: 30px;

            cursor: pointer;

            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            transition: all 0.25s ease;

            z-index: 1000;
        }

        /* hover */
        .bottom-btn:hover {
            transform: translateX(-50%) scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        /* clique */
        .bottom-btn:active {
            transform: translateX(-50%) scale(0.95);
        }

        /* MODAL */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.25s ease;
        }

        .modal-content h2 {
            margin-bottom: 15px;
        }

        .modal-content input,
        .modal-content textarea {
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .modal-content button {
            width: 100%;
            padding: 10px;
            border: none;
            background: #28a745;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }

        .close {
            float: right;
            cursor: pointer;
            font-weight: bold;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* estado vazio */
        .empty-state {
            text-align: center;
            margin-top: 80px;
            padding: 20px;
            color: #555;
        }

        .empty-state h2 {
            margin-bottom: 10px;
        }

        .btn-empty {
            margin-top: 20px;
            padding: 12px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
        }

        .btn-empty:hover {
            transform: scale(1.05);
            background: #218838;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px 15px 120px 15px;
            }

            .cards {
                gap: 15px;
            }

            .lista {
                gap: 18px;
            }
        }
    </style>

</head>

<body>

    <div class="header">
        <h1>📋 Meus Chamados</h1>
        <div class="user">
            👤 <?php echo $_SESSION["cliente"]; ?> |
            <a href="logout.php" style="color: #fff;">Sair</a>
        </div>
    </div>

    <div class="container">

        <button class="bottom-btn" onclick="abrirModal()">
            + Abrir chamado
        </button>

        <!-- RESUMO -->
        <div class="cards">
            <div class="card">
                <h3>Abertos</h3>
                <p><?php echo $abertos; ?></p>
            </div>

            <div class="card">
                <h3>Em andamento</h3>
                <p><?php echo $andamento; ?></p>
            </div>

            <div class="card">
                <h3>Finalizados</h3>
                <p><?php echo $finalizados; ?></p>
            </div>
        </div>

        <!-- LISTA -->
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

                    <div class="chamado status-<?php echo $classe; ?>">
                        <h3><?php echo htmlspecialchars($c["titulo"]); ?></h3>

                        <p>
                            <?php echo substr(htmlspecialchars($c["descricao"]), 0, 100); ?>...
                        </p>

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

                <button type="submit">Enviar</button>
            </form>
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

        // fechar clicando fora
        window.onclick = function(e) {
            const modal = document.getElementById("modal");
            if (e.target === modal) {
                fecharModal();
            }
        }
    </script>

</body>

</html>