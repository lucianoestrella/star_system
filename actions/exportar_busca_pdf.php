<?php
session_start();
include('../includes/conexao.php');

if (!isset($_SESSION['usuario_id'])) { 
    die("Acesso negado."); 
}

// Captura filtros
$rua = isset($_GET['rua']) ? mysqli_real_escape_string($conn, $_GET['rua']) : '';
$bairro = isset($_GET['bairro']) ? mysqli_real_escape_string($conn, $_GET['bairro']) : '';
$secao = isset($_GET['secao']) ? mysqli_real_escape_string($conn, $_GET['secao']) : '';

// Lógica de Hierarquia (Segurança)
$nivel_usuario = $_SESSION['nivel'];
$id_usuario = $_SESSION['usuario_id'];
$where = " WHERE 1=1 ";

if ($nivel_usuario == 'coordenador') {
    $where .= " AND (coordenador_id = '$id_usuario') ";
} elseif ($nivel_usuario == 'lider') {
    $where .= " AND lider_id = '$id_usuario' ";
}

if ($rua) $where .= " AND logradouro LIKE '%$rua%' ";
if ($bairro) $where .= " AND bairro LIKE '%$bairro%' ";
if ($secao) $where .= " AND secao_eleitoral = '$secao' ";

$query = "SELECT nome, telefone, logradouro, bairro, secao_eleitoral FROM usuarios $where ORDER BY nome ASC";
$resultado = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório Polis - <?php echo date('d-m-Y'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background: #eee; }
        .no-print { 
            background: #ef4444; color: white; padding: 10px; text-align: center; 
            text-decoration: none; display: block; margin-bottom: 20px; font-weight: bold;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print();">

    <a href="javascript:void(0);" onclick="window.print();" class="no-print">
        CLIQUE AQUI SE A JANELA DE IMPRESSÃO NÃO ABRIR AUTOMATICAMENTE
    </a>

    <div class="header">
        <h1>SISTEMA POLIS - LISTA DE CAMPO</h1>
        <p>Bairro: <?php echo $bairro ?: 'Todos'; ?> | Seção: <?php echo $secao ?: 'Todas'; ?> | Rua: <?php echo $rua ?: 'Todas'; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nome do Eleitor</th>
                <th>Telefone</th>
                <th>Endereço / Bairro</th>
                <th>Seção</th>
                <th width="80">Votou?</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?php echo strtoupper($row['nome']); ?></td>
                <td><?php echo $row['telefone']; ?></td>
                <td><?php echo $row['logradouro'] . " - " . $row['bairro']; ?></td>
                <td style="text-align:center;"><?php echo $row['secao_eleitoral']; ?></td>
                <td>[ ] Sim</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>