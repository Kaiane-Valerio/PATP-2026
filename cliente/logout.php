<?php
session_start();
session_destroy();
header("Location: loginCliente.php");
exit();
?>