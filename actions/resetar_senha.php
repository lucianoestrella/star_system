<?php
session_start();
include('../includes/conexao.php');

// Verifica se o usuário está logado e se é admin
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] !== 'admin') {
    die("Acesso negado.");
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Define a senha padrão (criptografada)
    // Dica: Informe ao usuário que a nova senha será Polis@2026
    $senha_padrao = password_hash("Polis@2026", PASSWORD_DEFAULT);
    
    // ATUALIZAÇÃO: Resetamos a senha e forçamos o primeiro acesso
    $sql = "UPDATE usuarios SET senha = '$senha_padrao', primeiro_acesso = 1 WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        // Redireciona de volta para a visualização de usuários/dashboard
        header("Location: ../views/dashboard.php?msg=reset_sucesso");
    } else {
        header("Location: ../views/dashboard.php?msg=reset_erro");
    }
} else {
    header("Location: ../views/dashboard.php");
}
exit;