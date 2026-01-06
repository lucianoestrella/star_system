<?php
include('../includes/header.php');
include('../includes/conexao.php');
include('../includes/funcoes.php');

// Segurança: Apenas Admin e Coordenador podem gerenciar regiões
if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['nivel'], ['admin', 'coordenador'])) {
    header('Location: dashboard.php');
    exit;
}

$regioes = listarRegioes($conn);
include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Gerenciar Regiões - Sistema Polis</title>
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <header>
            <h2>Gerenciar Regiões</h2>
            <p style="color: var(--text-muted);">Adicione ou remova as Regiões Administrativas do sistema.</p>
        </header>

        <form action="../actions/processar_regiao.php" method="POST" style="margin-bottom: 30px; background: rgba(255,255,255,0.03); padding: 20px; border-radius: 8px;">
            <label style="color: var(--text-muted); font-size: 14px;">Nova Região</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" name="nome_regiao" placeholder="Ex: Sobradinho" required style="margin-bottom: 0;">
                <button type="submit" name="btn_adicionar" class="btn btn-primary">Adicionar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nome da Região</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($reg = mysqli_fetch_assoc($regioes)): ?>
                    <tr>
                        <td><?php echo $reg['nome']; ?></td>
                        <td style="text-align: center;">
                            <a href="../actions/processar_regiao.php?excluir=<?php echo $reg['id']; ?>" 
                               style="color: var(--danger); font-size: 12px; text-decoration: none;"
                               onclick="return confirm('Tem certeza que deseja remover esta região?')">Remover</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            <a href="dashboard.php" class="btn" style="background: rgba(255,255,255,0.05); color: white; display: block; text-align: center;">Voltar ao Dashboard</a>
        </div>
    </div>
</body>
</html>