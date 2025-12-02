-- Projeto Transporte Público TI_19
-- Backup Geral do banco de dados TransportePublico_ti19
-- Excluir o usuário TransportePublico_ti19 caso ele exista
DROP USER IF EXISTS 'TransportePublico_ti19'@'localhost';

-- Criar o usuário TransportePublico_ti19 se ele não existir
CREATE USER IF NOT EXISTS 'TransportePublico_ti19'@'localhost'
    IDENTIFIED BY 'senacti19';
GRANT ALL PRIVILEGES ON *.* TO 'TransportePublico_ti19'@'localhost'
    WITH GRANT OPTION;
    FLUSH PRIVILEGES;

-- Excluir o banco de dados TransportePublico_ti19 caso ele exista
DROP DATABASE IF EXISTS TransportePublico_ti19;

-- Criar o banco de dados TransportePublico_ti19 se ele não existir
CREATE DATABASE IF NOT EXISTS TransportePublico_ti19
    DEFAULT CHARACTER SET utf8
    COLLATE utf8_general_ci;

-- Usamos o banco de dados TransportePublico_ti19
USE TransportePublico_ti19;

-- Estrutura de tabela tblinhas
CREATE TABLE tblinhas (
    id_linha INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    operadora VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbpontos
CREATE TABLE tbpontos (
    id_ponto INT(11) PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,6) NOT NULL,
    longitude DECIMAL(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbveiculos
CREATE TABLE tbveiculos(
    id_veiculo INT PRIMARY KEY AUTO_INCREMENT,
    placa VARCHAR(10) UNIQUE NOT NULL,
    id_linha INT,
    capacidade INT,

    -- Chave estrangeira
    FOREIGN KEY (id_linha) REFERENCES tblinhas(id_linha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbrotas
CREATE TABLE tbrotas (
    id_rota INT PRIMARY KEY AUTO_INCREMENT,
    id_linha INT NOT NULL,
    id_ponto INT NOT NULL,
    ordem INT NOT NULL,

    UNIQUE (id_linha, id_ponto),

    -- chave estrangeira
    FOREIGN KEY (id_linha) REFERENCES tblinhas(id_linha),
    FOREIGN KEY (id_ponto) REFERENCES tbpontos(id_ponto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbhorario_programados
CREATE TABLE tbhorario_programados (
    id_horario INT PRIMARY KEY AUTO_INCREMENT,
    id_linha INT NOT NULL,
    dia_semana VARCHAR(10) NOT NULL,
    horario_partida TIME NOT NULL,

    -- chave estrangeira
    FOREIGN KEY (id_linha) REFERENCES tblinhas(id_linha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tblocalizacao_tempo_real
CREATE TABLE tblocalizacao_tempo_real (
    id_localizacao INT PRIMARY KEY AUTO_INCREMENT,
    id_veiculo INT NOT NULL,
    latitude DECIMAL(10,6) NOT NULL,
    longitude DECIMAL(10,6) NOT NULL,
    velocidade FLOAT,
    timestamp_atualizacao DATETIME NOT NULL,

    -- chave estrangeira
    FOREIGN KEY (id_veiculo) REFERENCES tbveiculos(id_veiculo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbprevisao_chegada
CREATE TABLE tbprevisao_chegada (
    id_previsao INT PRIMARY KEY AUTO_INCREMENT,
    id_veiculo INT NOT NULL,
    id_ponto INT NOT NULL,
    estimativa_chegada DATETIME NOT NULL,
    criado_em DATETIME NOT NULL,

    -- Chave estrangeira
    FOREIGN KEY (id_veiculo) REFERENCES tbveiculos(id_veiculo),
    FOREIGN KEY (id_ponto ) REFERENCES tbpontos(id_ponto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbusuarios
CREATE TABLE tbusuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbmotoristas
CREATE TABLE tbmotoristas (
    id_motorista INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    foto_url VARCHAR(255),
    data_nascimento DATE,
    telefone VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Estrutura de tabela tbmotoristas_alocados
CREATE TABLE tbmotoristas_alocados (
    id_alocado INT PRIMARY KEY AUTO_INCREMENT,
    id_motorista INT NOT NULL,
    id_veiculo INT NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    data_hora_fim DATETIME NULL,

    UNIQUE (id_motorista, id_veiculo, data_hora_inicio),

    -- chave estrangeira
    FOREIGN KEY (id_motorista) REFERENCES tbmotoristas(id_motorista),
    FOREIGN KEY (id_veiculo)   REFERENCES tbveiculos(id_veiculo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- Inserir dados na tabela 'tblinhas'
-- 1. Insere a Linha de Teste
INSERT INTO tblinhas (codigo, nome) VALUES ('101-A', 'Terminal - Vila Rio Branco');

-- Inserir dados na tabela 'tbpontos'
-- Ponto 1: Terminal (Partida)
INSERT INTO tbpontos (id_ponto, nome, latitude, longitude) 
VALUES (101, 'Terminal Rodoviário Central', '-23.5850', '-48.0450');

-- Ponto 2: Centro (Ponto de Passagem)
INSERT INTO tbpontos (id_ponto, nome, latitude, longitude) 
VALUES (102, 'Catedral Nossa Senhora dos Prazeres', '-23.5917', '-48.0531');

-- Ponto 3: Bairro Final (Destino)
INSERT INTO tbpontos (id_ponto, nome, latitude, longitude) 
VALUES (103, 'Bairro Vila Rio Branco', '-23.6000', '-48.0600');

-- Inserir dados na tabela 'tbrotas'
-- Ordem 1: Terminal
INSERT INTO tbrotas (id_linha, id_ponto, ordem) VALUES (1, 101, 1);
-- Ordem 2: Catedral
INSERT INTO tbrotas (id_linha, id_ponto, ordem) VALUES (1, 102, 2);
-- Ordem 3: Destino
INSERT INTO tbrotas (id_linha, id_ponto, ordem) VALUES (1, 103, 3);

-- Inserir dados na tabela 'tbveiculos'
INSERT INTO tbveiculos (placa, id_linha, capacidade)
VALUES ('ABC-1234', 1, 40);

-- Inserir dados na tabela 'tbmotoristas'
INSERT INTO tbmotoristas (nome, cpf, telefone)
VALUES ('João da Silva', '123.456.789-00', '15999999999');

-- Inserir dados na tabela 'tbhorario_programados'
INSERT INTO tbhorario_programados (id_linha, dia_semana, horario_partida)
VALUES (1, 'segunda', '06:00:00');