<?php
session_start();
include('../includes/conexao.php');

// Proteção de acesso: se não estiver logado, volta para o login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.html');
    exit;
}

$nivel_usuario = $_SESSION['nivel'];
$nome_logado = isset($_SESSION['nome']) ? $_SESSION['nome'] : "Usuário";

// 1. Query com JOIN para buscar nomes dos responsáveis
$query = "SELECT u.*, 
          c.nome as nome_coordenador, 
          l.nome as nome_lider 
          FROM usuarios u
          LEFT JOIN usuarios c ON u.coordenador_id = c.id
          LEFT JOIN usuarios l ON u.lider_id = l.id
          ORDER BY u.id DESC";
$resultado = mysqli_query($conn, $query);

// 2. Busca os logs recentes
$query_logs = "SELECT l.acao, l.data_hora, u.nome as autor 
               FROM logs l 
               LEFT JOIN usuarios u ON l.usuario_id = u.id 
               ORDER BY l.data_hora DESC LIMIT 5";
$resultado_logs = mysqli_query($conn, $query_logs);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Dashboard - Gestão de Usuários</title>
</head>
<body>
    <div class="container">
        <header style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <h2>Painel de Gestão</h2>
                <p style="color: var(--text-muted);">Bem-vindo, <strong><?php echo $nome_logado; ?></strong> (<?php echo ucfirst($nivel_usuario); ?>)</p>
            </div>
            <div>
                <a href="../actions/logout.php" class="btn btn-delete">Sair do Sistema</a>
            </div>
        </header>

        <div style="margin-bottom: 25px;">
            <?php if(in_array($nivel_usuario, ['admin', 'coordenador', 'lider', 'pessoa'])): ?>
                <a href="cadastrar.php" class="btn btn-primary">+ Cadastrar Novo Usuário</a>
                <a href="relatorio.php" class="btn" style="background: #0ea5e9; color: white;"> Gerar Relatório</a>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Perfil</th>
                        <th>Nome / Email</th>
                        <th>Vínculos (Coord/Líder)</th>
                        <th>Nível</th>
                        <th style="text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td>
                            <img src="../assets/uploads/<?php echo $user['foto'] ? $user['foto'] : 'default.png'; ?>" 
                                 style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.1);">
                        </td>
                        <td>
                            <strong><?php echo $user['nome']; ?></strong><br>
                            <span style="font-size: 11px; color: var(--text-muted);"><?php echo $user['email']; ?></span>
                        </td>
                        <td>
                            <div style="font-size: 11px; line-height: 1.4;">
                                <?php if($user['nome_coordenador']): ?>
                                    <span style="color: var(--info);">C:</span> <?php echo $user['nome_coordenador']; ?><br>
                                <?php endif; ?>
                                <?php if($user['nome_lider']): ?>
                                    <span style="color: var(--warning);">L:</span> <?php echo $user['nome_lider']; ?>
                                <?php endif; ?>
                                <?php if(!$user['nome_coordenador'] && !$user['nome_lider']): ?>
                                    <span style="color: var(--text-muted);">Sem vínculo</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-<?php echo $user['nivel']; ?>">
                                <?php echo ucfirst($user['nivel']); ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <?php if(in_array($nivel_usuario, ['admin', 'coordenador', 'lider'])): ?>
                                <a href="editar.php?id=<?php echo $user['id']; ?>" class="btn btn-edit" style="padding: 5px 10px; font-size: 12px;">Editar</a>
                            <?php endif; ?>

                            <?php if($nivel_usuario == 'admin'): ?>
                                <a href="../actions/excluir.php?id=<?php echo $user['id']; ?>" 
                                   class="btn btn-delete" 
                                   style="padding: 5px 10px; font-size: 12px;"
                                   onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
                                   Excluir
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <h3 style="font-size: 18px; margin-bottom: 15px;">Atividades Recentes</h3>
            <div style="background: rgba(0,0,0,0.2); border-radius: 8px; padding: 15px;">
                <?php if(mysqli_num_rows($resultado_logs) > 0): ?>
                    <?php while($log = mysqli_fetch_assoc($resultado_logs)): ?>
                        <div style="font-size: 13px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05); color: var(--text-muted);">
                            <span style="color: var(--info);"><?php echo date('d/m/Y H:i', strtotime($log['data_hora'])); ?></span> - 
                            <strong style="color: var(--text-main);"><?php echo $log['autor'] ? $log['autor'] : 'Sistema'; ?></strong> 
                            <?php echo $log['acao']; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="font-size: 13px; color: var(--text-muted);">Nenhuma atividade registrada ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>