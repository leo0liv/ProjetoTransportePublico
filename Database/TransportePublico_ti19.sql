-- Projeto Transporte Público TI_19
-- Backup Geral do banco de dados TransportePublico_ti19 (Atualizado)

-- Configuração de Usuário e Banco
DROP USER IF EXISTS 'TransportePublico_ti19'@'localhost';
CREATE USER IF NOT EXISTS 'TransportePublico_ti19'@'localhost' IDENTIFIED BY 'senacti19';
GRANT ALL PRIVILEGES ON *.* TO 'TransportePublico_ti19'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;

DROP DATABASE IF EXISTS TransportePublico_ti19;
CREATE DATABASE IF NOT EXISTS TransportePublico_ti19 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE TransportePublico_ti19;

-- Tabela de Linhas
CREATE TABLE tblinhas (
    id_linha INT PRIMARY KEY AUTO_INCREMENT,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    operadora VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Pontos Físicos
CREATE TABLE tbpontos (
    id_ponto INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    latitude DECIMAL(10,6) NOT NULL,
    longitude DECIMAL(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Veículos
CREATE TABLE tbveiculos(
    id_veiculo INT PRIMARY KEY AUTO_INCREMENT,
    placa VARCHAR(10) UNIQUE NOT NULL,
    id_linha INT,
    capacidade INT,
    FOREIGN KEY (id_linha) REFERENCES tblinhas(id_linha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Horários Programados (Cabeçalho da Viagem)
CREATE TABLE tbhorario_programados (
    id_horario INT PRIMARY KEY AUTO_INCREMENT,
    id_linha INT NOT NULL,
    dia_semana VARCHAR(50) NOT NULL, -- Ex: 'Segunda-Sexta', 'Sabado'
    horario_partida TIME NOT NULL,
    FOREIGN KEY (id_linha) REFERENCES tblinhas(id_linha) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Rotas / Itinerários Detalhados
CREATE TABLE tbrotas (
    id_rota INT PRIMARY KEY AUTO_INCREMENT,
    id_linha INT NOT NULL,   
    id_horario INT NOT NULL, 
    id_ponto INT NOT NULL,  
    ordem INT NOT NULL,     
    
    horario_previsto TIME DEFAULT NULL, -- Que horas passa neste ponto
    tipo_ponto VARCHAR(20) DEFAULT 'meio', -- 'inicio', 'meio', 'fim'

    -- Constraints
    FOREIGN KEY (id_linha) REFERENCES tblinhas(id_linha) ON DELETE CASCADE,
    FOREIGN KEY (id_horario) REFERENCES tbhorario_programados(id_horario) ON DELETE CASCADE,
    FOREIGN KEY (id_ponto) REFERENCES tbpontos(id_ponto) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Localização em Tempo Real
CREATE TABLE tblocalizacao_tempo_real (
    id_localizacao INT PRIMARY KEY AUTO_INCREMENT,
    id_veiculo INT NOT NULL,
    latitude DECIMAL(10,6) NOT NULL,
    longitude DECIMAL(10,6) NOT NULL,
    velocidade FLOAT,
    timestamp_atualizacao DATETIME NOT NULL,
    FOREIGN KEY (id_veiculo) REFERENCES tbveiculos(id_veiculo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Previsão de Chegada
CREATE TABLE tbprevisao_chegada (
    id_previsao INT PRIMARY KEY AUTO_INCREMENT,
    id_veiculo INT NOT NULL,
    id_ponto INT NOT NULL,
    estimativa_chegada DATETIME NOT NULL,
    criado_em DATETIME NOT NULL,
    FOREIGN KEY (id_veiculo) REFERENCES tbveiculos(id_veiculo),
    FOREIGN KEY (id_ponto ) REFERENCES tbpontos(id_ponto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Usuários Administrativos
CREATE TABLE tbusuarios (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(100) NOT NULL, 
    nivel_usuario VARCHAR(20) NOT NULL DEFAULT 'comum' 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Motoristas
CREATE TABLE tbmotoristas (
    id_motorista INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    foto_url VARCHAR(255),
    data_nascimento DATE,
    telefone VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Tabela de Alocação de Motoristas
CREATE TABLE tbmotoristas_alocados (
    id_alocado INT PRIMARY KEY AUTO_INCREMENT,
    id_motorista INT NOT NULL,
    id_veiculo INT NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    data_hora_fim DATETIME NULL,
    UNIQUE (id_motorista, id_veiculo, data_hora_inicio),
    FOREIGN KEY (id_motorista) REFERENCES tbmotoristas(id_motorista),
    FOREIGN KEY (id_veiculo)   REFERENCES tbveiculos(id_veiculo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Inserção do Usuário Padrão (Admin)
INSERT INTO tbusuarios (id_usuario, nome, email, senha, nivel_usuario) 
VALUES (1, 'Administrador Master', 'admin@transporte.com', '123456', 'admin');

<<<<<<< HEAD
USE TransportePublico_ti19;
 
-- 
ALTER TABLE tbrotas DROP FOREIGN KEY tbrotas_ibfk_1;
 
--
=======
-- Remove a trava de segurança que está bloqueando a inserção
ALTER TABLE tbrotas DROP FOREIGN KEY tbrotas_ibfk_1;
 
-- Remove a coluna que não deveria estar aí
>>>>>>> 26529d29be367f7df515236976914456da29c69f
ALTER TABLE tbrotas DROP COLUMN id_linha;