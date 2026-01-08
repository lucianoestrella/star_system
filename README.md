🛡️ Star System
O Star System uma plataforma de gestão de usuários desenvolvida em PHP e MySQL, focada em segurança, controle de acesso e experiência do usuário. O sistema conta com recursos de proteção contra inspeção de código, troca obrigatória de senha no primeiro acesso e suporte integrado.

🚀 Funcionalidades Principais
Controle de Acesso: Sistema de login com níveis de permissão (admin e comum).

Primeiro Acesso: Redirecionamento automático para atualização de perfil e troca de senha caso o usuário tenha uma senha padrão/resetada.

Segurança Avançada: * Bloqueio de botão direito do mouse.

Bloqueio de atalhos de desenvolvedor (F12, Ctrl+Shift+I, Ctrl+U).

Criptografia de senhas com password_hash.

Suporte Integrado: * Botão direto para WhatsApp com mensagem personalizada.

Formulário de contato interno via e-mail.

Dashboard Responsiva: Interface moderna com Sidebar e visualização de dados.

📁 Estrutura de Pastas

Star_System/
├── actions/             # Processamento de formulários (Login, Reset, Contato)
├── assets/              # Arquivos estáticos (CSS, JS, Imagens)
├── includes/            # Arquivos globais (Conexão, Header, Footer)
├── views/               # Páginas protegidas (Dashboard, Perfil)
├── index.html           # Tela de login (Raiz)
└── contato.php          # Formulário de suporte

🗄️ Estrutura do Banco de Dados (SQL)
Execute o comando abaixo no seu gerenciador de banco de dados (PHPMyAdmin ou similar) para criar a estrutura compatível com a aplicação:


🛠️ Tecnologias Utilizadas
PHP 8.x (Backend e Lógica)

MySQL (Banco de dados)

CSS3 (Estilização Customizada)

JavaScript (Proteções e Interatividade)

FontAwesome (Iconografia)

Toastify JS (Notificações flutuantes)

🔧 Instalação e Configuração
Banco de Dados:

Crie um banco de dados chamado polis_db (ou o nome definido em conexao.php).

Importe a tabela usuarios com as colunas: id, nome, email, senha, nivel, foto e primeiro_acesso.

Conexão:

Ajuste as credenciais no arquivo includes/conexao.php.

Servidor:

Coloque a pasta do projeto no htdocs (XAMPP) ou www (WAMP).

Acesse via http://localhost/Star_system

🛡️ Segurança do Sistema
Para garantir a integridade dos dados, o sistema utiliza o arquivo includes/header.php para validar as sessões em todas as páginas internas. Se um usuário não estiver logado, ele é automaticamente expulso para a index.html.

👨‍💻 Desenvolvedor
Luciano Estrella

WhatsApp: (61) 99661-1472

E-mail: programador@lucianoestrella.com.br
