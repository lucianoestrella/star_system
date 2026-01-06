<?php
session_start();
include('../includes/conexao.php'); // Sobe uma pasta para achar a conexão

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = $_POST['senha'];

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verifica se usuário existe e se a senha bate com o hash
    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['nivel'] = $user['nivel'];

$_SESSION['primeiro_acesso'] = $user['primeiro_acesso'];

    if ($_SESSION['primeiro_acesso'] == 1) {
    header('Location: ../pages/meu_perfil.php?primeiro_acesso=1');
    exit;
    } else {
    header('Location: ../pages/dashboard.php');
    }
        
        // Redireciona para a dashboard que está na pasta views
        header('Location: ../views/dashboard.php');
        exit;
    } else {
        echo "<script>alert('E-mail ou senha incorretos!'); window.location.href='../index.html';</script>";
    }
}
?>