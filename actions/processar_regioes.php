<?php
session_start();
include('../includes/conexao.php');

if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['nivel'], ['admin', 'coordenador'])) {
    die("Acesso negado.");
}

// Lógica de ADICIONAR
if (isset($_POST['btn_adicionar'])) {
    $nome = mysqli_real_escape_string($conn, $_POST['nome_regiao']);
    
    $sql = "INSERT INTO regioes (nome) VALUES ('$nome')";
    if (mysqli_query($conn, $sql)) {
        header('Location: ../views/gerenciar_regioes.php?msg=sucesso');
    }
}

// Lógica de EXCLUIR
if (isset($_GET['excluir'])) {
    $id = mysqli_real_escape_string($conn, $_GET['excluir']);
    
    $sql = "DELETE FROM regioes WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        header('Location: ../views/gerenciar_regioes.php?msg=removido');
    }
}
?>