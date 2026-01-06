<?php
include('../includes/header.php');
include('../includes/conexao.php');

// 1. Proteção de acesso: apenas logados podem ver relatórios
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$id_logado = $_SESSION['usuario_id'];
$nivel_logado = $_SESSION['nivel'];
$nome_logado = $_SESSION['nome'];

/**
 * 2. LÓGICA DE FILTRO DO RELATÓRIO
 * - Admin: vê todos (sem filtro)
 * - Coordenador: vê quem tem coordenador_id = seu ID
 * - Líder: vê quem tem lider_id = seu ID
 */
$filtro = "";

if ($nivel_logado == 'coordenador') {
    $filtro = " WHERE u.coordenador_id = '$id_logado'";
} elseif ($nivel_logado == 'lider') {
    $filtro = " WHERE u.lider_id = '$id_logado'";
} elseif ($nivel_logado == 'pessoa') {
    // Nível pessoa vê apenas a si mesmo ou nada, dependendo da sua regra. 
    // Aqui vou bloquear o acesso para manter a hierarquia.
    echo "<script>alert('Acesso negado aos relatórios.'); window.location.href='dashboard.php';</script>";
    exit;
}

// 3. Query com os JOINS para mostrar os nomes dos responsáveis na lista
$query = "SELECT u.nome, u.email, u.nivel, u.data_cadastro, 
          c.nome as nome_coordenador, 
          l.nome as nome_lider 
          FROM usuarios u
          LEFT JOIN usuarios c ON u.coordenador_id = c.id
          LEFT JOIN usuarios l ON u.lider_id = l.id
          $filtro
          ORDER BY u.nome ASC";

$resultado = mysqli_query($conn, $query);

include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Relatório de Membros - Sistema Polis</title>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; color: black; }
            .container { max-width: 100%; border: none; box-shadow: none; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ddd; color: black !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header style="border-bottom: 2px solid var(--primary); padding-bottom: 20px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h2 style="margin: 0;">Relatório de Membros</h2>
                    <p style="color: var(--text-muted); margin-top: 5px;">
                        Gerado por: <strong><?php echo $nome_logado; ?></strong> | Nível: <?php echo ucfirst($nivel_logado); ?>
                    </p>
                </div>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-primary">Imprimir / PDF</button>
                    <a href="dashboard.php" class="btn" style="background: #334155; color: white;">Voltar</a>
                </div>
            </div>
        </header>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nome Completo</th>
                        <th>E-mail</th>
                        <th>Nível</th>
                        <th>Coordenador</th>
                        <th>Líder</th>
                        <th>Data Cadastro</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($resultado) > 0): ?>
                        <?php while($user = mysqli_fetch_assoc($resultado)): ?>
                        <tr>
                            <td><strong><?php echo $user['nome']; ?></strong></td>
                            <td style="font-size: 13px;"><?php echo $user['email']; ?></td>
                            <td><span style="font-size: 11px; padding: 2px 8px; border-radius: 4px; background: rgba(255,255,255,0.05);">
                                <?php echo ucfirst($user['nivel']); ?>
                            </span></td>
                            <td style="font-size: 12px;"><?php echo $user['nome_coordenador'] ?? '-'; ?></td>
                            <td style="font-size: 12px;"><?php echo $user['nome_lider'] ?? '-'; ?></td>
                            <td style="font-size: 12px;"><?php echo date('d/m/Y', strtotime($user['data_cadastro'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                Nenhum membro vinculado encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <footer style="margin-top: 30px; font-size: 11px; color: var(--text-muted); text-align: center;">
            Sistema Polis - Relatório Gerado em <?php echo date('d/m/Y H:i'); ?>
        </footer>
    </div>
</body>
</html>