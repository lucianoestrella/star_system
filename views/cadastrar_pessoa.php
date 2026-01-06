<?php
include('../includes/header.php');;
include('../includes/conexao.php');
include_once('../includes/funcoes.php');

// Segurança: Qualquer utilizador logado pode cadastrar uma nova pessoa
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
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
    <title>Cadastrar Pessoa - Sistema Polis</title>
</head>
<body>
    <div class="container" style="max-width: 700px;">
        <header>
            <h2>Novo Cadastro: Pessoa</h2>
            <p style="color: var(--text-muted);">Registre um novo integrante com os dados completos abaixo.</p>
        </header>

        <form action="../actions/processar_cadastro.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="nivel" value="pessoa">

            <div style="background: rgba(255,255,255,0.03); padding: 15px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.1); margin-bottom: 20px; text-align: center;">
                <label style="color: var(--text-muted); display: block; margin-bottom: 10px; font-size: 14px;">Foto de Perfil</label>
                <input type="file" name="foto" accept="image/*" style="font-size: 13px; color: var(--text-muted);">
            </div>

            <label style="color: var(--text-muted); font-size: 14px;">Nome Completo</label>
            <input type="text" name="nome" placeholder="Ex: Maria Oliveira" required>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">CPF</label>
                    <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" required>
                </div>
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Telefone / WhatsApp</label>
                    <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" required>
                </div>
            </div>

            <label style="color: var(--text-muted); font-size: 14px;">E-mail de Acesso</label>
            <input type="email" name="email" placeholder="pessoa@exemplo.com" required>

            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); margin: 20px 0;">

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Região Administrativa</label>
                    <select name="regiao_administrativa" required 
                        style="width: 100%; padding: 12px; background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; margin-bottom: 20px;">
                        <option value="">Selecione...</option>
                        <?php
                        $regioes = listarRegioes($conn);
                        while($regiao = mysqli_fetch_assoc($regioes)): 
                        ?>
                            <option value="<?php echo $regiao['nome']; ?>">
                                <?php echo $regiao['nome']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Bairro</label>
                    <input type="text" name="bairro" placeholder="Ex: Setor O">
                </div>
            </div>

            <label style="color: var(--text-muted); font-size: 14px;">Logradouro (Rua/Quadra/Conjunto)</label>
            <input type="text" name="logradouro" placeholder="Ex: QNM 12, Conjunto A, Casa 01">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Título de Eleitor</label>
                    <input type="text" name="titulo_eleitor" id="titulo_eleitor" placeholder="0000 0000 0000">
                </div>
                <div>
                    <label style="color: var(--text-muted); font-size: 14px;">Seção Eleitoral</label>
                    <input type="text" name="secao_eleitoral" placeholder="Ex: 0142">
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.05); margin: 20px 0;">

            <label style="color: var(--text-muted); font-size: 14px;">Senha Provisória</label>
            <input type="password" name="senha" placeholder="••••••••" required>

            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn btn-primary" style="flex: 2; justify-content: center;">Finalizar Cadastro de Pessoa</button>
                <a href="dashboard.php" class="btn" style="flex: 1; justify-content: center; background: rgba(255,255,255,0.05); color: white; border: 1px solid rgba(255,255,255,0.1);">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        // --- GERENCIADOR DE MÁSCARAS ---
        function inputHandler(masks, max, event) {
            var c = event.target;
            var v = c.value.replace(/\D/g, '');
            var m = c.value.length > max ? masks[1] : masks[0];
            VMasker(c).unMask();
            VMasker(c).maskPattern(m);
            c.value = VMasker.toPattern(v, m);
        }

        // Máscara Telefone
        var telMask = ['(99) 9999-9999', '(99) 99999-9999'];
        var telInput = document.getElementById('telefone');
        if (telInput) {
            VMasker(telInput).maskPattern(telMask[0]);
            telInput.addEventListener('input', inputHandler.bind(undefined, telMask, 13), false);
        }

        // Máscara CPF
        var cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            VMasker(cpfInput).maskPattern('999.999.999-99');
        }

        // Máscara Título
        var tituloInput = document.getElementById('titulo_eleitor');
        if (tituloInput) {
            VMasker(tituloInput).maskPattern('9999 9999 9999');
        }

        // --- ALERTAS DE ERRO (DUPLICADOS) ---
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('erro')) {
            const erro = urlParams.get('erro');
            let mensagem = "Ocorreu um erro no cadastro.";
            
            if(erro === 'cpf_duplicado') mensagem = "⚠️ Este CPF já está cadastrado no sistema!";
            if(erro === 'titulo_duplicado') mensagem = "⚠️ Este Título de Eleitor já está cadastrado!";
            if(erro === 'email_duplicado') mensagem = "⚠️ Este E-mail já está em uso!";

            Toastify({
                text: mensagem,
                duration: 5000,
                close: true,
                gravity: "top",
                position: "right",
                style: { background: "linear-gradient(to right, #ff5f6d, #ffc371)", borderRadius: "8px" }
            }).showToast();
        }
    </script>
</body>
</html>