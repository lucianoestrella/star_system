<?php
include('../includes/header.php');
include('../includes/conexao.php');
include('../includes/funcoes.php');

if (!isset($_SESSION['usuario_id'])) { header('Location: ../index.php'); exit; }

$nivel_usuario = $_SESSION['nivel'];
$id_usuario = $_SESSION['usuario_id'];

// Filtros Avançados
$rua = isset($_GET['rua']) ? mysqli_real_escape_string($conn, $_GET['rua']) : '';
$bairro = isset($_GET['bairro']) ? mysqli_real_escape_string($conn, $_GET['bairro']) : '';
$secao = isset($_GET['secao']) ? mysqli_real_escape_string($conn, $_GET['secao']) : '';

// Lógica de Hierarquia
$where = " WHERE 1=1 ";
if ($nivel_usuario == 'coordenador') {
    $where .= " AND (u.id = '$id_usuario' OR u.coordenador_id = '$id_usuario') ";
} elseif ($nivel_usuario == 'lider') {
    $where .= " AND u.lider_id = '$id_usuario' ";
}

// Aplicação dos Filtros
if ($rua) $where .= " AND u.logradouro LIKE '%$rua%' ";
if ($bairro) $where .= " AND u.bairro LIKE '%$bairro%' ";
if ($secao) $where .= " AND u.secao_eleitoral = '$secao' ";

$query = "SELECT u.* FROM usuarios u $where ORDER BY u.nome ASC";
$resultado = mysqli_query($conn, $query);
include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Busca Avançada - Sistema Polis</title>
</head>
<body style="background: #151521; color: white; margin: 0; padding: 30px;">

    <div style="max-width: 1100px; margin: 0 auto;">
        
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h2 style="margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">🔍</span> Busca Avançada de Eleitores
                </h2>
                <p style="color: #94a3b8; margin: 5px 0 0;">Filtre sua base de dados por localização exata.</p>
            </div>
            <a href="dashboard.php" class="btn" style="background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                ⬅️ Voltar ao Painel
            </a>
        </header>

        <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) auto; gap: 15px; background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
            <div>
                <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Rua / Logradouro</label>
                <input type="text" name="rua" value="<?php echo $rua; ?>" placeholder="Ex: QNM 12" style="width: 100%; margin: 0;">
            </div>
            <div>
                <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Bairro</label>
                <input type="text" name="bairro" value="<?php echo $bairro; ?>" placeholder="Ex: Ceilândia Norte" style="width: 100%; margin: 0;">
            </div>
            <div>
                <label style="font-size: 12px; color: #94a3b8; display: block; margin-bottom: 5px;">Seção Eleitoral</label>
                <input type="text" name="secao" value="<?php echo $secao; ?>" placeholder="Ex: 0451" style="width: 100%; margin: 0;">
            </div>
            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary" style="height: 42px;">Filtrar</button>
                
               <a href="../actions/exportar_busca_pdf.php?rua=<?php echo urlencode($rua); ?>&bairro=<?php echo urlencode($bairro); ?>&secao=<?php echo urlencode($secao); ?>" 
   target="_blank" 
   class="btn" 
   style="background: #ef4444; height: 42px; display: flex; align-items: center; text-decoration: none; color: white; padding: 0 15px;">
   📄 Gerar Relatório
</a>

                <a href="busca_avancada.php" class="btn" style="background: #334155; height: 42px; display: flex; align-items: center;">Limpar</a>
            </div>
        </form>

        <div class="table-responsive" style="margin-top: 30px; background: rgba(255,255,255,0.02); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: rgba(255,255,255,0.05);">
                    <tr>
                        <th style="padding: 15px; text-align: left;">Nome</th>
                        <th style="padding: 15px; text-align: left;">Endereço Completo</th>
                        <th style="padding: 15px; text-align: center;">Seção</th>
                        <th style="padding: 15px; text-align: center;">WhatsApp</th>
                        <th style="padding: 15px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($resultado) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($resultado)): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 15px;">
                                <strong style="display: block;"><?php echo $row['nome']; ?></strong>
                                <small style="color: #94a3b8;"><?php echo $row['regiao_administrativa']; ?></small>
                            </td>
                            <td style="padding: 15px; font-size: 13px;">
                                <?php echo $row['logradouro'] ?: '<span style="color: #475569;">Rua não informada</span>'; ?><br>
                                <span style="color: #64748b;">Bairro: <?php echo $row['bairro'] ?: '-'; ?></span>
                            </td>
                            <td style="padding: 15px; text-align: center; font-weight: bold; color: #4f46e5;">
                                <?php echo $row['secao_eleitoral'] ?: '-'; ?>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <?php 
                                $limpo = preg_replace('/\D/', '', $row['telefone']);
                                if($limpo): ?>
                                    <a href="https://wa.me/55<?php echo $limpo; ?>" target="_blank" style="color: #22c55e; text-decoration: none; font-size: 20px;">📱</a>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="editar.php?id=<?php echo $row['id']; ?>" style="color: #4f46e5; text-decoration: none; font-size: 13px;">Ver Detalhes</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 50px; color: #64748b;">
                                Nenhuma pessoa encontrada com os filtros aplicados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>