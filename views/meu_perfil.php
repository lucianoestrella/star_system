<?php
include('../includes/header.php');
$alerta_primeiro_acesso = isset($_GET['primeiro_acesso']) ? true : false;
include('../includes/conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$query = "SELECT * FROM usuarios WHERE id = '$id_usuario'";
$res = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($res);

include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <title>Meu Perfil - Sistema Polis</title>
</head>
<body style="display: flex; background: #151521; color: white; margin: 0; min-height: 100vh; font-family: 'Inter', sans-serif;">

    <aside class="sidebar" style="width: 260px; background: #1e1e2d; position: fixed; height: 100vh; border-right: 1px solid rgba(255,255,255,0.05);">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <h2 style="color: #4f46e5; margin: 0; font-size: 22px;">Polis System</h2>
        </div>
        <nav style="padding: 20px;">
            <ul class="sidebar-menu" style="list-style: none; padding: 0;">
                <li><a href="dashboard.php" style="color: #94a3b8; text-decoration: none; display: block; padding: 12px; border-radius: 8px;">🏠 Início</a></li>
                <li><a href="meu_perfil.php" class="active" style="background: rgba(79, 70, 229, 0.1); color: #818cf8; text-decoration: none; display: block; padding: 12px; border-radius: 8px;">👤 Meu Perfil</a></li>
                <li style="margin-top: 50px;">
                    <a href="../actions/logout.php" style="color: #ef4444; text-decoration: none; display: block; padding: 12px;">🚪 Sair</a>
                </li>
            </ul>
        </nav>
    </aside>

    <main class="main-content" style="flex: 1; padding: 30px; margin-left: 260px; display: flex; justify-content: center;">
        
        <div style="width: 100%; max-width: 600px; background: rgba(255,255,255,0.02); padding: 40px; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);">
            <h2 style="margin-top: 0; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px;">Configurações de Perfil</h2>
            
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'sucesso'): ?>
                <div style="background: #10b981; color: white; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center;">
                    Perfil atualizado com sucesso!
                </div>
            <?php endif; ?>

            <form action="../actions/processar_perfil.php" method="POST" enctype="multipart/form-data">
                
                <div style="text-align: center; margin-bottom: 30px;">
                    <div style="position: relative; display: inline-block;">
                        <img src="../assets/uploads/<?php echo $user['foto'] ?: 'default.png'; ?>" 
                             style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #4f46e5;">
                    </div>
                    <p style="font-size: 13px; color: #94a3b8; margin-top: 10px;">Alterar foto de perfil</p>
                    <input type="file" name="foto" style="font-size: 12px; color: #94a3b8;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 8px;">Nome Completo</label>
                        <input type="text" value="<?php echo $user['nome']; ?>" disabled style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: #64748b; border-radius: 8px;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 8px;">E-mail</label>
                        <input type="email" value="<?php echo $user['email']; ?>" disabled style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: #64748b; border-radius: 8px;">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 8px;">Telefone de Contato</label>
                    <input type="text" name="telefone" value="<?php echo $user['telefone']; ?>" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 8px;">
                </div>

                <div style="margin-top: 30px; padding: 20px; border-radius: 12px; border: 1px solid <?php echo $alerta_primeiro_acesso ? '#f59e0b' : 'rgba(255,255,255,0.05)'; ?>; background: <?php echo $alerta_primeiro_acesso ? 'rgba(245, 158, 11, 0.05)' : 'transparent'; ?>">
                    <h4 style="margin: 0 0 15px 0; color: #f59e0b;">
                        🔐 Alterar Senha <?php if($alerta_primeiro_acesso) echo "<span style='font-size:10px; background:#f59e0b; color:black; padding:2px 6px; border-radius:4px; margin-left:10px;'>OBRIGATÓRIO</span>"; ?>
                    </h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 8px;">Nova Senha</label>
                            <input type="password" name="nova_senha" required="<?php echo $alerta_primeiro_acesso; ?>" placeholder="Mínimo 6 caracteres" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 8px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 12px; color: #94a3b8; margin-bottom: 8px;">Confirmar Senha</label>
                            <input type="password" name="confirmar_senha" required="<?php echo $alerta_primeiro_acesso; ?>" placeholder="Repita a senha" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 8px;">
                        </div>
                    </div>
                    <small style="display: block; margin-top: 10px; color: #64748b;">
                        <?php echo $alerta_primeiro_acesso ? "Crie uma senha segura para liberar seu acesso total." : "Deixe os campos de senha vazios se não desejar alterá-la."; ?>
                    </small>
                </div>

                <button type="submit" class="btn" style="width: 100%; margin-top: 30px; background: #4f46e5; border: none; color: white; padding: 15px; font-weight: bold; border-radius: 8px; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
                    Salvar Alterações
                </button>
            </form>
        </div>
    </main>

    <script>
    <?php if ($alerta_primeiro_acesso): ?>
    Toastify({
        text: "⚠️ SEGURANÇA: Por favor, altere sua senha provisória para continuar.",
        duration: 10000,
        gravity: "top",
        position: "center",
        style: { background: "linear-gradient(to right, #f59e0b, #d97706)", borderRadius: "10px", color: "white" }
    }).showToast();
    <?php endif; ?>

    // Feedback de erro se as senhas não coincidirem (opcional)
    document.querySelector('form').onsubmit = function(e) {
        const nova = document.getElementsByName('nova_senha')[0].value;
        const confirm = document.getElementsByName('confirmar_senha')[0].value;
        
        if (nova !== "" && nova !== confirm) {
            e.preventDefault();
            Toastify({
                text: "❌ As senhas não coincidem!",
                duration: 3000,
                style: { background: "#ef4444" }
            }).showToast();
        }
    };
    </script>
</body>
</html>