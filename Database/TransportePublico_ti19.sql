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
    DEFAULT CHATACTER SET utf8
    COLLATE utf8_general_ci;

-- Usamos o banco de dados TransportePublico_ti19
USE TransportePublico_ti19

-- Estrutura de tabela tblinha
