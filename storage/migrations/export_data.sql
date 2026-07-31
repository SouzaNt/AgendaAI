-- Exportação de Dados do AgendaAI em 2026-07-30 15:18:55

-- Dados para a tabela `paises`
INSERT INTO `paises` (`id`, `nome`, `ativo`) VALUES ('1', 'Brasil', 1);

-- Dados para a tabela `estados`
INSERT INTO `estados` (`id`, `nome`, `uf`, `ativo`) VALUES ('1', 'São Paulo', 'SP', 1);
INSERT INTO `estados` (`id`, `nome`, `uf`, `ativo`) VALUES ('2', 'Rio de Janeiro', 'RJ', 1);
INSERT INTO `estados` (`id`, `nome`, `uf`, `ativo`) VALUES ('3', 'Minas Gerais', 'MG', 1);

-- Dados para a tabela `municipios`
INSERT INTO `municipios` (`id`, `nome`, `id_estado`, `ativo`) VALUES ('1', 'São Paulo', '1', 1);
INSERT INTO `municipios` (`id`, `nome`, `id_estado`, `ativo`) VALUES ('2', 'Campinas', '1', 1);
INSERT INTO `municipios` (`id`, `nome`, `id_estado`, `ativo`) VALUES ('3', 'Rio de Janeiro', '2', 1);
INSERT INTO `municipios` (`id`, `nome`, `id_estado`, `ativo`) VALUES ('4', 'Belo Horizonte', '3', 1);

-- Dados para a tabela `bairros`
INSERT INTO `bairros` (`id`, `nome`, `ativo`) VALUES ('1', 'Centro', 1);
INSERT INTO `bairros` (`id`, `nome`, `ativo`) VALUES ('2', 'Brooklin', 1);
INSERT INTO `bairros` (`id`, `nome`, `ativo`) VALUES ('3', 'Pinheiros', 1);

-- Dados para a tabela `tipos_logradouro`
INSERT INTO `tipos_logradouro` (`id`, `nome`, `abreviacao`, `ativo`) VALUES ('1', 'Rua', 'R.', 1);
INSERT INTO `tipos_logradouro` (`id`, `nome`, `abreviacao`, `ativo`) VALUES ('2', 'Avenida', 'Av.', 1);
INSERT INTO `tipos_logradouro` (`id`, `nome`, `abreviacao`, `ativo`) VALUES ('3', 'Praça', 'Pç.', 1);
INSERT INTO `tipos_logradouro` (`id`, `nome`, `abreviacao`, `ativo`) VALUES ('4', 'Alameda', 'Al.', 1);

-- Dados para a tabela `logradouros`
INSERT INTO `logradouros` (`id`, `nome`, `id_tipo_logradouro`, `ativo`) VALUES ('1', 'Paulista', '2', 1);
INSERT INTO `logradouros` (`id`, `nome`, `id_tipo_logradouro`, `ativo`) VALUES ('2', 'Engenheiro Luís Carlos Berrini', '1', 1);

