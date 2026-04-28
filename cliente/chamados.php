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
            background: #eef2f7;
            color: #1e293b;
        }

        /* HEADER MESMO PADRÃO LOGIN */
        .header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;

            padding: 28px 35px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .header h1 {
            font-size: 30px;
            font-weight: 700;
        }

        .user {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 14px;
        }

        .user a {
            text-decoration: none;
            color: white;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            transition: .25s;
        }

        .user a:hover {
            background: rgba(255, 255, 255, .22);
        }


        /* CONTAINER */
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 38px 22px 120px;
        }


        /* RESUMO */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 22px;
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }


        /* CHAMADO CARD */
        .chamado {
            background: #fff;
            padding: 24px;
            border-radius: 18px;

            box-shadow: 0 12px 30px rgba(0, 0, 0, .06);

            transition: .25s;
            cursor: pointer;
        }

        .chamado:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
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
            bottom: 22px;

            transform: translateX(-50%);

            background: #2a5298;
            color: white;

            padding: 15px 30px;

            border: none;
            border-radius: 999px;

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

            display: none;
            justify-content: center;
            align-items: center;

            background: rgba(15, 23, 42, .45);
            backdrop-filter: blur(4px);
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;

            width: 100%;
            max-width: 440px;

            padding: 35px;

            border-radius: 18px;

            box-shadow: 0 18px 45px rgba(0, 0, 0, .15);

            animation: fadeIn .22s ease;
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


        .modal-content button {
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

        .modal-content button:hover {
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
    </style>

</head>

<body>

    <div class="header">
        <h1>📋 Meus Chamados</h1>
        <div class="user">
            👤 <?php echo $_SESSION["cliente"]; ?> |
            <a href="loginCLiente.php">Sair</a>
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