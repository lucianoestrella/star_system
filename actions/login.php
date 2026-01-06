<?php
session_start();
include('../includes/conexao.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $senha = $_POST['senha'];

    $query = "SELECT * FROM usuarios WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verifica se usuário existe e se a senha bate com o hash
    if ($user && password_verify($senha, $user['senha'])) {
        // Define as variáveis de sessão principais
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['nivel'] = $user['nivel'];
        $_SESSION['foto'] = $user['foto']; // É bom já carregar a foto aqui também
        $_SESSION['primeiro_acesso'] = $user['primeiro_acesso'];

        // Lógica de Redirecionamento
        if ($_SESSION['primeiro_acesso'] == 1) {
            // Se for o primeiro acesso, força a troca de senha na página de perfil
            header('Location: ../views/meu_perfil.php?primeiro_acesso=1');
            exit;
        } else {
            // Se não for o primeiro acesso, vai direto para a dashboard
            header('Location: ../views/dashboard.php');
            exit;
        }

    } else {
        // Se as credenciais estiverem erradas
        echo "<script>alert('E-mail ou senha incorretos!'); window.location.href='../index.html';</script>";
    }
}
?>