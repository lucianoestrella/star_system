<?php
session_start();
include('../includes/conexao.php');

if (!isset($_SESSION['usuario_id'])) exit;

$id = $_SESSION['usuario_id'];
$telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
$nova_senha = $_POST['nova_senha'];
$confirmar_senha = $_POST['confirmar_senha'];

// 1. Atualização de Dados Básicos
$update_campos = "telefone = '$telefone'";

// 2. Lógica de Senha
if (!empty($nova_senha)) {
    if ($nova_senha === $confirmar_senha) {
        $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $update_campos .= ", senha = '$senha_hash'";
        
        // ADIÇÃO: Se trocou a senha, desativamos a marcação de primeiro acesso
        $update_campos .= ", primeiro_acesso = 0";
        $_SESSION['primeiro_acesso'] = 0; 
    } else {
        // Melhorando o retorno de erro para o usuário
        header('Location: ../views/meu_perfil.php?msg=erro_senha');
        exit;
    }
}

// 3. Processamento de Nova Foto (opcional)
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nome_foto = md5(uniqid()) . "." . $ext;
    move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/uploads/" . $nome_foto);
    $update_campos .= ", foto = '$nome_foto'";
    $_SESSION['foto'] = $nome_foto; // Atualiza a foto na sessão para refletir na sidebar
}

// 4. Executa a Query
$sql = "UPDATE usuarios SET $update_campos WHERE id = '$id'";

if (mysqli_query($conn, $sql)) {
    // Se o usuário veio do fluxo de primeiro acesso, ao salvar ele vai para o dashboard
    if (isset($_GET['primeiro_acesso'])) {
        header('Location: ../views/dashboard.php?msg=boas_vindas');
    } else {
        header('Location: ../views/meu_perfil.php?msg=sucesso');
    }
} else {
    echo "Erro ao atualizar: " . mysqli_error($conn);
}