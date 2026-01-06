<?php
include('../includes/header.php');
include('../includes/conexao.php');

// Segurança: Apenas Admin acessa os Logs
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Busca os logs com as novas colunas (IP e Entidade)
$query = "SELECT logs.*, usuarios.nome as autor, usuarios.nivel as autor_nivel 
          FROM logs 
          LEFT JOIN usuarios ON logs.usuario_id = usuarios.id 
          ORDER BY logs.data_hora DESC LIMIT 150";
$resultado = mysqli_query($conn, $query);
include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoria de Sistema - Logs</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-pessoa { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .badge-lider { background: rgba(124, 58, 237, 0.2); color: #a78bfa; }
        .badge-coordenador { background: rgba(79, 70, 229, 0.2); color: #818cf8; }
        .badge-sistema { background: rgba(255, 255, 255, 0.1); color: #cbd5e1; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1100px;">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h2 style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-shield-alt" style="color: #4f46e5;"></i> 
                    Painel de Auditoria
                </h2>
                <p style="color: var(--text-muted);">Monitoramento de segurança e histórico de ações em tempo real.</p>
            </div>
            <a href="dashboard.php" class="btn" style="background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </header>

        <div class="card" style="background: #0f172a; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; color: white;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.02); text-align: left;">
                        <th style="padding: 15px; font-size: 13px; color: var(--text-muted);">Data / Hora</th>
                        <th style="padding: 15px; font-size: 13px; color: var(--text-muted);">Autor</th>
                        <th style="padding: 15px; font-size: 13px; color: var(--text-muted);">Ação</th>
                        <th style="padding: 15px; font-size: 13px; color: var(--text-muted);">Tipo</th>
                        <th style="padding: 15px; font-size: 13px; color: var(--text-muted);">Endereço IP</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($log = mysqli_fetch_assoc($resultado)): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); font-size: 14px;">
                        <td style="padding: 15px; color: #94a3b8;">
                            <i class="far fa-clock" style="margin-right: 5px; font-size: 12px;"></i>
                            <?php echo date('d/m/Y H:i', strtotime($log['data_hora'])); ?>
                        </td>
                        <td style="padding: 15px;">
                            <strong><?php echo $log['autor'] ?? 'Sistema'; ?></strong>
                            <div style="font-size: 11px; color: #64748b;"><?php echo strtoupper($log['autor_nivel'] ?? 'Auto'); ?></div>
                        </td>
                        <td style="padding: 15px;">
                            <?php 
                                $acao = $log['acao'];
                                $acao = str_replace(['Cadastrou', 'Excluiu', 'Editou'], [
                                    '<span style="color:#10b981;">Cadastrou</span>', 
                                    '<span style="color:#ef4444;">Excluiu</span>', 
                                    '<span style="color:#f59e0b;">Editou</span>'
                                ], $acao);
                                echo $acao;
                            ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php 
                                $entidade = $log['tipo_entidade'] ?? 'sistema';
                                echo "<span class='badge badge-$entidade'>$entidade</span>";
                            ?>
                        </td>
                        <td style="padding: 15px; font-family: monospace; color: #64748b; font-size: 12px;">
                            <i class="fas fa-network-wired" style="margin-right: 5px;"></i>
                            <?php echo $log['ip_acesso'] ?? '0.0.0.0'; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>