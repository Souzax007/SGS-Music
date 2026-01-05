-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 28/12/2025 às 22:02
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `musica`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `music_album`
--

CREATE TABLE `music_album` (
  `id_album` int(11) NOT NULL,
  `nm_album` varchar(255) NOT NULL,
  `ds_cover` varchar(255) DEFAULT NULL,
  `dt_release` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `music_album`
--

INSERT INTO `music_album` (`id_album`, `nm_album`, `ds_cover`, `dt_release`) VALUES
(2, 'Gojira', 'img/cover_691fc61b4438d.jfif', NULL),
(8, 'Shepherd of Fire', 'img/cover_694b4659b95d3.jfif', NULL),
(9, 'Eu venci o mundo', 'img/cover_694cc431b7d85.jfif', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `music_favorites`
--

CREATE TABLE `music_favorites` (
  `id_user` int(11) NOT NULL,
  `id_music` int(11) NOT NULL,
  `dt_favorited` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `music_history`
--

CREATE TABLE `music_history` (
  `id_history` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_music` int(11) NOT NULL,
  `dt_played` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `music_history`
--

INSERT INTO `music_history` (`id_history`, `id_user`, `id_music`, `dt_played`) VALUES
(24, 1, 3, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `music_music`
--

CREATE TABLE `music_music` (
  `id_music` int(11) NOT NULL,
  `nm_music` varchar(255) NOT NULL,
  `ds_path` varchar(255) NOT NULL,
  `id_artist` varchar(255) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  `dt_upload` datetime DEFAULT NULL,
  `id_album` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `music_music`
--

INSERT INTO `music_music` (`id_music`, `nm_music`, `ds_path`, `id_artist`, `duration`, `dt_upload`, `id_album`) VALUES
(3, 'Flying Whales', 'audio/track_691fc661bee74.mp3', NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `music_user`
--

CREATE TABLE `music_user` (
  `id_user` int(11) NOT NULL,
  `nm_user` varchar(255) NOT NULL,
  `ds_pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `music_user`
--

INSERT INTO `music_user` (`id_user`, `nm_user`, `ds_pass`) VALUES
(1, 'Marcos', '$2y$10$rwUTTOQc0o6CZlD33zixKOahuHk3o8MZoEbFnXy6U1tHL.rfK1KEG'),
(2, 'Marcos souza', '$2y$10$YaNLsugSoZyKq8DKHv5WD.tUkCqRXfdFdo1Kpj1Nzg9oAedcOWmZG');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `music_album`
--
ALTER TABLE `music_album`
  ADD PRIMARY KEY (`id_album`);

--
-- Índices de tabela `music_favorites`
--
ALTER TABLE `music_favorites`
  ADD KEY `fk_fav_user` (`id_user`),
  ADD KEY `fk_fav_music` (`id_music`);

--
-- Índices de tabela `music_history`
--
ALTER TABLE `music_history`
  ADD PRIMARY KEY (`id_history`),
  ADD KEY `fk_history_user` (`id_user`),
  ADD KEY `fk_history_music` (`id_music`);

--
-- Índices de tabela `music_music`
--
ALTER TABLE `music_music`
  ADD PRIMARY KEY (`id_music`),
  ADD KEY `fk_music_album` (`id_album`);

--
-- Índices de tabela `music_user`
--
ALTER TABLE `music_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `music_album`
--
ALTER TABLE `music_album`
  MODIFY `id_album` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `music_history`
--
ALTER TABLE `music_history`
  MODIFY `id_history` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `music_music`
--
ALTER TABLE `music_music`
  MODIFY `id_music` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `music_user`
--
ALTER TABLE `music_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `music_favorites`
--
ALTER TABLE `music_favorites`
  ADD CONSTRAINT `fk_fav_music` FOREIGN KEY (`id_music`) REFERENCES `music_music` (`id_music`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`id_user`) REFERENCES `music_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `music_history`
--
ALTER TABLE `music_history`
  ADD CONSTRAINT `fk_history_music` FOREIGN KEY (`id_music`) REFERENCES `music_music` (`id_music`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_user` FOREIGN KEY (`id_user`) REFERENCES `music_user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `music_music`
--
ALTER TABLE `music_music`
  ADD CONSTRAINT `fk_music_album` FOREIGN KEY (`id_album`) REFERENCES `music_album` (`id_album`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
