-- Banco de dados: `transportepublico_ti19`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbhorario_programados`
--

CREATE TABLE `tbhorario_programados` (
  `id_horario` int(11) NOT NULL,
  `id_linha` int(11) NOT NULL,
  `dia_semana` varchar(50) NOT NULL,
  `horario_partida` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tbhorario_programados`
--

INSERT INTO `tbhorario_programados` (`id_horario`, `id_linha`, `dia_semana`, `horario_partida`) VALUES
(1, 1, 'Segunda-Sexta', '06:00:00'),
(2, 2, 'Segunda-Sexta', '06:35:00'),
(3, 1, 'Domingo/Feriados', '06:00:00'),
(4, 2, 'Domingo/Feriados', '06:35:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tblinhas`
--

CREATE TABLE `tblinhas` (
  `id_linha` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `operadora` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tblinhas`
--

INSERT INTO `tblinhas` (`id_linha`, `codigo`, `nome`, `operadora`) VALUES
(1, '01-A', 'Rodoviaria - Mercado Municipal', 'Prefeitura'),
(2, '01-B', 'Mercado Municipal - Rodoviaria', 'Prefeitura');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tblocalizacao_tempo_real`
--

CREATE TABLE `tblocalizacao_tempo_real` (
  `id_localizacao` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL,
  `velocidade` float DEFAULT NULL,
  `timestamp_atualizacao` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbmotoristas`
--

CREATE TABLE `tbmotoristas` (
  `id_motorista` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tbmotoristas`
--

INSERT INTO `tbmotoristas` (`id_motorista`, `nome`, `cpf`, `foto_url`, `data_nascimento`, `telefone`) VALUES
(1, 'Walter Hartwell White', '07207207200', '1771550130_interferências 2.png', '1901-01-01', '40028922');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbmotoristas_alocados`
--

CREATE TABLE `tbmotoristas_alocados` (
  `id_alocado` int(11) NOT NULL,
  `id_motorista` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `data_hora_inicio` datetime NOT NULL,
  `data_hora_fim` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbpontos`
--

CREATE TABLE `tbpontos` (
  `id_ponto` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `latitude` decimal(10,6) NOT NULL,
  `longitude` decimal(10,6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tbpontos`
--

INSERT INTO `tbpontos` (`id_ponto`, `nome`, `latitude`, `longitude`) VALUES
(1, 'Rodoviaria', -23.581436, -48.032745),
(2, 'Mercado Municipal', -23.590666, -48.046129),
(3, 'Controle de Vetores e Zoonoses', -23.587518, -48.040364),
(4, 'AME Itapetininga', -23.589507, -48.044444);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbprevisao_chegada`
--

CREATE TABLE `tbprevisao_chegada` (
  `id_previsao` int(11) NOT NULL,
  `id_veiculo` int(11) NOT NULL,
  `id_ponto` int(11) NOT NULL,
  `estimativa_chegada` datetime NOT NULL,
  `criado_em` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbrotas`
--

CREATE TABLE `tbrotas` (
  `id_rota` int(11) NOT NULL,
  `id_horario` int(11) NOT NULL,
  `id_ponto` int(11) NOT NULL,
  `ordem` int(11) NOT NULL,
  `horario_previsto` time DEFAULT NULL,
  `tipo_ponto` varchar(20) DEFAULT 'meio'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tbrotas`
--

INSERT INTO `tbrotas` (`id_rota`, `id_horario`, `id_ponto`, `ordem`, `horario_previsto`, `tipo_ponto`) VALUES
(5, 1, 1, 1, '06:00:00', 'inicio'),
(6, 1, 3, 2, '06:15:00', 'meio'),
(8, 1, 4, 3, '06:25:00', 'meio'),
(10, 2, 2, 1, '06:35:00', 'inicio'),
(11, 2, 4, 2, '06:45:00', 'meio'),
(12, 2, 3, 3, '06:55:00', 'meio'),
(13, 2, 1, 4, '07:00:00', 'fim'),
(14, 1, 2, 4, '06:35:00', 'fim'),
(15, 3, 1, 1, '06:00:00', 'inicio'),
(16, 3, 2, 2, '06:20:00', 'fim'),
(17, 4, 2, 1, '06:20:00', 'inicio'),
(18, 4, 1, 2, '06:45:00', 'fim');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbusuarios`
--

CREATE TABLE `tbusuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `nivel_usuario` varchar(20) NOT NULL DEFAULT 'comum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tbusuarios`
--

INSERT INTO `tbusuarios` (`id_usuario`, `nome`, `email`, `senha`, `nivel_usuario`) VALUES
(1, 'Administrador Master', 'admin@transporte.com', '123456', 'admin'),
(3, 'Anderson Iwanezuk Thaczuk', 'anderson@senac', '123', 'admin'),
(4, 'comum', 'comum@rotabus', '123', 'comum');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tbveiculos`
--

CREATE TABLE `tbveiculos` (
  `id_veiculo` int(11) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `id_linha` int(11) DEFAULT NULL,
  `capacidade` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `tbveiculos`
--

INSERT INTO `tbveiculos` (`id_veiculo`, `placa`, `id_linha`, `capacidade`) VALUES
(1, 'BGH7A12', 1, 45);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tbhorario_programados`
--
ALTER TABLE `tbhorario_programados`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_linha` (`id_linha`);

--
-- Índices de tabela `tblinhas`
--
ALTER TABLE `tblinhas`
  ADD PRIMARY KEY (`id_linha`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `tblocalizacao_tempo_real`
--
ALTER TABLE `tblocalizacao_tempo_real`
  ADD PRIMARY KEY (`id_localizacao`),
  ADD KEY `id_veiculo` (`id_veiculo`);

--
-- Índices de tabela `tbmotoristas`
--
ALTER TABLE `tbmotoristas`
  ADD PRIMARY KEY (`id_motorista`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `tbmotoristas_alocados`
--
ALTER TABLE `tbmotoristas_alocados`
  ADD PRIMARY KEY (`id_alocado`),
  ADD UNIQUE KEY `id_motorista` (`id_motorista`,`id_veiculo`,`data_hora_inicio`),
  ADD KEY `id_veiculo` (`id_veiculo`);

--
-- Índices de tabela `tbpontos`
--
ALTER TABLE `tbpontos`
  ADD PRIMARY KEY (`id_ponto`);

--
-- Índices de tabela `tbprevisao_chegada`
--
ALTER TABLE `tbprevisao_chegada`
  ADD PRIMARY KEY (`id_previsao`),
  ADD KEY `id_veiculo` (`id_veiculo`),
  ADD KEY `id_ponto` (`id_ponto`);

--
-- Índices de tabela `tbrotas`
--
ALTER TABLE `tbrotas`
  ADD PRIMARY KEY (`id_rota`),
  ADD KEY `id_horario` (`id_horario`),
  ADD KEY `id_ponto` (`id_ponto`);

--
-- Índices de tabela `tbusuarios`
--
ALTER TABLE `tbusuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `tbveiculos`
--
ALTER TABLE `tbveiculos`
  ADD PRIMARY KEY (`id_veiculo`),
  ADD UNIQUE KEY `placa` (`placa`),
  ADD KEY `id_linha` (`id_linha`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tbhorario_programados`
--
ALTER TABLE `tbhorario_programados`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tblinhas`
--
ALTER TABLE `tblinhas`
  MODIFY `id_linha` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tblocalizacao_tempo_real`
--
ALTER TABLE `tblocalizacao_tempo_real`
  MODIFY `id_localizacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbmotoristas`
--
ALTER TABLE `tbmotoristas`
  MODIFY `id_motorista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tbmotoristas_alocados`
--
ALTER TABLE `tbmotoristas_alocados`
  MODIFY `id_alocado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbpontos`
--
ALTER TABLE `tbpontos`
  MODIFY `id_ponto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `tbprevisao_chegada`
--
ALTER TABLE `tbprevisao_chegada`
  MODIFY `id_previsao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tbrotas`
--
ALTER TABLE `tbrotas`
  MODIFY `id_rota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `tbusuarios`
--
ALTER TABLE `tbusuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `tbveiculos`
--
ALTER TABLE `tbveiculos`
  MODIFY `id_veiculo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tbhorario_programados`
--
ALTER TABLE `tbhorario_programados`
  ADD CONSTRAINT `tbhorario_programados_ibfk_1` FOREIGN KEY (`id_linha`) REFERENCES `tblinhas` (`id_linha`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tblocalizacao_tempo_real`
--
ALTER TABLE `tblocalizacao_tempo_real`
  ADD CONSTRAINT `tblocalizacao_tempo_real_ibfk_1` FOREIGN KEY (`id_veiculo`) REFERENCES `tbveiculos` (`id_veiculo`);

--
-- Restrições para tabelas `tbmotoristas_alocados`
--
ALTER TABLE `tbmotoristas_alocados`
  ADD CONSTRAINT `tbmotoristas_alocados_ibfk_1` FOREIGN KEY (`id_motorista`) REFERENCES `tbmotoristas` (`id_motorista`),
  ADD CONSTRAINT `tbmotoristas_alocados_ibfk_2` FOREIGN KEY (`id_veiculo`) REFERENCES `tbveiculos` (`id_veiculo`);

--
-- Restrições para tabelas `tbprevisao_chegada`
--
ALTER TABLE `tbprevisao_chegada`
  ADD CONSTRAINT `tbprevisao_chegada_ibfk_1` FOREIGN KEY (`id_veiculo`) REFERENCES `tbveiculos` (`id_veiculo`),
  ADD CONSTRAINT `tbprevisao_chegada_ibfk_2` FOREIGN KEY (`id_ponto`) REFERENCES `tbpontos` (`id_ponto`);

--
-- Restrições para tabelas `tbrotas`
--
ALTER TABLE `tbrotas`
  ADD CONSTRAINT `tbrotas_ibfk_2` FOREIGN KEY (`id_horario`) REFERENCES `tbhorario_programados` (`id_horario`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbrotas_ibfk_3` FOREIGN KEY (`id_ponto`) REFERENCES `tbpontos` (`id_ponto`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tbveiculos`
--
ALTER TABLE `tbveiculos`
  ADD CONSTRAINT `tbveiculos_ibfk_1` FOREIGN KEY (`id_linha`) REFERENCES `tblinhas` (`id_linha`);
COMMIT;

