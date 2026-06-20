<?php
session_start();
require_once "../db/conexao.php";

// Verifica se o cliente está logado
if (!isset($_SESSION["cliente_id"])) {
    header("Location: loginCliente.php");
    exit();
}

$cliente_id = $_SESSION["cliente_id"];

// 1. BUSCA OS DADOS ATUAIS DO CLIENTE NO BANCO
$query = $pdo->prepare("SELECT nome, email, telefone FROM clientes WHERE id = :id");
$query->bindParam(":id", $cliente_id);
$query->execute();
$cliente = $query->fetch(PDO::FETCH_ASSOC);

// Se não achar o usuário por algum motivo, puxa o que estiver na sessão
if (!$cliente) {
    $cliente = [
        "nome" => $_SESSION["cliente"] ?? "",
        "email" => "",
        "telefone" => ""
    ];
}

// 2. PROCESSA O FORMULÁRIO QUANDO FOR ENVIADO
$mensagem_sucesso = "";
$mensagem_erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["salvar_perfil"])) {
    $novo_nome = trim($_POST["nome"]);
    $novo_email = trim($_POST["email"]);
    $novo_telefone = trim($_POST["telefone"]);
    $nova_senha = $_POST["senha"]; // Pega o que foi digitado no campo senha

    if (!empty($novo_nome) && !empty($novo_email)) {
        try {
            // Se o campo de senha não estiver vazio, atualiza a senha também
            if (!empty($nova_senha)) {
                // Criptografa a nova senha antes de salvar
                $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);
                
                $sql = "UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone, senha = :senha WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":nome" => $novo_nome,
                    ":email" => $novo_email,
                    ":telefone" => $novo_telefone,
                    ":senha" => $senha_criptografada,
                    ":id" => $cliente_id
                ]);
            } else {
                // Se a senha estiver vazia, atualiza apenas os outros dados
                $sql = "UPDATE clientes SET nome = :nome, email = :email, telefone = :telefone WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":nome" => $novo_nome,
                    ":email" => $novo_email,
                    ":telefone" => $novo_telefone,
                    ":id" => $cliente_id
                ]);
            }

            // Atualiza a variável de sessão para mudar o nome no cabeçalho
            $_SESSION["cliente"] = $novo_nome;
            
            $mensagem_sucesso = "Perfil atualizado com sucesso! Redirecionando...";
            header("Refresh: 1.5; url=chamados.php"); 
        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao atualizar o perfil. Tente novamente.";
        }
    } else {
        $mensagem_erro = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - Área do Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Segoe UI", sans-serif;
            background: #eef2f7;
            color: #1e293b;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .card-perfil {
            background: white;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .08);
            width: min(100%, 600px);
            border-radius: 24px;
            overflow: hidden;
            animation: showUp .3s ease;
        }

        .card-perfil-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 35px;
            text-align: center;
            position: relative;
        }

        .card-perfil-header h2 {
            font-size: 26px;
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 4px;
        }

        .card-perfil-header p {
            font-size: 14px;
            opacity: 0.85;
            margin: 0;
        }

        .avatar-preview {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            border: 3px solid rgba(255, 255, 255, 0.25);
        }

        .card-perfil-body {
            padding: 40px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: #475569;
        }

        .input-group-text {
            background-color: #f8fafc;
            border-color: #d6dbe3;
            color: #64748b;
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }

        input.form-control {
            padding: 12px 14px;
            border: 1px solid #d6dbe3;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            font-size: 15px;
            transition: all 0.2s;
        }

        input.form-control:focus {
            outline: none;
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, .12);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 14px;
            margin-top: 30px;
        }

        .btn-cancelar {
            width: 100%;
            padding: 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .25s;
        }

        .btn-cancelar:hover {
            background: #f1f5f9;
            color: #334155;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #2a5298, #1e3c72);
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: .25s;
            box-shadow: 0 8px 20px rgba(42, 82, 152, .15);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(42, 82, 152, .25);
        }

        @keyframes showUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <div class="card-perfil">
        <div class="card-perfil-header">
            <div class="avatar-preview">
                <?php 
                    echo strtoupper(substr($cliente['nome'], 0, 1)) ?: "👤"; 
                ?>
            </div>
            <h2>Editar Meu Perfil</h2>
            <p>Mantenha seus dados atualizados para contato técnico</p>
        </div>

        <div class="card-perfil-body">
            
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <div><?php echo $mensagem_sucesso; ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($mensagem_erro)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?php echo $mensagem_erro; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="salvar_perfil" value="1">

                <div class="form-group">
                    <label for="nome">Nome Completo *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" id="nome" name="nome" class="form-control" 
                               value="<?php echo htmlspecialchars($cliente['nome']); ?>" required placeholder="Seu nome completo">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">E-mail de Contato *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?php echo htmlspecialchars($cliente['email']); ?>" required placeholder="exemplo@email.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefone">WhatsApp / Telefone</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                        <input type="text" id="telefone" name="telefone" class="form-control" 
                               value="<?php echo htmlspecialchars($cliente['telefone']); ?>" placeholder="(00) 00000-0000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Nova Senha <span class="text-muted" style="font-weight: normal; font-size: 12px;"></span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="senha" name="senha" class="form-control" placeholder="Digite uma nova senha">
                    </div>
                </div>

                <div class="actions-grid">
                    <a href="chamados.php" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-submit">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>