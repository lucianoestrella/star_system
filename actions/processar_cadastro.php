<?php
session_start();
include('../includes/conexao.php');

// 1. Verificação de Segurança
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

// 2. Captura de Dados do Formulário
$nome   = mysqli_real_escape_string($conn, $_POST['nome']);
$email  = mysqli_real_escape_string($conn, $_POST['email']);
$cpf    = mysqli_real_escape_string($conn, $_POST['cpf']); // Novo campo
$nivel_novo = mysqli_real_escape_string($conn, $_POST['nivel']);
$telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
$regiao = mysqli_real_escape_string($conn, $_POST['regiao_administrativa']);
$titulo = mysqli_real_escape_string($conn, $_POST['titulo_eleitor']);

// Campos de localização
$bairro = isset($_POST['bairro']) ? mysqli_real_escape_string($conn, $_POST['bairro']) : '';
$logradouro = isset($_POST['logradouro']) ? mysqli_real_escape_string($conn, $_POST['logradouro']) : '';
$secao_eleitoral = isset($_POST['secao_eleitoral']) ? mysqli_real_escape_string($conn, $_POST['secao_eleitoral']) : '';

// 3. Definição da URL de retorno em caso de erro (Dinâmico)
$origem = "cadastrar_pessoa.php";
if ($nivel_novo == 'coordenador') $origem = "cadastrar_coordenador.php";
if ($nivel_novo == 'lider') $origem = "cadastrar_lider.php";

// --- VALIDAÇÕES DE DUPLICIDADE ---

// 1. Verificar se o CPF já existe
$check_cpf = mysqli_query($conn, "SELECT id FROM usuarios WHERE cpf = '$cpf'");
if (mysqli_num_rows($check_cpf) > 0) {
    header("Location: ../pages/$origem?erro=cpf_duplicado");
    exit;
}

// 2. Verificar se o Título já existe (apenas se preenchido)
if (!empty($titulo)) {
    $check_titulo = mysqli_query($conn, "SELECT id FROM usuarios WHERE titulo_eleitor = '$titulo'");
    if (mysqli_num_rows($check_titulo) > 0) {
        header("Location: ../pages/$origem?erro=titulo_duplicado");
        exit;
    }
}

// 3. Verificar se o E-mail já existe
$check_email = mysqli_query($conn, "SELECT id FROM usuarios WHERE email = '$email'");
if (mysqli_num_rows($check_email) > 0) {
    header("Location: ../pages/$origem?erro=email_duplicado");
    exit;
}

/**
 * 4. TRATAMENTO DA SENHA
 */
$senha_input = isset($_POST['senha']) && !empty($_POST['senha']) ? $_POST['senha'] : "Polis@2026";
$senha_hash = password_hash($senha_input, PASSWORD_DEFAULT);

// Dados de quem está cadastrando
$id_logado = $_SESSION['usuario_id'];
$nivel_logado = $_SESSION['nivel'];

/**
 * 5. LÓGICA DE HIERARQUIA AUTOMÁTICA
 */
$coordenador_vinculo = "NULL";
$lider_vinculo = "NULL";

if ($nivel_logado == 'coordenador') {
    $coordenador_vinculo = "'$id_logado'";
} 
elseif ($nivel_logado == 'lider') {
    $lider_vinculo = "'$id_logado'";
    
    $busca_coord = mysqli_query($conn, "SELECT coordenador_id FROM usuarios WHERE id = '$id_logado'");
    $dados_lider = mysqli_fetch_assoc($busca_coord);
    
    if ($dados_lider['coordenador_id']) {
        $id_coord_do_lider = $dados_lider['coordenador_id'];
        $coordenador_vinculo = "'$id_coord_do_lider'";
    }
}

// 6. Processamento da Foto
$foto_nome = "default.png";
if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
    $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto_nome = md5(uniqid()) . "." . $extensao;
    move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/uploads/" . $foto_nome);
}

// 7. Inserção no Banco de Dados (Incluso campo CPF)
$sql = "INSERT INTO usuarios 
        (nome, email, cpf, senha, nivel, foto, coordenador_id, lider_id, telefone, 
         regiao_administrativa, logradouro, bairro, titulo_eleitor, secao_eleitoral, data_cadastro) 
        VALUES 
        ('$nome', '$email', '$cpf', '$senha_hash', '$nivel_novo', '$foto_nome', $coordenador_vinculo, $lider_vinculo, '$telefone', 
         '$regiao', '$logradouro', '$bairro', '$titulo', '$secao_eleitoral', NOW())";

if (mysqli_query($conn, $sql)) {
    // 8. Registro de Log
    $acao = "Cadastrou um novo $nivel_novo: $nome";
    mysqli_query($conn, "INSERT INTO logs (usuario_id, acao, data_hora) VALUES ('$id_logado', '$acao', NOW())");
    
    header('Location: ../views/dashboard.php?msg=sucesso');
} else {
    echo "Erro crítico ao cadastrar: " . mysqli_error($conn);
}
// Pega o IP do usuário
$ip_usuario = $_SERVER['REMOTE_ADDR'];

// Registro de Log Melhorado
$acao = "Cadastrou um novo $nivel_novo: $nome";
$sql_log = "INSERT INTO logs (usuario_id, acao, data_hora, ip_acesso, tipo_entidade) 
            VALUES ('$id_logado', '$acao', NOW(), '$ip_usuario', '$nivel_novo')";

mysqli_query($conn, $sql_log);
?>