-- ============================================================
-- AgendaAI: Estrutura de Banco de Dados MySQL
-- Fuso Horário: America/Sao_Paulo (Brasília)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `agendaai` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `agendaai`;

-- 1. Base Geográfica
CREATE TABLE IF NOT EXISTS `paises` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `estados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `uf` CHAR(2) NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `municipios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `id_estado` INT NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    FOREIGN KEY (`id_estado`) REFERENCES `estados`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `bairros` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tipos_logradouro` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(50) NOT NULL,
    `abreviacao` VARCHAR(10) NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `logradouros` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `id_tipo_logradouro` INT NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    FOREIGN KEY (`id_tipo_logradouro`) REFERENCES `tipos_logradouro`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Acesso e Segurança
CREATE TABLE IF NOT EXISTS `grupos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `permissoes` JSON NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `funcoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `funcionarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `foto` VARCHAR(255) NULL,
    `email` VARCHAR(150) UNIQUE NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    `id_funcao` INT NOT NULL,
    `id_grupo` INT NOT NULL,
    `instituicoes_vinculadas` JSON NULL,
    `recebe_email` TINYINT(1) DEFAULT 1,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    FOREIGN KEY (`id_funcao`) REFERENCES `funcoes`(`id`),
    FOREIGN KEY (`id_grupo`) REFERENCES `grupos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Estrutura Organizacional e Recursos
CREATE TABLE IF NOT EXISTS `instituicoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `unidade_pai` INT NULL,
    `municipio` VARCHAR(100) NULL,
    `bairro` VARCHAR(100) NULL,
    `logradouro_completo` VARCHAR(255) NULL,
    `numero` VARCHAR(20) NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tipos_recurso` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `recursos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(150) NOT NULL,
    `id_tipo_recurso` INT NOT NULL,
    `patrimonio` VARCHAR(50) UNIQUE NOT NULL,
    `id_instituicao_responsavel` INT NOT NULL,
    `numero_serie` VARCHAR(100) NULL,
    `estado` ENUM('Funcionando', 'Não Funcionando', 'Problemas') DEFAULT 'Funcionando',
    `historico_movimentacao` TEXT NULL,
    `disponivel_agendamento` TINYINT(1) DEFAULT 1,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    FOREIGN KEY (`id_tipo_recurso`) REFERENCES `tipos_recurso`(`id`),
    FOREIGN KEY (`id_instituicao_responsavel`) REFERENCES `instituicoes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `salas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(100) NOT NULL,
    `id_instituicao_vinculada` INT NOT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    FOREIGN KEY (`id_instituicao_vinculada`) REFERENCES `instituicoes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Agendamentos, Feedbacks e Manutenções
CREATE TABLE IF NOT EXISTS `agendamentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NOT NULL,
    `usuario_nome` VARCHAR(150) NOT NULL,
    `tipo_uso` ENUM('Unidade', 'Externo') DEFAULT 'Unidade',
    `motivo` TEXT NULL,
    `data_inicio` DATETIME NOT NULL,
    `data_fim` DATETIME NOT NULL,
    `recursos_ids` JSON NULL,
    `salas_ids` JSON NULL,
    `instituicao_id` INT NOT NULL,
    `observacoes` TEXT NULL,
    `prioridade_emergencia` TINYINT(1) DEFAULT 0,
    `status` VARCHAR(50) DEFAULT 'Pendente',
    `motivo_cancelamento` TEXT NULL,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    FOREIGN KEY (`usuario_id`) REFERENCES `funcionarios`(`id`),
    FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `feedbacks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `agendamento_id` INT NOT NULL,
    `recurso_id` INT NULL,
    `comentario` TEXT NULL,
    `aluno_utilizador` VARCHAR(150) NULL,
    `imagem_path` VARCHAR(255) NULL,
    `data_registro` DATETIME NOT NULL,
    FOREIGN KEY (`agendamento_id`) REFERENCES `agendamentos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `manutencoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `recurso_id` INT NOT NULL,
    `recurso_nome` VARCHAR(150) NOT NULL,
    `estado_anterior` VARCHAR(50) NOT NULL,
    `estado_novo` VARCHAR(50) NOT NULL,
    `data_registro` DATETIME NOT NULL,
    `usuario_id` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Auditoria, Configurações e Logs
CREATE TABLE IF NOT EXISTS `auditoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `data_hora` DATETIME NOT NULL,
    `usuario_id` INT NULL,
    `usuario_nome` VARCHAR(150) NULL,
    `acao` VARCHAR(100) NOT NULL,
    `tabela` VARCHAR(100) NOT NULL,
    `valores_anteriores` JSON NULL,
    `valores_novos` JSON NULL,
    `ip` VARCHAR(45) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `email_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `destinatario_email` VARCHAR(150) NOT NULL,
    `destinatario_nome` VARCHAR(150) NULL,
    `assunto` VARCHAR(255) NOT NULL,
    `corpo` TEXT NULL,
    `tipo_evento` VARCHAR(100) NULL,
    `data_envio` DATETIME NOT NULL,
    `status` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `configuracoes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tolerancia_minutos` INT DEFAULT 15,
    `antecedencia_horas` INT DEFAULT 1,
    `smtp_servidor` VARCHAR(150) NULL,
    `smtp_porta` INT DEFAULT 587,
    `smtp_usuario` VARCHAR(150) NULL,
    `flags_email` TINYINT(1) DEFAULT 1,
    `agendamento_direto` TINYINT(1) DEFAULT 0,
    `limite_itens_usuario` INT DEFAULT 5,
    `duracao_maxima_horas` INT DEFAULT 8,
    `prazo_cancelamento_horas` INT DEFAULT 2,
    `horario_abertura` VARCHAR(5) DEFAULT '07:00',
    `horario_fechamento` VARCHAR(5) DEFAULT '22:00',
    `nivel_visibilidade` VARCHAR(20) DEFAULT 'Publico',
    `justificativa_cancelamento_obrigatoria` TINYINT(1) DEFAULT 1,
    `agendamento_massa_habilitado` TINYINT(1) DEFAULT 1,
    `limite_dias_agendamento_massa` INT DEFAULT 30,
    `ativo` TINYINT(1) DEFAULT 1,
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
