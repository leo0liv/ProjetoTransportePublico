-- Inserir dados na tabela 'tblinhas'
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

INSERT INTO tblocalizacao_tempo_real (id_veiculo, latitude, longitude, velocidade, timestamp_atualizacao)
VALUES (1, -23.5917, -48.0531, 40.5, NOW());

-- Garante que a linha existe
INSERT IGNORE INTO tblinhas (id_linha, codigo, nome) VALUES (1, '101-A', 'Terminal - Vila Rio Branco');

-- Garante que os pontos existem
INSERT IGNORE INTO tbpontos (id_ponto, nome, latitude, longitude) VALUES 
(101, 'Terminal', -23.5850, -48.0450),
(102, 'Centro', -23.5917, -48.0531),
(103, 'Bairro', -23.6000, -48.0600);

-- Vincula os pontos à linha (A ROTA)
INSERT IGNORE INTO tbrotas (id_linha, id_ponto, ordem) VALUES (1, 101, 1), (1, 102, 2), (1, 103, 3);

-- Adiciona um veículo e uma localização para ele aparecer
INSERT IGNORE INTO tbveiculos (id_veiculo, placa, id_linha) VALUES (1, 'ABC-1234', 1);
INSERT INTO tblocalizacao_tempo_real (id_veiculo, latitude, longitude, velocidade, timestamp_atualizacao) 
VALUES (1, -23.5917, -48.0531, 0, NOW());