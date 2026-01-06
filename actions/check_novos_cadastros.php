<?php
session_start();
include('../includes/conexao.php');

$id_usuario = $_SESSION['usuario_id'];
$nivel_usuario = $_SESSION['nivel'];

$where = " WHERE nivel = 'pessoa' ";

if ($nivel_usuario == 'coordenador') {
    $where .= " AND (coordenador_id = '$id_usuario') ";
} elseif ($nivel_usuario == 'lider') {
    $where .= " AND lider_id = '$id_usuario' ";
}

$sql = "SELECT COUNT(*) as total FROM usuarios $where";
$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

echo json_encode(['total' => (int)$data['total']]);