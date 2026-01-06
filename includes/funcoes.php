<?php
function registrarLog($conn, $usuario_id, $acao) {
    $acao = mysqli_real_escape_string($conn, $acao);
    $sql = "INSERT INTO logs (usuario_id, acao) VALUES ('$usuario_id', '$acao')";
    mysqli_query($conn, $sql);
}

function listarRegioes($conn) {
    $sql = "SELECT * FROM regioes ORDER BY nome ASC";
    return mysqli_query($conn, $sql);
}
?>