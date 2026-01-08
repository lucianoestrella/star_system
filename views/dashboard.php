<?php
include('../includes/header.php');
include('../includes/conexao.php');
include('../includes/funcoes.php');

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../index.php');
    exit;
}

$id_usuario = $_SESSION['usuario_id'];
$nivel_usuario = $_SESSION['nivel'];
$nome_logado = isset($_SESSION['nome']) ? $_SESSION['nome'] : "Usuário";
$foto_logado = isset($_SESSION['foto']) ? $_SESSION['foto'] : "default.png";

// --- PROCESSAR ALTERAÇÃO DE META (SOMENTE ADMIN) ---
if ($nivel_usuario == 'admin' && isset($_POST['atualizar_meta'])) {
    $nova_meta = mysqli_real_escape_string($conn, $_POST['nova_meta']);
    mysqli_query($conn, "UPDATE configuracoes SET valor = '$nova_meta' WHERE chave = 'meta_cadastros'");
    header("Location: dashboard.php?sucesso_meta=1");
    exit;
}

// --- BUSCAR META ATUAL ---
$res_meta = mysqli_query($conn, "SELECT valor FROM configuracoes WHERE chave = 'meta_cadastros'");
$meta_row = mysqli_fetch_assoc($res_meta);
$meta_cadastros = (isset($meta_row['valor'])) ? (int)$meta_row['valor'] : 100;

// --- LÓGICA DE FILTROS ---
$busca_nome = isset($_GET['busca_nome']) ? mysqli_real_escape_string($conn, $_GET['busca_nome']) : '';
$filtro_regiao = isset($_GET['filtro_regiao']) ? mysqli_real_escape_string($conn, $_GET['filtro_regiao']) : '';

$where = " WHERE 1=1 ";
if ($nivel_usuario == 'coordenador') {
    $where .= " AND (u.id = '$id_usuario' OR u.coordenador_id = '$id_usuario') ";
} elseif ($nivel_usuario == 'lider') {
    $where .= " AND u.lider_id = '$id_usuario' ";
}
if ($busca_nome) $where .= " AND u.nome LIKE '%$busca_nome%' ";
if ($filtro_regiao) $where .= " AND u.regiao_administrativa = '$filtro_regiao' ";

// --- COMPARATIVO: HOJE vs ONTEM ---
$hoje = date('Y-m-d');
$ontem = date('Y-m-d', strtotime("-1 day"));

$total_hoje = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios u $where AND DATE(u.data_cadastro) = '$hoje' AND u.nivel = 'pessoa'"))['total'];
$total_ontem = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios u $where AND DATE(u.data_cadastro) = '$ontem' AND u.nivel = 'pessoa'"))['total'];

// --- ESTATÍSTICAS GERAIS ---
if ($nivel_usuario == 'admin') {
    $total_coordenadores = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios WHERE nivel = 'coordenador'"))['total'];
    $total_lideres = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios WHERE nivel = 'lider'"))['total'];
    $total_pessoas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios WHERE nivel = 'pessoa'"))['total'];
} else {
    $vinculo_sql = ($nivel_usuario == 'coordenador') ? "coordenador_id = '$id_usuario'" : "lider_id = '$id_usuario'";
    $total_coordenadores = ($nivel_usuario == 'coordenador') ? 1 : 0;
    $total_lideres = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios WHERE nivel = 'lider' AND $vinculo_sql"))['total'];
    $total_pessoas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios WHERE nivel = 'pessoa' AND $vinculo_sql"))['total'];
}

// --- DADOS GRÁFICOS SEMANAL ---
$labels_dias = []; $dados_dias = [];
for ($i = 6; $i >= 0; $i--) {
    $data_loop = date('Y-m-d', strtotime("-$i days"));
    $labels_dias[] = date('d/m', strtotime($data_loop));
    $res_dia = mysqli_query($conn, "SELECT COUNT(*) as total FROM usuarios u $where AND DATE(u.data_cadastro) = '$data_loop' AND u.nivel = 'pessoa'");
    $dados_dias[] = (int)mysqli_fetch_assoc($res_dia)['total'];
}

