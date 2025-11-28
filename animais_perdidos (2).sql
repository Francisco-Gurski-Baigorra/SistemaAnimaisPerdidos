-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 28-Nov-2025 às 01:48
-- Versão do servidor: 9.1.0
-- versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `animais_perdidos`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `animais`
--

DROP TABLE IF EXISTS `animais`;
CREATE TABLE IF NOT EXISTS `animais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `situacao` enum('perdido','encontrado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `especie` enum('cachorro','gato','outros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `genero` enum('macho','femea','nao_informado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `raca_id` int DEFAULT NULL,
  `porte` enum('pequeno','medio','grande') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cor_predominante` enum('preto','branco','marrom','cinza','caramelo','preto e branco','outros') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idade` enum('Filhote','Adulto','Idoso') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `data_ocorrido` date DEFAULT NULL,
  `telefone_contato` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_animais_usuarios` (`usuario_id`),
  KEY `fk_animais_racas` (`raca_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `racas`
--

DROP TABLE IF EXISTS `racas`;
CREATE TABLE IF NOT EXISTS `racas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `racas` enum('vira-lata','labrador','bulldog','pastor alemão','pincher','cimarron','husky','salsicha','golden','siamês','persa','sphynx') COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `racas`
--

INSERT INTO `racas` (`id`, `racas`) VALUES
(18, 'labrador'),
(19, 'pastor alemão'),
(20, 'vira-lata'),
(21, 'persa'),
(22, 'siamês'),
(26, 'sphynx');

-- --------------------------------------------------------

--
-- Estrutura da tabela `recuperacao`
--

DROP TABLE IF EXISTS `recuperacao`;
CREATE TABLE IF NOT EXISTS `recuperacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expira_em` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_recuperacao_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `recuperar_senha`
--

DROP TABLE IF EXISTS `recuperar_senha`;
CREATE TABLE IF NOT EXISTS `recuperar_senha` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  `usado` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `recuperar_senha`
--

INSERT INTO `recuperar_senha` (`id`, `email`, `token`, `data`, `usado`) VALUES
(1, 'francisco.2023318347@aluno.iffar.edu.br', 'ae3cd60b52124c731467850315875d10cf4344ec3ae06c7b12a3f650b46e9c34', '2025-10-27 14:07:58', 0),
(2, 'francisco.2023318347@aluno.iffar.edu.br', '5616414a12f52f27c4e185505cd172cadc73aff6e0f809c3540d4534d306747d', '2025-10-27 14:14:22', 0),
(3, 'francisco.2023318347@aluno.iffar.edu.br', '6087b55ccbb410f8b44da5f67925c96ea70964727466447d008f92511ea52467', '2025-10-27 14:17:33', 0),
(4, 'francisco.2023318347@aluno.iffar.edu.br', 'a42185776ffc8d0ebc866e9886ba3e65a3ed8e30b035969ba39cd1db906b02fb', '2025-10-27 14:21:37', 0),
(5, 'francisco.2023318347@aluno.iffar.edu.br', 'ce068234969d1c418ca197d333ef52ba9730b7a028308e7eb6bbb2c93f3f31a8', '2025-10-27 15:02:48', 1),
(6, 'francisco.2023318347@aluno.iffar.edu.br', '996ed36a30b46b1ca88453d2c80f5f2f0eaae05362446c2731c206cb57da4d59', '2025-11-24 20:39:18', 0),
(7, 'francisco.2023318347@aluno.iffar.edu.br', '0e11a1e98b561432adafec0ccc7aea8a4d6885031ae3c1eb2d1acbddda6c9a78', '2025-11-24 20:39:20', 0),
(8, 'franciscobaigorra@gmail.com', '084171957203252bfce7b8ccd48e68c1b395385de7f5c190526fbaad19ce090f', '2025-11-24 20:42:57', 0),
(9, 'franciscobaigorra@gmail.com', 'a1d800a9f2b6177264e44c05c7da0488c9a2cc8cebcc2625e8166a99aa493b63', '2025-11-24 20:49:24', 0),
(10, 'franciscobaigorra@gmail.com', 'aa19dc1d3d2f5c5f4369089174c5b88bbe7edfe91ea40f6da8d6756e791bc7b6', '2025-11-24 20:52:11', 0),
(11, 'franciscobaigorra@gmail.com', 'e80518266eb4f63eb134742098dbd7253b53475bd31c8353e31dc9e521a68f9d', '2025-11-24 20:55:21', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `tipo_usuario` enum('usuario','administrador') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usuario',
  `ativo` enum('sim','nao') COLLATE utf8mb4_unicode_ci DEFAULT 'sim',
  `data_cadastro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `telefone`, `endereco`, `data_nascimento`, `tipo_usuario`, `ativo`, `data_cadastro`) VALUES
(10, 'Chico', 'francisco.2023318347@aluno.iffar.edu.br', '$2y$10$jQbJJMVezmyVF80NAZWVYO9kOw2umzx8QFJ0r0vgkS8wKnGIICEtq', '55999957255', '1152', '2007-08-06', 'usuario', 'sim', '2025-10-27 18:02:38'),
(11, 'francisco baigorra', 'franciscobaigorra@gmail.com', '$2y$10$ohtwW.kCIEvTMFLxNwkA9uGUhrgWsUJsaPClJNNp33z8OYkrLL6P.', '55999957255', '1152', '2007-08-06', 'administrador', 'sim', '2025-11-13 23:14:39');

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `animais`
--
ALTER TABLE `animais`
  ADD CONSTRAINT `fk_animais_racas` FOREIGN KEY (`raca_id`) REFERENCES `racas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_animais_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `recuperacao`
--
ALTER TABLE `recuperacao`
  ADD CONSTRAINT `fk_recuperacao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
