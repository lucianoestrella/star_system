<?php
include('../includes/header.php');
include('../includes/conexao.php');
include('../includes/funcoes.php');

// 1. Verificação de Acesso
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['nivel'], ['admin', 'coordenador', 'lider'])) {
    header('Location: dashboard.php');
    exit;
}

// 2. Busca os dados atuais do usuário a ser editado
if (isset($_GET['id'])) {
    $id_editar = mysqli_real_escape_string($conn, $_GET['id']);
    $query = "SELECT * FROM usuarios WHERE id = '$id_editar'";
    $resultado = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($resultado);

    if (!$user) {
        header('Location: dashboard.php');
        exit;
    }
} else {
    header('Location: dashboard.php');
    exit;
}
include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Editar Usuário - Sistema Polis</title>
</head>
<body>
    <div class="container" style="max-width: 700px;">
        <header>
            <h2>Editar Cadastro</h2>
            <p style="color: var(--text-muted);">Atualize as informações de <strong><?php echo $user['nome']; ?></strong></p>
        </header>

        <form action="../actions/processar_edicao.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

            <div style="display: flex; align-items: center; gap: 20px; background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <img src="../assets/uploads/<?php echo $user['foto'] ? $user['foto'] : 'default.png'; ?>" 
                     style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary);">
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Alterar Foto de Perfil</label>
                    <input type="file" name="foto" accept="image/*" style="font-size: 13px;">
                </div>
            </div>

            <label style="color: var(--text-muted); font-size: 14px;">Nome Completo</label>
            <input type="text" name="nome" value="<?php echo $user['nome']; ?>" required>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Telefone / WhatsApp</label>
                    <input type="text" name="telefone" id="telefone" value="<?php echo $user['telefone']; ?>" required>
                </div>
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Região Administrativa</label>
                    <select name="regiao_administrativa" required 
                        style="width: 100%; padding: 12px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; margin-bottom: 20px;">
                        <option value="">Selecione...</option>
                        <?php
                        $regioes = listarRegioes($conn);
                        while($reg = mysqli_fetch_assoc($regioes)): 
                            $selected = ($reg['nome'] == $user['regiao_administrativa']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $reg['nome']; ?>" <?php echo $selected; ?>>
                                <?php echo $reg['nome']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <label style="color: var(--text-muted); font-size: 14px;">Título de Eleitor</label>
            <input type="text" name="titulo_eleitor" id="titulo_eleitor" value="<?php echo $user['titulo_eleitor']; ?>">

            <label style="color: var(--text-muted); font-size: 14px;">E-mail de Acesso</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

            <div style="background: rgba(234, 179, 8, 0.1); padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <p style="font-size: 12px; color: var(--warning); margin: 0;">
                    ⚠️ Deixe o campo de senha em branco caso não deseje alterá-la.
                </p>
            </div>
            
            <label style="color: var(--text-muted); font-size: 14px;">Nova Senha (opcional)</label>
            <input type="password" name="nova_senha" placeholder="Digitar nova senha...">

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 2; justify-content: center;">Salvar Alterações</button>
                <a href="dashboard.php" class="btn" style="flex: 1; justify-content: center; background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://unpkg.com/imask"></script>
    <script>
        IMask(document.getElementById('telefone'), { mask: '(00) 00000-0000' });
        IMask(document.getElementById('titulo_eleitor'), { mask: '0000 0000 0000' });
    </script>
</body>
</html>