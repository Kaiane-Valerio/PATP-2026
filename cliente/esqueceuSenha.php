<?php
session_start();
require_once "../db/conexao.php";

$erro = "";
$sucesso = "";
$passo2 = false; // Controla se exibe o campo da nova senha
$usuario_id = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // PASSO 1: Verificar se o e-mail existe
    if (isset($_POST["verificar_email"])) {
        $email = trim($_POST["email"]);
        
        $sql = "SELECT id FROM clientes WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["email" => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $passo2 = true;
            $usuario_id = $user["id"];
        } else {
            $erro = "E-mail não encontrado em nosso sistema!";
        }
    }

    // PASSO 2: Atualizar a senha diretamente
    if (isset($_POST["atualizar_senha"])) {
        $usuario_id = $_POST["usuario_id"];
        $nova_senha = $_POST["nova_senha"];
        $confirmar_senha = $_POST["confirmar_senha"];

        if (!empty($nova_senha) && $nova_senha === $confirmar_senha) {
            // Criptografa a nova senha
            $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);

            $sql = "UPDATE clientes SET senha = :senha WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":senha" => $senha_criptografada,
                ":id" => $usuario_id
            ]);

            $sucesso = "Senha redefinida com sucesso! Redirecionando...";
            header("Refresh: 2; url=loginCliente.php");
        } else {
            $passo2 = true; // Mantém no formulário de senha
            $erro = "As senhas não coincidem ou estão vazias!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Acesso</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --card: #ffffff;
            --text: #0f172a;
            --text-light: #64748b;
            --border: #dbe2ea;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        body {
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(to bottom right, #eef2f7, #f8fafc);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .box {
            width: 100%;
            max-width: 430px;
            background: var(--card);
            border-radius: 22px;
            padding: 40px;
            box-shadow: var(--shadow);
            animation: fadeUp .5s ease;
        }
        h2 { color: var(--text); margin-bottom: 10px; font-size: 1.8rem; text-align: center;}
        p { color: var(--text-light); line-height: 1.6; margin-bottom: 24px; font-size: 0.95rem; text-align: center;}
        
        .erro { background: #fee2e2; color: #b91c1c; padding: 14px; border-radius: 12px; margin-bottom: 18px; font-size: .95rem; border: 1px solid #fecaca; }
        .sucesso { background: #dcfce7; color: #15803d; padding: 14px; border-radius: 12px; margin-bottom: 18px; font-size: .95rem; border: 1px solid #bbf7d0; }

        input {
            width: 100%;
            height: 54px;
            padding: 0 16px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: #fff;
            font-size: 15px;
            transition: .25s ease;
            margin-bottom: 16px;
        }
        input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(37, 99, 235, .12); }
        
        button {
            width: 100%;
            min-height: 54px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: .25s ease;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(37, 99, 235, .25); }
        
        .link-voltar { display: block; text-align: center; margin-top: 24px; color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.95rem; }
        .link-voltar:hover { text-decoration: underline; }
        
        @keyframes fadeUp { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

    <div class="box">
        <h2>Recuperar Senha</h2>
        
        <?php if (!empty($erro)): ?>
            <p class="erro"><?php echo $erro; ?></p>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <p class="sucesso"><?php echo $sucesso; ?></p>
        <?php endif; ?>

        <?php if (!$passo2 && empty($sucesso)): ?>
            <p>Digite seu e-mail cadastrado para redefinir sua senha de acesso.</p>
            <form method="POST">
                <input type="hidden" name="verificar_email" value="1">
                <input type="email" name="email" placeholder="Seu e-mail cadastrado" required>
                <button type="submit">Continuar</button>
            </form>
        <?php endif; ?>

        <?php if ($passo2 && empty($sucesso)): ?>
            <p>E-mail validado! Digite e confirme sua nova senha abaixo.</p>
            <form method="POST">
                <input type="hidden" name="atualizar_senha" value="1">
                <input type="hidden" name="usuario_id" value="<?php echo $usuario_id; ?>">
                
                <input type="password" name="nova_senha" placeholder="Nova Senha" required>
                <input type="password" name="confirmar_senha" placeholder="Confirme a Nova Senha" required>
                
                <button type="submit">Alterar Senha</button>
            </form>
        <?php endif; ?>

        <a href="loginCliente.php" class="link-voltar">Voltar para o login</a>
    </div>

</body>
</html>