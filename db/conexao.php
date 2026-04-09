<!-- Criar conexão com o banco de dados -->
<?php

$host = "localhost";
$dbname = "ordens";
$user = "root";
$password = "";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

?>