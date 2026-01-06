<?php
session_start();

// Correção da chave da sessão: de 'usuario_nivel' para 'nivel'
if (!isset($_SESSION['nivel']) || $_SESSION['nivel'] != 'admin') {
    die("Acesso negado. Apenas administradores podem gerar backups.");
}

include('../includes/conexao.php');

// Configuração para não expirar o tempo de execução em bancos grandes
set_time_limit(300);

$tabelas = array();
$result = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_row($result)) {
    $tabelas[] = $row[0];
}

$conteudo = "-- Backup Sistema Polis\n";
$conteudo .= "-- Data: " . date('d/m/Y H:i:s') . "\n";
$conteudo .= "-- Gerado por: " . $_SESSION['nome'] . "\n\n";

foreach ($tabelas as $tabela) {
    // Recupera a estrutura da tabela
    $result = mysqli_query($conn, "SELECT * FROM $tabela");
    $num_fields = mysqli_num_fields($result);

    $conteudo .= "DROP TABLE IF EXISTS `$tabela`;";
    $row2 = mysqli_fetch_row(mysqli_query($conn, "SHOW CREATE TABLE `$tabela`"));
    $conteudo .= "\n\n" . $row2[1] . ";\n\n";

    // Recupera os dados da tabela
    for ($i = 0; $i < $num_fields; $i++) {
        while ($row = mysqli_fetch_row($result)) {
            $conteudo .= "INSERT INTO `$tabela` VALUES(";
            for ($j = 0; $j < $num_fields; $j++) {
                if (isset($row[$j])) {
                    $val = mysqli_real_escape_string($conn, $row[$j]);
                    $conteudo .= '"' . $val . '"';
                } else {
                    $conteudo .= 'NULL';
                }
                if ($j < ($num_fields - 1)) { $conteudo .= ','; }
            }
            $conteudo .= ");\n";
        }
    }
    $conteudo .= "\n\n\n";
}

// Força o download do arquivo .sql
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"backup_polis_" . date('Y-m-d_H-i') . ".sql\"");

echo $conteudo;
exit;