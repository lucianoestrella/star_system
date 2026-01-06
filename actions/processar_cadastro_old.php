<?php
session_start();
include('../includes/conexao.php');

// 1. Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.html');
    exit;
}

// 2. Captura de Dados do Formulário
$nome = mysqli_real_escape_string($conn, $_POST['nome']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
$nivel_novo = mysqli_real_escape_string($conn, $_POST['nivel']);
$telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
$regiao = mysqli_real_escape_string($conn, $_POST['regiao_administrativa']);
$titulo = mysqli_real_escape_string($conn, $_POST['titulo_eleitor']);

// Dados de quem está a cadastrar (o utilizador logado)
$id_logado = $_SESSION['usuario_id'];
$nivel_logado = $_SESSION['nivel'];

/**
 * 3. LÓGICA DE HIERARQUIA AUTOMÁTICA
 * Definimos quem será o Coordenador e o Líder do novo registo
 */
$coordenador_vinculo = "NULL";
$lider_vinculo = "NULL";

if ($nivel_logado == 'coordenador') {
    // Se um Coordenador cadastra alguém, ele é o coordenador dessa pessoa
    $coordenador_vinculo = "'$id_logado'";
} 
elseif ($nivel_logado == 'lider') {
    // Se um Líder cadastra alguém, ele é o líder, 
    // e precisamos buscar quem é o coordenador deste líder para manter a cadeia
    $lider_vinculo = "'$id_logado'";
    
    $busca_coord = mysqli_query($conn, "SELECT coordenador_id FROM usuarios WHERE id = '$id_logado'");
    $dados_lider = mysqli_fetch_assoc($busca_coord);
    
    if ($dados_lider['coordenador_id']) {
        $id_coord_do_lider = $dados_lider['coordenador_id'];
        $coordenador_vinculo = "'$id_coord_do_lider'";
    }
}
// Nota: Se for Admin a cadastrar, os vínculos permanecem NULL por padrão, 
// a menos que queiras adicionar um campo de seleção no form de Admin.

// 4. Processamento da Foto
$foto_nome = "default.png";
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto_nome = md5(uniqid()) . "." . $extensao;
    move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/uploads/" . $foto_nome);
}

// 5. Inserção no Banco de Dados
$sql = "INSERT INTO usuarios 
        (nome, email, senha, nivel, foto, coordenador_id, lider_id, telefone, regiao_administrativa, titulo_eleitor, data_cadastro) 
        VALUES 
        ('$nome', '$email', '$senha', '$nivel_novo', '$foto_nome', $coordenador_vinculo, $lider_vinculo, '$telefone', '$regiao', '$titulo', NOW())";

if (mysqli_query($conn, $sql)) {
    // 6. Registo de Log
    $acao = "Cadastrou um novo $nivel_novo: $nome";
    mysqli_query($conn, "INSERT INTO logs (usuario_id, acao, data_hora) VALUES ('$id_logado', '$acao', NOW())");
    
    header('Location: ../views/dashboard.php?msg=sucesso');
} else {
    echo "Erro ao cadastrar: " . mysqli_error($conn);
}
?>