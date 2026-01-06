<?php
include('../includes/conexao.php');

$nome  = "Administrador";
$email = "admin@sistema.com";
$senha = "admin123"; // Senha que você usará no login
$nivel = "admin";

// Criptografa a senha antes de salvar
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios (nome, email, senha, nivel) VALUES ('$nome', '$email', '$senha_hash', '$nivel')";

if (mysqli_query($conn, $sql)) {
    echo "Usuário Admin criado com sucesso!";
} else {
    echo "Erro: " . mysqli_error($conn);
}
?>