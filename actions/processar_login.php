$ip = $_SERVER['REMOTE_ADDR'];

// Verificar se o IP está bloqueado (ex: mais de 5 tentativas nos últimos 15 min)
$check_bloqueio = mysqli_query($conn, "SELECT * FROM tentativas_login WHERE ip_address = '$ip' AND tentativas >= 5 AND ultimo_acesso > NOW() - INTERVAL 15 MINUTE");

if (mysqli_num_rows($check_bloqueio) > 0) {
    die("Acesso bloqueado por 15 minutos devido a múltiplas tentativas falhas.");
}

// Se o login falhar:
if (!$usuario_valido) {
    mysqli_query($conn, "INSERT INTO tentativas_login (ip_address, tentativas) VALUES ('$ip', 1) ON DUPLICATE KEY UPDATE tentativas = tentativas + 1, ultimo_acesso = NOW()");
} else {
    // Se o login tiver sucesso, limpamos as tentativas
    mysqli_query($conn, "DELETE FROM tentativas_login WHERE ip_address = '$ip'");
}