-- Dados para a tabela `grupos`
INSERT INTO `grupos` (`id`, `nome`, `permissoes`, `ativo`, `created_at`, `updated_at`) VALUES ('1', 'Administrador', '{\"visualizar\":true,\"consultar\":true,\"editar\":true,\"deletar\":true}', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `grupos` (`id`, `nome`, `permissoes`, `ativo`, `created_at`, `updated_at`) VALUES ('2', 'Organizador', '{\"visualizar\":true,\"consultar\":true,\"editar\":true,\"deletar\":false}', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `grupos` (`id`, `nome`, `permissoes`, `ativo`, `created_at`, `updated_at`) VALUES ('3', 'Funcionário Agendador', '{\"visualizar\":true,\"consultar\":true,\"editar\":false,\"deletar\":false}', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

-- Dados para a tabela `funcoes`
INSERT INTO `funcoes` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('1', 'Gestor de T.I.', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `funcoes` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('2', 'Professor / Docente', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `funcoes` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('3', 'Coordenador Pedagógico', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `funcoes` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('4', 'Assistente Administrativo', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

-- Dados para a tabela `funcionarios`
INSERT INTO `funcionarios` (`id`, `nome`, `foto`, `email`, `senha`, `id_funcao`, `id_grupo`, `instituicoes_vinculadas`, `recebe_email`, `ativo`, `created_at`, `updated_at`) VALUES ('1', 'Administrador do Sistema', NULL, 'admin@agendaai.com', '$2y$12$SoM1rvuGXgABLtLLzmp0GexvMiRp5uyr5QjXPlUE8RNvsNInRjfmG', '1', '1', '[1,2]', 1, 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `funcionarios` (`id`, `nome`, `foto`, `email`, `senha`, `id_funcao`, `id_grupo`, `instituicoes_vinculadas`, `recebe_email`, `ativo`, `created_at`, `updated_at`) VALUES ('2', 'Prof. Carlos Eduardo', NULL, 'carlos.eduardo@agendaai.com', '$2y$12$SoM1rvuGXgABLtLLzmp0GexvMiRp5uyr5QjXPlUE8RNvsNInRjfmG', '2', '3', '[1]', 1, 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

-- Dados para a tabela `instituicoes`
INSERT INTO `instituicoes` (`id`, `nome`, `unidade_pai`, `municipio`, `bairro`, `logradouro_completo`, `numero`, `ativo`, `created_at`, `updated_at`) VALUES ('1', 'Campus Central - Sede', NULL, 'São Paulo', 'Centro', 'Avenida Paulista', '1000', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `instituicoes` (`id`, `nome`, `unidade_pai`, `municipio`, `bairro`, `logradouro_completo`, `numero`, `ativo`, `created_at`, `updated_at`) VALUES ('2', 'Unidade Zona Sul - Tecnologia', '1', 'São Paulo', 'Brooklin', 'Rua Engenheiro Luís Carlos Berrini', '500', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

-- Dados para a tabela `tipos_recurso`
INSERT INTO `tipos_recurso` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('1', 'Notebook Dell Inspiron i7', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `tipos_recurso` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('2', 'Projetor Datashow Epson 4K', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `tipos_recurso` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('3', 'Caixa de Som Amplificada Bluetooth', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `tipos_recurso` (`id`, `nome`, `ativo`, `created_at`, `updated_at`) VALUES ('4', 'Kit Microfone Sem Fio', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

-- Dados para a tabela `recursos`
INSERT INTO `recursos` (`id`, `nome`, `id_tipo_recurso`, `patrimonio`, `id_instituicao_responsavel`, `numero_serie`, `estado`, `historico_movimentacao`, `disponivel_agendamento`, `ativo`, `created_at`, `updated_at`) VALUES ('1', 'Notebook Core i7 - #01', '1', 'PAT-2026-001', '1', 'SN-DELL-998811', 'Funcionando', 'Disponibilizado no Campus Central', 1, 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `recursos` (`id`, `nome`, `id_tipo_recurso`, `patrimonio`, `id_instituicao_responsavel`, `numero_serie`, `estado`, `historico_movimentacao`, `disponivel_agendamento`, `ativo`, `created_at`, `updated_at`) VALUES ('2', 'Notebook Core i7 - #02', '1', 'PAT-2026-002', '1', 'SN-DELL-998812', 'Funcionando', 'Disponibilizado no Campus Central', 1, 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `recursos` (`id`, `nome`, `id_tipo_recurso`, `patrimonio`, `id_instituicao_responsavel`, `numero_serie`, `estado`, `historico_movimentacao`, `disponivel_agendamento`, `ativo`, `created_at`, `updated_at`) VALUES ('3', 'Projetor Datashow Epson HD', '2', 'PAT-2026-003', '1', 'SN-EPSON-3344', 'Funcionando', 'Alocado na Sala de Reuniões Sede', 1, 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `recursos` (`id`, `nome`, `id_tipo_recurso`, `patrimonio`, `id_instituicao_responsavel`, `numero_serie`, `estado`, `historico_movimentacao`, `disponivel_agendamento`, `ativo`, `created_at`, `updated_at`) VALUES ('4', 'Caixa Som JBL EON', '3', 'PAT-2026-004', '2', 'SN-JBL-7711', 'Não Funcionando', 'Enviado para manutenção preventiva', 0, 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

-- Dados para a tabela `salas`
INSERT INTO `salas` (`id`, `nome`, `id_instituicao_vinculada`, `ativo`, `created_at`, `updated_at`) VALUES ('1', 'Auditório Principal (Capacidade: 150 pessoas)', '1', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `salas` (`id`, `nome`, `id_instituicao_vinculada`, `ativo`, `created_at`, `updated_at`) VALUES ('2', 'Laboratório de Informática 01', '1', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');
INSERT INTO `salas` (`id`, `nome`, `id_instituicao_vinculada`, `ativo`, `created_at`, `updated_at`) VALUES ('3', 'Sala de Reuniões Diretoria', '2', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

-- Dados para a tabela `agendamentos`
INSERT INTO `agendamentos` (`id`, `usuario_id`, `usuario_nome`, `tipo_uso`, `motivo`, `data_inicio`, `data_fim`, `recursos_ids`, `salas_ids`, `instituicao_id`, `observacoes`, `prioridade_emergencia`, `status`, `motivo_cancelamento`, `ativo`, `created_at`, `updated_at`) VALUES ('1', '2', 'Prof. Carlos Eduardo', 'Unidade', 'Aula Prática de Programação', '2026-07-30 08:00:00', '2026-07-30 11:30:00', '[1,3]', '[2]', '1', 'Necessário projetor configurado para HDMI.', 0, 'Aprovado', NULL, 1, '2026-07-30 07:00:00', '2026-07-30 07:00:00');
INSERT INTO `agendamentos` (`id`, `usuario_id`, `usuario_nome`, `tipo_uso`, `motivo`, `data_inicio`, `data_fim`, `recursos_ids`, `salas_ids`, `instituicao_id`, `observacoes`, `prioridade_emergencia`, `status`, `motivo_cancelamento`, `ativo`, `created_at`, `updated_at`) VALUES ('2', '2', 'Prof. Carlos Eduardo', 'Externo', 'Apresentação em Feira de Tecnologia Externa', '2026-07-31 13:00:00', '2026-07-31 17:00:00', '[2]', '[]', '1', 'Uso externo com justificativa aprovada.', 0, 'Pendente', NULL, 1, '2026-07-30 08:30:00', '2026-07-30 08:30:00');

-- Dados para a tabela `auditoria`
INSERT INTO `auditoria` (`data_hora`, `usuario_id`, `usuario_nome`, `acao`, `tabela`, `valores_anteriores`, `valores_novos`, `ip`, `id`, `ativo`, `created_at`, `updated_at`) VALUES ('2026-07-30 15:18:50', '1', 'Administrador do Sistema', 'Login', 'funcionarios', NULL, '{\"email\":\"admin@agendaai.com\"}', '127.0.0.1', '1', 1, '2026-07-30 15:18:50', '2026-07-30 15:18:50');
INSERT INTO `auditoria` (`data_hora`, `usuario_id`, `usuario_nome`, `acao`, `tabela`, `valores_anteriores`, `valores_novos`, `ip`, `id`, `ativo`, `created_at`, `updated_at`) VALUES ('2026-07-30 15:18:52', '1', 'Administrador do Sistema', 'Login', 'funcionarios', NULL, '{\"email\":\"admin@agendaai.com\"}', '::1', '2', 1, '2026-07-30 15:18:52', '2026-07-30 15:18:52');

-- Dados para a tabela `configuracoes`
INSERT INTO `configuracoes` (`id`, `tolerancia_minutos`, `antecedencia_horas`, `smtp_servidor`, `smtp_porta`, `smtp_usuario`, `flags_email`, `agendamento_direto`, `limite_itens_usuario`, `duracao_maxima_horas`, `prazo_cancelamento_horas`, `horario_abertura`, `horario_fechamento`, `nivel_visibilidade`, `justificativa_cancelamento_obrigatoria`, `agendamento_massa_habilitado`, `limite_dias_agendamento_massa`, `ativo`, `created_at`, `updated_at`) VALUES ('1', '15', '1', 'smtp.agendaai.local', '587', 'notificacoes@agendaai.local', 1, 0, '5', '8', '2', '07:00', '22:00', 'Publico', 1, 1, '30', 1, '2026-07-30 00:00:00', '2026-07-30 00:00:00');

