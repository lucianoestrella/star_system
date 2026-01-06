<?php
// 1. Autoload e Classes (Sempre no topo)
$path_autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($path_autoload)) {
    require_once $path_autoload;
}

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
include('../includes/conexao.php');

// 2. Verificação de Sessão (Usando a chave correta 'nivel')
if (!isset($_SESSION['usuario_id'])) {
    die("Acesso negado. Por favor, faça login.");
}

$id_usuario = $_SESSION['usuario_id'];
$nivel_usuario = $_SESSION['nivel']; // Ajustado para bater com a dashboard

// 3. Captura segura dos filtros via GET
$busca_nome = isset($_GET['busca_nome']) ? mysqli_real_escape_string($conn, $_GET['busca_nome']) : '';
$filtro_regiao = isset($_GET['filtro_regiao']) ? mysqli_real_escape_string($conn, $_GET['filtro_regiao']) : '';

// 4. Lógica de Hierarquia (Igual à Dashboard)
$where = " WHERE 1=1 ";

if ($nivel_usuario == 'coordenador') {
    $where .= " AND (u.id = '$id_usuario' OR u.coordenador_id = '$id_usuario') ";
} elseif ($nivel_usuario == 'lider') {
    $where .= " AND u.lider_id = '$id_usuario' ";
}

if ($busca_nome) $where .= " AND u.nome LIKE '%$busca_nome%' ";
if ($filtro_regiao) $where .= " AND u.regiao_administrativa = '$filtro_regiao' ";

// 5. Query
$query = "SELECT u.*, c.nome as nome_coordenador, l.nome as nome_lider 
          FROM usuarios u
          LEFT JOIN usuarios c ON u.coordenador_id = c.id
          LEFT JOIN usuarios l ON u.lider_id = l.id
          $where ORDER BY u.nome ASC";

$resultado = mysqli_query($conn, $query);

if (!$resultado) {
    die("Erro na consulta: " . mysqli_error($conn));
}

// 6. Montagem do HTML para o PDF
$html = '
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #4f46e5; color: white; padding: 8px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #ddd; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Relatório de Cadastros - Sistema Polis</h2>
        <p>Gerado por: ' . $_SESSION['nome'] . ' | Data: ' . date('d/m/Y H:i') . '</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Nível</th>
                <th>Telefone</th>
                <th>Região</th>
                <th>Vínculos</th>
            </tr>
        </thead>
        <tbody>';

while ($user = mysqli_fetch_assoc($resultado)) {
    $html .= '<tr>
        <td>' . htmlspecialchars($user['nome']) . '</td>
        <td>' . ucfirst($user['nivel']) . '</td>
        <td>' . $user['telefone'] . '</td>
        <td>' . $user['regiao_administrativa'] . '</td>
        <td>C: ' . ($user['nome_coordenador'] ?: '-') . ' / L: ' . ($user['nome_lider'] ?: '-') . '</td>
    </tr>';
}

$html .= '</tbody></table></body></html>';

// 7. Geração com Dompdf usando nomes completos
try {
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    if (ob_get_length()) ob_end_clean();

    $dompdf->stream("relatorio_polis.pdf", ["Attachment" => false]);
    exit;
} catch (Exception $e) {
    echo "Erro ao gerar PDF: " . $e->getMessage();
}