<?php
include('../includes/header.php');
include('../includes/conexao.php');
include('../includes/funcoes.php');

// Segurança: RIGOROSAMENTE apenas Admin pode cadastrar Coordenadores
if (!isset($_SESSION['usuario_id']) || $_SESSION['nivel'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}
include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-masker/1.2.0/vanilla-masker.min.js"></script>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <title>Cadastrar Coordenador - Sistema Polis</title>
</head>
<body>
    <div class="container" style="max-width: 700px;">
        <header>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                <span style="background: #4f46e5; width: 12px; height: 12px; border-radius: 50%;"></span>
                <h2 style="margin: 0;">Novo Cadastro: Coordenador</h2>
            </div>
            <p style="color: var(--text-muted);">Membros de alto nível com gestão de múltiplos líderes.</p>
        </header>

        <form action="../actions/processar_cadastro.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="nivel" value="coordenador">

            <div style="background: rgba(79, 70, 229, 0.05); padding: 15px; border-radius: 8px; border: 1px dashed #4f46e5; margin-bottom: 20px; text-align: center;">
                <label style="color: var(--text-muted); display: block; margin-bottom: 10px; font-size: 14px;">Foto Oficial</label>
                <input type="file" name="foto" accept="image/*" style="font-size: 13px; color: var(--text-muted);">
            </div>

            <label style="color: var(--text-muted); font-size: 14px;">Nome Completo</label>
            <input type="text" name="nome" placeholder="Ex: Dr. Ricardo Santos" required>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">CPF</label>
                    <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" required>
                </div>
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Telefone Profissional</label>
                    <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Título de Eleitor</label>
                    <input type="text" name="titulo_eleitor" id="titulo_eleitor" placeholder="0000 0000 0000">
                </div>
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Região de Coordenação</label>
                    <select name="regiao_administrativa" required 
                        style="width: 100%; padding: 12px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; margin-bottom: 20px;">
                        <option value="">Selecione...</option>
                        <?php
                        $regioes = listarRegioes($conn);
                        while($reg = mysqli_fetch_assoc($regioes)): 
                        ?>
                            <option value="<?php echo $reg['nome']; ?>"><?php echo $reg['nome']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); margin: 20px 0;">

            <label style="color: var(--text-muted); font-size: 14px;">E-mail Corporativo</label>
            <input type="email" name="email" placeholder="coordenador@sistema.com" required>

            <label style="color: var(--text-muted); font-size: 14px;">Senha de Acesso</label>
            <input type="password" name="senha" placeholder="••••••••" required>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 2; background: #4f46e5; justify-content: center;">Confirmar Coordenador</button>
                <a href="dashboard.php" class="btn" style="flex: 1; justify-content: center; background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        function inputHandler(masks, max, event) {
            var c = event.target;
            var v = c.value.replace(/\D/g, '');
            var m = c.value.length > max ? masks[1] : masks[0];
            VMasker(c).unMask();
            VMasker(c).maskPattern(m);
            c.value = VMasker.toPattern(v, m);
        }

        // Telefone Dinâmico
        var telMask = ['(99) 9999-9999', '(99) 99999-9999'];
        var telInput = document.getElementById('telefone');
        if (telInput) {
            VMasker(telInput).maskPattern(telMask[0]);
            telInput.addEventListener('input', inputHandler.bind(undefined, telMask, 13), false);
        }

        // Título de Eleitor
        var tituloInput = document.getElementById('titulo_eleitor');
        if (tituloInput) {
            VMasker(tituloInput).maskPattern('9999 9999 9999');
        }

        // CPF
        var cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            VMasker(cpfInput).maskPattern('999.999.999-99');
        }

        // --- ALERTAS DE DUPLICIDADE ---
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('erro')) {
            const erro = urlParams.get('erro');
            let msg = "Erro ao processar cadastro.";
            
            if(erro === 'cpf_duplicado') msg = "⚠️ Este CPF já está cadastrado!";
            if(erro === 'titulo_duplicado') msg = "⚠️ Este Título de Eleitor já está cadastrado!";
            if(erro === 'email_duplicado') msg = "⚠️ Este E-mail já está em uso!";

            Toastify({
                text: msg,
                duration: 5000,
                gravity: "top",
                position: "right",
                style: { background: "linear-gradient(to right, #ef4444, #991b1b)", borderRadius: "10px" }
            }).showToast();
        }
    </script>
</body>
</html>