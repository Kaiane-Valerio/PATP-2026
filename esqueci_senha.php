<?php
session_start();

$host = "localhost";
$dbname = "ordens";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$mensagem = "";
$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $novaSenha = trim($_POST["nova_senha"]);

    $stmt = $conn->prepare("SELECT id FROM operadores WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        $update = $conn->prepare("UPDATE operadores SET senha = ? WHERE email = ?");
        $update->bind_param("ss", $senhaHash, $email);

        if ($update->execute()) {
            $mensagem = "Senha alterada com sucesso!";
        } else {
            $erro = "Erro ao alterar a senha.";
        }

    } else {
        $erro = "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>

<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Senha</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:"Segoe UI",sans-serif;
    background:#f4f6f9;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.box{
    width:400px;
    background:white;
    padding:40px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

h2{
    text-align:center;
    margin-bottom:20px;
    color:#203a43;
}

input{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:8px;
}

button{
    width:100%;
    padding:14px;
    background:#2c5364;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#203a43;
}

.sucesso{
    background:#e8ffe8;
    color:green;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
}

.erro{
    background:#ffe5e5;
    color:#d8000c;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
}

.link{
    margin-top:15px;
    text-align:center;
}

.link a{
    text-decoration:none;
    color:#2c5364;
}
</style>

</head>
<body>

<div class="box">

<h2>Recuperar Senha</h2>

<?php if($mensagem != "") { ?>
    <div class="sucesso"><?php echo $mensagem; ?></div>
<?php } ?>

<?php if($erro != "") { ?>
    <div class="erro"><?php echo $erro; ?></div>
<?php } ?>

<form method="POST">

    <input
        type="email"
        name="email"
        placeholder="Digite seu e-mail"
        required
    >

    <input
        type="password"
        name="nova_senha"
        placeholder="Digite a nova senha"
        required
    >

    <button type="submit">
        Alterar Senha
    </button>

</form>

<div class="link">
    <a href="login.php">Voltar ao Login</a>
</div>

</div>

</body>
</html>
