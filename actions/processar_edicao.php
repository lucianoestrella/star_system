<?php
session_start();
include('../includes/conexao.php');

// 1. Verificação de Segurança
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['nivel'], ['admin', 'coordenador', 'lider'])) {
    header('Location: ../index.php');
    exit;
}

// 2. Captura de Dados
$id = mysqli_real_escape_string($conn, $_POST['id']);
$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
$regiao = mysqli_real_escape_string($conn, $_POST['regiao_administrativa']);
$titulo = mysqli_real_escape_string($conn, $_POST['titulo_eleitor']);
$nova_senha = $_POST['nova_senha'];

// 3. Busca dados atuais para tratar foto e senha
$busca_atual = mysqli_query($conn, "SELECT foto, senha FROM usuarios WHERE id = '$id'");
$dados_atuais = mysqli_fetch_assoc($busca_atual);

// 4. Lógica de Senha (só altera se preenchida)
$sql_senha = "";
if (!empty($nova_senha)) {
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $sql_senha = ", senha = '$senha_hash'";
}

// 5. Lógica de Foto
$foto_nome = $dados_atuais['foto'];
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $novo_nome = md5(uniqid()) . "." . $extensao;
    
    if (move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/uploads/" . $novo_nome)) {
        // Remove a foto antiga se não for a default
        if ($foto_nome != 'default.png' && file_exists("../assets/uploads/" . $foto_nome)) {
            unlink("../assets/uploads/" . $foto_nome);
        }
        $foto_nome = $novo_nome;
    }
}

// 6. Execução do UPDATE
$sql = "UPDATE usuarios SET 
        nome = '$nome', 
        email = '$email', 
        telefone = '$telefone', 
        regiao_administrativa = '$regiao', 
        titulo_eleitor = '$titulo', 
        foto = '$foto_nome' 
        $sql_senha 
        WHERE id = '$id'";

if (mysqli_query($conn, $sql)) {
    // 7. Registro de Log
    $id_logado = $_SESSION['usuario_id'];
    $acao = "Editou as informações de: $nome (ID: $id)";
    mysqli_query($conn, "INSERT INTO logs (usuario_id, acao, data_hora) VALUES ('$id_logado', '$acao', NOW())");

    header('Location: ../views/dashboard.php?msg=editado');
} else {
    echo "Erro ao atualizar: " . mysqli_error($conn);
}
?>