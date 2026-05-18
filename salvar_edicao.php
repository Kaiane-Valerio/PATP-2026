<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ordens");

if ($conn->connect_error) {
    die("Erro: " . $conn->connect_error);
}


if (!isset($_SESSION["operador_id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["operador_id"];


$nome = $_POST["nome"] ?? "";
$email = $_POST["email"] ?? "";
$telefone = $_POST["telefone"] ?? "";
$identidade = $_POST["identidade"] ?? "";
$senha = $_POST["senha"] ?? "";

$mensagem = "";
$sucesso = false;

if (!empty($nome) && !empty($email)) {

    $check = $conn->prepare("SELECT id FROM operadores WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $mensagem = "Este email já está em uso por outro operador!";
    } else {

        if (!empty($senha)) {
           
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE operadores SET nome=?, email=?, telefone=?, identidade=?, senha=? WHERE id=?");
            $stmt->bind_param("sssssi", $nome, $email, $telefone, $identidade, $senhaHash, $id);
        } else {
        
            $stmt = $conn->prepare("UPDATE operadores SET nome=?, email=?, telefone=?, identidade=? WHERE id=?");
            $stmt->bind_param("ssssi", $nome, $email, $telefone, $identidade, $id);
        }

        if ($stmt->execute()) {
      
            $_SESSION["operador_nome"] = $nome; 
            $_SESSION["usuario"] = $nome; 
            
            $mensagem = "Dados atualizados com sucesso!";
            $sucesso = true;
        } else {
            $mensagem = "Erro ao atualizar os dados no banco.";
        }
    }

} else {
    $mensagem = "Nome e Email são campos obrigatórios.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Atualização - Sistema OS</title>

<style>
    body {
        font-family: "Segoe UI", sans-serif;
        background: #f4f6f9;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .box {
        background: white;
        padding: 40px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        width: 320px;
    }

    .sucesso { color: #2ecc71; }
    .erro { color: #e74c3c; }

    .loader {
        margin-top: 15px;
        font-size: 14px;
        color: #555;
    }

    .btn {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background: #1e3c72;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
    }
</style>

<?php if ($sucesso): ?>
<script>
    setTimeout(() => {
        window.location.href = "inicial.php";
    }, 2000);
</script>
<?php endif; ?>

</head>

<body>

<div class="box">
    <h2 class="<?php echo $sucesso ? 'sucesso' : 'erro'; ?>">
        <?php echo $sucesso ? "✅" : "❌"; ?><br>
        <?php echo $mensagem; ?>
    </h2>

    <?php if ($sucesso): ?>
        <div class="loader">Aguarde, redirecionando...</div>
    <?php else: ?>
        <a href="editar_perfil.php" class="btn">Tentar novamente</a>
    <?php endif; ?>
</div>

</body>
</html>