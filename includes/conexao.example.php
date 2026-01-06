<?php
$host = "localhost";
$user = "";
$pass = "";
$db   = "sistema_gestao";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}
?>