// Top Bairros
$res_bairros = mysqli_query($conn, "SELECT bairro, COUNT(*) as total FROM usuarios u $where AND nivel = 'pessoa' AND bairro != '' GROUP BY bairro ORDER BY total DESC LIMIT 5");
$labels_bairros = []; $dados_bairros = [];
while($row = mysqli_fetch_assoc($res_bairros)) { $labels_bairros[] = $row['bairro']; $dados_bairros[] = $row['total']; }

// Ranking Coordenadores
$res_ranking = mysqli_query($conn, "SELECT c.nome as coordenador, COUNT(u.id) as total FROM usuarios u INNER JOIN usuarios c ON u.coordenador_id = c.id WHERE u.nivel = 'pessoa' GROUP BY u.coordenador_id ORDER BY total DESC LIMIT 5");

// Tabela Recentes
$query_tab = "SELECT u.*, c.nome as nome_coordenador, l.nome as nome_lider FROM usuarios u LEFT JOIN usuarios c ON u.coordenador_id = c.id LEFT JOIN usuarios l ON u.lider_id = l.id $where ORDER BY u.id DESC LIMIT 8";
$resultado = mysqli_query($conn, $query_tab);
include('../includes/footer.php');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Dashboard - Star System</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>
<body style="display: flex; background: #151521; color: white; margin: 0; min-height: 100vh; font-family: 'Inter', sans-serif;">

    <aside class="sidebar" style="width: 260px; background: #1e1e2d; position: fixed; height: 100vh; overflow-y: auto; border-right: 1px solid rgba(255,255,255,0.05);">
        <div style="padding: 25px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05);">
            <h2 style="color: #4f46e5; margin: 0; font-size: 22px; letter-spacing: 1px;">POLIS SYSTEM</h2>
        </div>
        <nav style="padding: 15px;">
            <ul class="sidebar-menu" style="list-style: none; padding: 0;">
                <li style="color: #64748b; font-size: 10px; text-transform: uppercase; padding: 15px 10px 5px;">Menu Principal</li>
                <li><a href="dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Início</a></li>
                <li><a href="busca_avancada.php"><i class="fas fa-search"></i> Busca Avançada</a></li>
                
                <?php if($nivel_usuario == 'admin'): ?>
                    <li style="margin-top: 15px; color: #64748b; font-size: 10px; text-transform: uppercase; padding: 10px 10px 5px;">Administração</li>
                    <li><a href="cadastrar_coordenador.php"><i class="fas fa-user-tie"></i> Coordenadores</a></li>
                    <li><a href="logs.php"><i class="fas fa-shield-alt"></i> Auditoria de Logs</a></li>
                    <li><a href="#" onclick="document.getElementById('modalMeta').style.display='flex'"><i class="fas fa-bullseye"></i> Definir Meta Global</a></li>
                <?php endif; ?>
                
                <li style="margin-top: 15px; color: #64748b; font-size: 10px; text-transform: uppercase; padding: 10px 10px 5px;">Operacional</li>
                <?php if(in_array($nivel_usuario, ['admin', 'coordenador'])): ?>
                    <li><a href="cadastrar_lider.php"><i class="fas fa-star"></i> Líderes</a></li>
                    <li><a href="gerenciar_regioes.php"><i class="fas fa-map-marker-alt"></i> Regiões</a></li>
                <?php endif; ?>
                <li><a href="cadastrar_pessoa.php"><i class="fas fa-user-plus"></i> Cadastrar Pessoa</a></li>
                
                <li style="margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px;">
                    <a href="../actions/logout.php" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Sair do Sistema</a>
                </li>
            </ul>
        </nav>
    </aside>

    <main style="flex: 1; padding: 30px; margin-left: 260px;">
        <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="margin:0; font-size: 24px; font-weight: 600;">Dashboard</h1>
                <p style="color: #94a3b8; margin: 5px 0 0;">Bem-vindo, <strong><?php echo $nome_logado; ?></strong></p>
            </div>
            
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="background: #1e1e2d; border: 1px solid rgba(255,255,255,0.05); padding: 5px 15px; border-radius: 10px; display: flex; align-items: center; gap: 15px; margin-right: 5px;">
                    <div style="text-align: center;">
                        <small style="display: block; color: #10b981; font-size: 9px; text-transform: uppercase;">Hoje</small>
                        <span style="color: #10b981; font-weight: bold;"><?php echo $total_hoje; ?></span>
                    </div>
                    <div style="width: 1px; height: 25px; background: rgba(255,255,255,0.1);"></div>
                    <div style="text-align: center;">
                        <small style="display: block; color: #94a3b8; font-size: 9px; text-transform: uppercase;">Ontem</small>
                        <span style="color: #64748b; font-weight: bold;"><?php echo $total_ontem; ?></span>
                    </div>
                </div>

                <?php if($nivel_usuario == 'admin'): ?>
                    <a href="../actions/backup_banco.php" class="btn-acao" style="background: #10b981;" title="Fazer Backup">
                        <i class="fas fa-database"></i> Backup
                    </a>
                <?php endif; ?>

                <a href="../actions/exportar_pdf.php?busca_nome=<?php echo $busca_nome; ?>&filtro_regiao=<?php echo $filtro_regiao; ?>" target="_blank" class="btn-acao" style="background: #ef4444;" title="Exportar PDF">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>

                <?php if($nivel_usuario == 'admin'): ?>
                    <a href="logs.php" class="btn-acao" style="background: #4f46e5;" title="Ver Logs">
                        <i class="fas fa-history"></i> Logs
                    </a>
                <?php endif; ?>
                
                <a href="meu_perfil.php" title="Meu Perfil" style="display: flex; align-items: center;">
                    <img src="../assets/uploads/<?php echo $foto_logado; ?>" style="width: 42px; height: 42px; border-radius: 10px; border: 2px solid #4f46e5; object-fit: cover; transition: 0.3s;" onmouseover="this.style.borderColor='#818cf8'" onmouseout="this.style.borderColor='#4f46e5'">
                </a>
            </div>
        </header>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <?php if($nivel_usuario == 'admin' || $nivel_usuario == 'coordenador'): ?>
            <div class="stat-card" style="background: #1e1e2d; padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                <div style="color: #4f46e5; margin-bottom: 10px;"><i class="fas fa-user-tie fa-lg"></i></div>
                <small style="color: #94a3b8;">Coordenadores</small>
                <div style="font-size: 26px; font-weight: bold; margin-top: 5px;"><?php echo $total_coordenadores; ?></div>
            </div>
            <div class="stat-card" style="background: #1e1e2d; padding: 20px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.05);">
                <div style="color: #7c3aed; margin-bottom: 10px;"><i class="fas fa-star fa-lg"></i></div>
                <small style="color: #94a3b8;">Líderes Ativos</small>
                <div style="font-size: 26px; font-weight: bold; margin-top: 5px;"><?php echo $total_lideres; ?></div>
            </div>
            <?php endif; ?>
            <div class="stat-card" style="background: #1e1e2d; padding: 20px; border-radius: 15px; border: 1px solid rgba(16, 185, 129, 0.2);">
                <div style="color: #10b981; margin-bottom: 10px;"><i class="fas fa-users fa-lg"></i></div>
                <small style="color: #94a3b8;">Eleitores na Base</small>
                <div style="font-size: 26px; font-weight: bold; margin-top: 5px;"><?php echo $total_pessoas; ?></div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
            <div class="card-painel" style="background: #1e1e2d; padding: 25px; border-radius: 15px;">
                <h3 style="margin:0 0 20px; font-size:15px; color: #94a3b8;">Desempenho da Semana</h3>
                <canvas id="graficoSemanal" height="110"></canvas>
            </div>
            <div class="card-painel" style="background: #1e1e2d; padding: 25px; border-radius: 15px;">
                <h3 style="margin:0 0 20px; font-size:15px; color: #94a3b8;">Top Bairros</h3>
                <div style="height: 220px;"><canvas id="mapaCalorBairros"></canvas></div>
            </div>
        </div>

        <div style="background: #1e1e2d; padding: 25px; border-radius: 15px; margin-bottom:30px; border: 1px solid rgba(79, 70, 229, 0.1);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                <h3 style="margin:0; font-size:16px;">Progresso de Metas por Coordenador</h3>
                <span style="font-size: 12px; color: #4f46e5; background: rgba(79, 70, 229, 0.1); padding: 4px 10px; border-radius: 20px;">Meta Global: <?php echo $meta_cadastros; ?></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
                <?php $pos=1; while($rank = mysqli_fetch_assoc($res_ranking)): 
                    $perc = min(($rank['total'] / $meta_cadastros) * 100, 100);
                ?>
                <div style="background: rgba(255,255,255,0.02); padding: 15px; border-radius: 10px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:13px;">
                        <span><strong><?php echo $pos."º"; ?></strong> <?php echo $rank['coordenador']; ?></span>
                        <span style="color:#10b981;"><?php echo $rank['total']; ?> <small style="color:#64748b;">/ <?php echo $meta_cadastros; ?></small></span>
                    </div>
                    <div style="height:6px; background:rgba(255,255,255,0.05); border-radius:10px; overflow:hidden;">
                        <div style="width:<?php echo $perc; ?>%; height:100%; background: linear-gradient(to right, #4f46e5, #10b981); transition: 1s ease-in-out;"></div>
                    </div>
                </div>
                <?php $pos++; endwhile; ?>
            </div>
        </div>

        <div style="background: #1e1e2d; border-radius: 15px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
            <div style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between;">
                <h3 style="margin:0; font-size:15px;">Últimos Cadastros</h3>
                <a href="busca_avancada.php" style="color:#4f46e5; font-size:12px; text-decoration:none;">Ver todos →</a>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; color: #64748b; font-size: 12px; background: rgba(0,0,0,0.1);">
                        <th style="padding: 15px;">ELEITOR</th>
                        <th style="padding: 15px;">LOCALIZAÇÃO</th>
                        <th style="padding: 15px;">VÍNCULO</th>
                        <th style="padding: 15px; text-align: center;">AÇÕES</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    <?php while($user = mysqli_fetch_assoc($resultado)): ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.02);">
                        <td style="padding: 12px 15px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="../assets/uploads/<?php echo $user['foto'] ?: 'default.png'; ?>" style="width: 35px; height: 35px; border-radius: 8px; object-fit: cover;">
                                <div><strong><?php echo $user['nome']; ?></strong><br><small style="color: #64748b; font-size: 10px;"><?php echo $user['cpf'] ?? 'Sem CPF'; ?></small></div>
                            </div>
                        </td>
                        <td style="padding: 15px;">
                            <?php echo $user['regiao_administrativa']; ?><br><small style="color: #64748b;"><?php echo $user['bairro']; ?></small>
                        </td>
                        <td style="padding: 15px; color: #94a3b8; font-size: 11px;">
                            <i class="fas fa-link" style="font-size: 9px;"></i> L: <?php echo $user['nome_lider'] ?: 'Direto'; ?>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="editar.php?id=<?php echo $user['id']; ?>" style="color: #4f46e5; margin-right: 10px;" title="Editar"><i class="fas fa-edit"></i></a>
                            
                            <?php if($nivel_usuario == 'admin'): ?>
                            <a href="#" onclick="confirmarReset('<?php echo $user['id']; ?>', '<?php echo $user['nome']; ?>')" style="color: #f59e0b; margin-right: 10px;" title="Resetar Senha">
                                <i class="fas fa-key"></i>
                            </a>
                            <?php endif; ?>

                            <a href="https://wa.me/55<?php echo preg_replace('/\D/', '', $user['telefone']); ?>" target="_blank" style="color: #10b981;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalMeta" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; justify-content: center; align-items: center; backdrop-filter: blur(5px);">
        <div style="background: #1e1e2d; padding: 30px; border-radius: 15px; width: 340px; border: 1px solid #4f46e5;">
            <h3 style="margin-top:0; color: white;">Ajustar Meta Global</h3>
            <form method="POST">
                <input type="number" name="nova_meta" value="<?php echo $meta_cadastros; ?>" required style="width: 100%; padding: 12px; background: #151521; border: 1px solid #333; color: white; border-radius: 8px; margin-bottom: 20px; font-size: 18px; text-align: center;">
                <div style="display:flex; gap:10px;">
                    <button type="button" onclick="document.getElementById('modalMeta').style.display='none'" style="flex:1; padding:12px; border-radius:8px; background: #333; color: white; border: none; cursor: pointer;">Cancelar</button>
                    <button type="submit" name="atualizar_meta" style="flex:1; padding:12px; background:#4f46e5; border:none; color:white; border-radius:8px; cursor: pointer; font-weight: bold;">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // --- ADIÇÃO: FUNÇÃO DE RESET DE SENHA ---
    function confirmarReset(id, nome) {
        if (confirm("Deseja realmente resetar a senha de " + nome + " para '123456'?")) {
            window.location.href = "../actions/resetar_senha.php?id=" + id;
        }
    }

    // --- ADIÇÃO: TOASTS DE FEEDBACK ---
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('reset_sucesso')) {
        Toastify({
            text: "✅ Senha resetada para: Polis@2026",
            duration: 5000,
            style: { background: "#10b981", borderRadius: "10px" }
        }).showToast();
    }
    if (urlParams.has('reset_erro')) {
        Toastify({
            text: "❌ Erro ao resetar senha",
            duration: 5000,
            style: { background: "#ef4444", borderRadius: "10px" }
        }).showToast();
    }
    if (urlParams.has('sucesso_meta')) {
        Toastify({
            text: "🎯 Meta global atualizada!",
            duration: 3000,
            style: { background: "#4f46e5", borderRadius: "10px" }
        }).showToast();
    }

    new Chart(document.getElementById('graficoSemanal'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels_dias); ?>,
            datasets: [{
                label: 'Cadastros',
                data: <?php echo json_encode($dados_dias); ?>,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                borderWidth: 3,
                fill: true, tension: 0.4,
                pointRadius: 4, pointBackgroundColor: '#4f46e5'
            }]
        },
        options: { 
            plugins: { legend: { display: false } }, 
            scales: { 
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#64748b' } },
                x: { grid: { display: false }, ticks: { color: '#64748b' } }
            } 
        }
    });

    new Chart(document.getElementById('mapaCalorBairros'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels_bairros); ?>,
            datasets: [{
                data: <?php echo json_encode($dados_bairros); ?>,
                backgroundColor: '#10b981', borderRadius: 4
            }]
        },
        options: { 
            indexAxis: 'y', 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#64748b' } },
                y: { ticks: { color: '#64748b' } }
            }
        }
    });

    let ultimoTotal = <?php echo $total_pessoas; ?>;
    function verificar() {
        fetch('../actions/check_novos_cadastros.php')
            .then(res => res.json())
            .then(data => {
                if (data.total > ultimoTotal) {
                    Toastify({
                        text: "🔔 NOVO CADASTRO RECEBIDO",
                        duration: 5000,
                        style: { background: "linear-gradient(to right, #4f46e5, #7c3aed)", borderRadius: "10px" },
                        onClick: () => location.reload()
                    }).showToast();
                    ultimoTotal = data.total;
                }
            });
    }
    setInterval(verificar, 10000);
    </script>

    <style>
        .btn-acao { color: white; text-decoration: none; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; transition: 0.3s; display: flex; align-items: center; gap: 8px; }
        .sidebar-menu a { color: #94a3b8; text-decoration: none; display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 10px; transition: 0.3s; margin-bottom: 2px; font-size: 14px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(79, 70, 229, 0.1); color: #818cf8; }
    </style>
</body>
</html>
