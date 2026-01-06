CREATE DATABASE sistema_gestao;
USE sistema_gestao;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    nivel ENUM('admin', 'coordenador', 'lider', 'pessoa') NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nome, email, senha, nivel) 
VALUES ('Admin Teste', 'admin@teste.com', '$2y$10$8W3jB5N9p1Y.R8U8p1e7u.K8gH8Y8y8Y8y8Y8y8Y8y8Y8y8Y8y8Y8y', 'admin');

-- Tabela para registar o que acontece no sistema
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao TEXT,
    data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Adicionar coluna para o nome do arquivo da foto
ALTER TABLE usuarios ADD COLUMN foto VARCHAR(255) DEFAULT 'default.png';

ALTER TABLE usuarios 
ADD COLUMN coordenador_id INT NULL,
ADD COLUMN lider_id INT NULL;

-- Adicionar chaves estrangeiras para manter a integridade
ALTER TABLE usuarios 
ADD CONSTRAINT fk_coordenador FOREIGN KEY (coordenador_id) REFERENCES usuarios(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_lider FOREIGN KEY (lider_id) REFERENCES usuarios(id) ON DELETE SET NULL;

INSERT INTO regioes_administrativas (nome) VALUES
('Plano Piloto'),
('Gama'),
('Taguatinga'),
('Brazlândia'),
('Sobradinho'),
('Planaltina'),
('Paranoá'),
('Núcleo Bandeirante'),
('Ceilândia'),
('Guará'),
('Cruzeiro'),
('Samambaia'),
('Santa Maria'),
('São Sebastião'),
('Recanto das Emas'),
('Lago Sul'),
('Riacho Fundo'),
('Lago Norte'),
('Candangolândia'),
('Águas Claras'),
('Riacho Fundo 2'),
('Sudoeste/Octogonal'),
('Varjão'),
('Park Way'),
('Estrutural/Scia'),
('Sobradinho II'),
('Jardim Botânico'),
('Itapoã'),
('SIA'),
('Vicente Pires'),
('Fercal'),
('Sol Nascente/Pôr do Sol'),
('Arniqueira');

CREATE TABLE tentativas_login (
    ip_address VARCHAR(45) NOT NULL,
    tentativas INT NOT NULL DEFAULT 1,
    ultimo_acesso TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (ip_address)
);

CREATE TABLE IF NOT EXISTS configuracoes (
    chave VARCHAR(50) PRIMARY KEY,
    valor VARCHAR(255)
);

INSERT INTO configuracoes (chave, valor) VALUES ('meta_cadastros', '100')
ON DUPLICATE KEY UPDATE valor = valor;

-- Adiciona o CPF caso não exista e remove duplicados acidentais no futuro
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS cpf VARCHAR(14) AFTER email;
ALTER TABLE usuarios ADD UNIQUE (cpf);
ALTER TABLE usuarios ADD UNIQUE (titulo_eleitor);

ALTER TABLE usuarios ADD COLUMN primeiro_acesso TINYINT(1) DEFAULT 1;