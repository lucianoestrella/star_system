<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Login - Star System</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: #151521;
        }
        .login-container {
            margin-bottom: 100px;
        }
        footer a:hover {
            color: #4f46e5 !important;
            transition: 0.3s;
        }
        /* Efeito suave no botão */
        .btn-primary:hover {
            background: #4338ca !important;
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <form action="actions/login.php" method="POST">
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: white;">Star System</h2>
                <p style="color: #94a3b8; font-size: 14px;">Entre com suas credenciais para acessar</p>
            </div>

            <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #94a3b8;">E-mail</label>
            <input type="email" name="email" placeholder="exemplo@email.com" required 
                   style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 8px; margin-bottom: 20px;">

            <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #94a3b8;">Senha</label>
            <input type="password" name="senha" placeholder="••••••••" required 
                   style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 8px; margin-bottom: 20px;">

            <button type="submit" class="btn btn-primary" style="width: 100%; background: #4f46e5; color: white; border: none; border-radius: 8px; cursor: pointer; height: 45px; font-weight: bold; transition: 0.3s;">
                Entrar no Sistema
            </button>
        </form>
    </div>

    <footer style="position: fixed; bottom: 0; width: 100%; padding: 25px 0; text-align: center; background: rgba(0,0,0,0.3); border-top: 1px solid rgba(255,255,255,0.05); font-size: 13px; color: #64748b;">
        <div style="margin-bottom: 10px;">
            <strong style="color: #94a3b8;">&copy; 2026 Star System</strong> - Todos os direitos reservados.
        </div>
        <div style="display: flex; justify-content: center; gap: 25px;">
            <a href="https://wa.me/5561996611472?text=Olá!%20Este%20contato%20veio%20pela%20plataforma%20Polis." 
               target="_blank" 
               style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                <i class="fab fa-whatsapp" style="color: #25d366; margin-right: 8px;"></i> (61) 99661-1472
            </a>
            <a href="./views/contato.php" style="text-decoration: none; color: inherit; display: flex; align-items: center;">
                <i class="fas fa-envelope" style="color: #4f46e5; margin-right: 8px;"></i> programador@lucianoestrella.com.br
            </a>
        </div>
    </footer>

    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());
        document.addEventListener('keydown', function(event) {
            if (event.keyCode === 123 || 
                (event.ctrlKey && event.shiftKey && (event.keyCode === 73 || event.keyCode === 74 || event.keyCode === 67)) || 
                (event.ctrlKey && event.keyCode === 85) || 
                (event.ctrlKey && event.keyCode === 83)
            ) {
                event.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
