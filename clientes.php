<?php
session_start();
require_once "db/conexao.php";

// BUSCAR CLIENTES
$sql = "SELECT * FROM clientes ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            background: #eef2f7;

            padding: 40px;
        }

        .box {
            background: white;

            width: 100%;
            max-width: 1000px;

            padding: 35px;

            border-radius: 18px;

            box-shadow: 0 12px 35px rgba(0, 0, 0, .08);

            overflow-x: auto;
        }

        .topo {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 25px;

            gap: 15px;
            flex-wrap: wrap;
        }

        .box h2 {
            font-size: 30px;
            margin-bottom: 5px;
            color: #1e293b;
        }

        .sub {
            color: #64748b;
            font-size: 14px;
        }

        .acoes-topo {
            display: flex;
            gap: 10px;
        }

        .novo {
            background: #16a34a;
            color: white;

            text-decoration: none;

            padding: 12px 18px;

            border-radius: 10px;

            font-weight: 600;

            transition: .2s;
        }

        .voltar {
            background: #64748b;
            color: white;

            text-decoration: none;

            padding: 12px 18px;

            border-radius: 10px;

            font-weight: 600;

            transition: .2s;
        }

        .novo:hover,
        .voltar:hover {
            opacity: .9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #2a5298;
            color: white;

            padding: 16px;

            text-align: left;
            font-size: 14px;
        }

        table td {
            padding: 16px;

            border-bottom: 1px solid #e2e8f0;

            font-size: 14px;
            color: #334155;
        }

        table tr:hover {
            background: #f8fafc;
        }

        .btn {
            padding: 8px 14px;

            border-radius: 8px;

            text-decoration: none;
            color: white;

            font-size: 13px;
            font-weight: 600;

            transition: .2s;

            display: inline-block;
        }

        .editar {
            background: #2563eb;
        }

        .excluir {
            background: #dc2626;
        }

        .btn:hover {
            opacity: .9;
        }

        @media(max-width:900px) {

            body {
                padding: 20px;
            }

            .box {
                padding: 20px;
            }

            .topo {
                flex-direction: column;
                align-items: flex-start;
            }

            .acoes-topo {
                width: 100%;
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

    <div class="box">

        <div class="topo">

            <div>
                <h2>Lista de Clientes</h2>

                <p class="sub">
                    Clientes cadastrados no sistema.
                </p>
            </div>

            <div class="acoes-topo">

                <a href="inicial.php" class="voltar">
                    ← Voltar
                </a>

                <a href="cliente/cadastroCliente.php" class="novo">
                    + Novo Cliente
                </a>

            </div>

        </div>

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                <?php if (count($clientes) > 0): ?>

                    <?php foreach ($clientes as $cliente): ?>

                        <tr>

                            <td><?= $cliente['id']; ?></td>

                            <td><?= htmlspecialchars($cliente['nome']); ?></td>

                            <td><?= htmlspecialchars($cliente['email']); ?></td>

                            <td>

                                <a
                                    href="editarCliente.php?id=<?= $cliente['id']; ?>"
                                    class="btn editar">
                                    Editar
                                </a>

                                <a
                                    href="excluirCliente.php?id=<?= $cliente['id']; ?>"
                                    class="btn excluir"
                                    onclick="return confirm('Deseja excluir este cliente?')">
                                    Excluir
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="4">
                            Nenhum cliente encontrado.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</body>

</html>
