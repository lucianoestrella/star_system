<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Contato - Sistema Polis</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: #151521;
            color: white;
            font-family: sans-serif;
        }
        .contact-container {
            width: 100%;
            max-width: 450px;
            background: rgba(255,255,255,0.02);
            padding: 40px;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.05);
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
            border-radius: 8px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        label {
            display: block;
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .btn-send {
            width: 100%;
            background: #4f46e5;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-send:hover { background: #4338ca; }
    </style>
</head>
<body>

    <div class="contact-container">
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-paper-plane" style="font-size: 40px; color: #4f46e5; margin-bottom: 15px;"></i>
            <h2 style="margin: 0;">Suporte via E-mail</h2>
            <p style="color: #94a3b8; font-size: 14px; margin-top: 10px;">Conte-nos como podemos ajudar.</p>
        </div>

        <form action="../actions/enviar_contato.php" method="POST">
            <label>Seu Nome</label>
            <input type="text" name="nome" placeholder="Digite seu nome completo" required>

            <label>Seu E-mail</label>
            <input type="email" name="email" placeholder="contato@exemplo.com" required>

            <label>Mensagem / Assunto</label>
            <textarea name="mensagem" rows="5" placeholder="Descreva sua dúvida ou problema..." required></textarea>

            <button type="submit" class="btn-send">Enviar Mensagem</button>
            
            <a href="index.html" style="display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none; font-size: 13px;">
                <i class="fas fa-arrow-left"></i> Voltar para o Login
            </a>
        </form>
    </div>

</body>
</html>