<?php
session_start();
include('../includes/conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado.");
}

$id_usuario = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha_nova = $_POST['senha_nova'];

    // Update básico (Nome e Email)
    $sql = "UPDATE usuarios SET nome = '$nome', email = '$email'";

    // Se o usuário digitou uma nova senha
    if (!empty($senha_nova)) {
        $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
        // Atualizamos a senha e marcamos que o primeiro acesso foi concluído (0)
        $sql .= ", senha = '$senha_hash', primeiro_acesso = 0";
        $_SESSION['primeiro_acesso'] = 0; // Atualiza a sessão
    }

    $sql .= " WHERE id = '$id_usuario'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['nome'] = $nome; // Atualiza nome na sessão
        header("Location: ../pages/meu_perfil.php?sucesso=1");
    } else {
        header("Location: ../pages/meu_perfil.php?erro=1");
    }
}