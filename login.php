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

$erro = "";

// LOGIN
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $senha = trim($_POST["senha"]);

    $stmt = $conn->prepare("SELECT * FROM operadores WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {

        $operador = $resultado->fetch_assoc();

        if (password_verify($senha, $operador["senha"])) {

            $_SESSION["usuario"] = $operador["nome"];
            $_SESSION["operador_id"] = $operador["id"];

            header("Location: inicial.php");
            exit();

        } else {
            $erro = "Senha incorreta!";
        }

    } else {
        $erro = "Usuário não encontrado!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login - Sistema OS</title>

<style>

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body{
    font-family: "Segoe UI", sans-serif;
    height: 100vh;
    display: flex;
    overflow: hidden;
}

/* ===== LADO ESQUERDO ===== */

.left{
    width: 55%;
    
    background:
        linear-gradient(rgba(15,32,39,0.75), rgba(44,83,100,0.75)),
        url('img/fundo.jpg');

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    color: white;

    display: flex;
    flex-direction: column;
    justify-content: center;

    padding: 60px;
}

.left h1{
    font-size: 42px;
    margin-bottom: 20px;
    font-weight: bold;
}

.left p{
    font-size: 18px;
    line-height: 28px;
    max-width: 500px;
}

/* ===== LADO DIREITO ===== */

.right{
    width: 45%;
    background: #f4f6f9;

    display: flex;
    justify-content: center;
    align-items: center;
}

/* ===== BOX LOGIN ===== */

.box{
    background: white;

    width: 380px;

    padding: 40px;

    border-radius: 15px;

    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.box h2{
    text-align: center;
    margin-bottom: 25px;
    color: #203a43;
}

/* ===== INPUTS ===== */

input{
    width: 100%;

    padding: 14px;

    margin-bottom: 15px;

    border: 1px solid #ccc;
    border-radius: 8px;

    font-size: 15px;

    transition: 0.3s;
}

input:focus{
    border-color: #2c5364;
    outline: none;

    box-shadow: 0 0 5px rgba(44,83,100,0.3);
}

/* ===== BOTÃO ===== */

button{
    width: 100%;

    padding: 14px;

    background: #2c5364;

    color: white;

    border: none;
    border-radius: 8px;

    font-size: 16px;
    font-weight: bold;

    cursor: pointer;

    transition: 0.3s;
}

button:hover{
    background: #203a43;
    transform: scale(1.02);
}

/* ===== ERRO ===== */

.erro{
    background: #ffe5e5;
    color: #d8000c;

    padding: 10px;

    border-radius: 6px;

    margin-bottom: 15px;

    text-align: center;

    font-size: 14px;
}

/* ===== LINK ===== */

.link{
    margin-top: 18px;
    text-align: center;
}

.link a{
    text-decoration: none;
    color: #2c5364;
    font-size: 14px;
    font-weight: 500;
}

.link a:hover{
    text-decoration: underline;
}

/* ===== RESPONSIVO ===== */

@media(max-width: 900px){

    .left{
        display: none;
    }

    .right{
        width: 100%;
    }
}

</style>
</head>

<body>

<div class="left">
    <h1>Sistema de Ordem de Serviços</h1>

    <p>
        Gerencie atendimentos, controle tarefas e acompanhe serviços em tempo real com praticidade e segurança.
    </p>
</div>

<div class="right">

    <div class="box">

        <h2>Login</h2>

        <?php if($erro != "") { ?>
            <div class="erro">
                <?php echo $erro; ?>
            </div>
        <?php } ?>

        <form method="POST">

        <form method="POST">

        <input 
            type="email" 
            name="email" 
            placeholder="Digite seu email" 
            required
        >

        <input 
            type="password" 
            name="senha" 
            placeholder="Digite sua senha" 
            required
        >

        <button type="submit">
            Entrar
        </button>

        </form>

        <div class="link">
            <a href="esqueci_senha.php">
                Esqueci minha senha?
            </a>
        </div>

        <div class="link">
            <a href="cadastro.php">
                Criar conta
            </a>
        </div>

</div>

</body>
</html>