<?php
include('../includes/header.php');
include('../includes/conexao.php');

// SEGURANÇA: Admin, Coordenador e Pessoa podem cadastrar (conforme sua regra)
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$nivel_logado = $_SESSION['nivel'];
include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Cadastrar Usuário - Sistema Polis</title>
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <header>
            <h2>Novo Cadastro</h2>
            <p style="color: var(--text-muted);">Adicione um novo integrante ao sistema.</p>
        </header>

        <form action="../actions/processar_cadastro.php" method="POST" enctype="multipart/form-data">
            
            <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.1); margin-bottom: 20px; text-align: center;">
                <label style="color: var(--text-muted); display: block; margin-bottom: 10px; font-size: 14px;">Foto de Perfil (Opcional)</label>
                <input type="file" name="foto" accept="image/*" style="font-size: 13px; color: var(--text-muted);">
            </div>

            <label style="color: var(--text-muted); font-size: 14px;">Nome Completo</label>
            <input type="text" name="nome" placeholder="Ex: João Silva" required>

            <label style="color: var(--text-muted); font-size: 14px;">E-mail de Acesso</label>
            <input type="email" name="email" placeholder="exemplo@sistema.com" required>

            <label style="color: var(--text-muted); font-size: 14px;">Nível de Acesso</label>
            <select name="nivel" required 
                style="width: 100%; padding: 12px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; margin-bottom: 20px;">
                
                <option value="pessoa">Pessoa (Utilizador Comum)</option>
                <option value="lider">Líder</option>

                <?php if($nivel_logado == 'admin'): ?>
                    <option value="coordenador">Coordenador</option>
                    <option value="admin">Administrador</option>
                <?php endif; ?>

                <?php if($nivel_logado == 'coordenador'): ?>
                    <option value="coordenador">Coordenador</option>
                <?php endif; ?>
            </select>

            <label style="color: var(--text-muted); font-size: 14px;">Senha Provisória</label>
            <input type="password" name="senha" placeholder="••••••••" required>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 2; justify-content: center;">Finalizar Cadastro</button>
                <a href="dashboard.php" class="btn" style="flex: 1; justify-content: center; background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>