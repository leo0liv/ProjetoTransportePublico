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
    longitude DECIMAL(10,6) NOT NULL,
    tipo_ponto VARCHAR(10)
        CHECK(tipo_ponto='inicio' OR tipo_ponto='meio' OR tipo_ponto='fim')
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

-- 3. INSERE O USUÁRIO 
INSERT INTO tbusuarios (id_usuario, nome, email, senha_hash) 
VALUES (1, 'Administrador Master', 'admin@transporte.com', '$2y$10$O9lMhHkR5L5h.6S.mE7vU.G/yX8S8WpL8R7Pq7z2Y.O8X7S8WpL8');