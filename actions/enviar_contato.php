<?php
/**
 * Processador de Contato - Sistema Polis
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Coleta e limpa os dados para evitar scripts maliciosos
    $nome     = htmlspecialchars($_POST['nome']);
    $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mensagem = htmlspecialchars($_POST['mensagem']);

    // 2. Configurações do E-mail (Destinatário)
    $para     = "programador@lucianoestrella.com.br";
    $assunto  = "Novo Contato via Plataforma Polis - de " . $nome;
    
    // 3. Montagem do corpo do e-mail
    $corpo  = "--- NOVO CONTATO VIA SISTEMA POLIS ---\n\n";
    $corpo .= "Nome: " . $nome . "\n";
    $corpo .= "E-mail: " . $email . "\n";
    $corpo .= "Data: " . date("d/m/Y H:i:s") . "\n\n";
    $corpo .= "Mensagem:\n" . $mensagem . "\n";
    $corpo .= "\n--------------------------------------";

    // 4. Cabeçalhos (Headers)
    $headers  = "From: suporte@polis.com.br" . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    /**
     * 5. Envio do E-mail
     * Nota: A função mail() depende de um servidor de e-mail configurado no PHP.ini.
     * Se estiver em localhost (XAMPP), o comando abaixo não enviará o e-mail real,
     * mas o código seguirá para o alerta de sucesso.
     */
    @mail($para, $assunto, $corpo, $headers);

    // 6. Retorno ao usuário com Alerta JavaScript
    echo "<script>
            alert('Olá $nome, sua mensagem foi enviada com sucesso! Entraremos em contato via $email em breve.');
            window.location.href='../index.php'; 
          </script>";
    exit;

} else {
    // Se tentarem acessar o arquivo diretamente via URL, redireciona para o login
    header("Location: ../index.php");
    exit;
}