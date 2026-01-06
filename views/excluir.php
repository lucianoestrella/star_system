<?php
include('../includes/header.php');
include('../includes/conexao.php');
include('../includes/funcoes.php'); // Necessário para usar registrarLog()

// 1. Segurança de Nível: Apenas Admin pode excluir
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: ../views/dashboard.php?erro=permissao');
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $usuario_logado_id = $_SESSION['usuario_id'];

    // 2. Impede que o admin se exclua a si próprio
    if ($id == $usuario_logado_id) {
        echo "<script>alert('Erro: Não pode excluir a sua própria conta!'); window.location.href='../views/dashboard.php';</script>";
        exit;
    }

    // 3. Busca o nome do usuário antes de excluir para salvar no log
    $sql_busca = "SELECT nome FROM usuarios WHERE id = '$id'";
    $res_busca = mysqli_query($conn, $sql_busca);
    $dados_usuario = mysqli_fetch_assoc($res_busca);
    $nome_excluido = $dados_usuario ? $dados_usuario['nome'] : "Desconhecido";

    // 4. Executa a exclusão
    $sql_delete = "DELETE FROM usuarios WHERE id = '$id'";

    if (mysqli_query($conn, $sql_delete)) {
        // 5. Registra a atividade no log
        registrarLog($conn, $usuario_logado_id, "Excluiu permanentemente o usuário: $nome_excluido");
        
        header('Location: ../views/dashboard.php?status=sucesso_exclusao');
    } else {
        echo "Erro ao excluir: " . mysqli_error($conn);
    }
} else {
    header('Location: ../views/dashboard.php');
}
include('../includes/footer.php');
?>