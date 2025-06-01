-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 01 juin 2025 à 13:00
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `zenticket`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

DROP TABLE IF EXISTS `administrateur`;
CREATE TABLE IF NOT EXISTS `administrateur` (
  `id` int NOT NULL,
  `rapport_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_32EB52E81DFBCC46` (`rapport_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`id`, `rapport_id`) VALUES
(1, NULL),
(2, NULL),
(3, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int NOT NULL AUTO_INCREMENT,
  `administrateur_id` int DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_497DD6347EE5403C` (`administrateur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `administrateur_id`, `nom`, `description`) VALUES
(1, NULL, 'Réseau', 'Problèmes liés au réseau'),
(2, NULL, 'Matériel', 'Problèmes matériels'),
(3, NULL, 'Logiciel', 'Problèmes logiciels'),
(4, NULL, 'Sécurité', 'Questions de sécurité'),
(5, NULL, 'Maintenance', 'Maintenance préventive'),
(6, NULL, 'Cloud', 'Services cloud et hébergement'),
(7, NULL, 'Base de données', 'Problèmes de bases de données'),
(8, 1, 'tesdt', 'test');

-- --------------------------------------------------------

--
-- Structure de la table `chatbox`
--

DROP TABLE IF EXISTS `chatbox`;
CREATE TABLE IF NOT EXISTS `chatbox` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ia_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `is_temporary` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7472FC2F489A6E65` (`ia_id`),
  KEY `IDX_7472FC2FA76ED395` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `chatbox`
--

INSERT INTO `chatbox` (`id`, `ia_id`, `user_id`, `created_at`, `is_temporary`) VALUES
(1, 1, NULL, NULL, 0),
(2, 1, NULL, NULL, 0),
(3, 1, NULL, NULL, 0),
(4, 1, NULL, NULL, 0),
(5, 1, NULL, NULL, 0),
(6, 1, NULL, NULL, 0),
(7, 1, NULL, NULL, 0),
(8, 1, NULL, NULL, 0),
(9, 1, NULL, NULL, 0),
(10, 1, NULL, NULL, 0),
(11, 1, NULL, NULL, 0),
(12, 1, NULL, NULL, 0),
(13, 1, NULL, NULL, 0),
(14, 1, NULL, NULL, 0),
(15, 1, NULL, NULL, 0),
(16, 1, NULL, NULL, 0),
(17, 1, NULL, NULL, 0),
(18, 1, NULL, NULL, 0),
(19, 1, NULL, NULL, 0),
(20, 1, NULL, NULL, 0),
(21, 1, NULL, NULL, 0),
(22, 1, NULL, NULL, 0),
(23, 2, 13, '2025-03-10 19:07:23', 1),
(24, NULL, NULL, NULL, 0),
(25, NULL, NULL, NULL, 0),
(26, 3, 16, '2025-05-23 22:40:38', 1),
(27, NULL, NULL, NULL, 0),
(28, NULL, 4, '2025-05-29 10:13:34', 0),
(29, NULL, NULL, NULL, 0),
(30, NULL, NULL, NULL, 0),
(31, 4, 17, '2025-06-01 08:55:06', 1);

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

DROP TABLE IF EXISTS `commentaire`;
CREATE TABLE IF NOT EXISTS `commentaire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_id` int NOT NULL,
  `auteur_id` int NOT NULL,
  `contenu` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` datetime NOT NULL,
  `piece_jointe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_67F068BC700047D2` (`ticket_id`),
  KEY `IDX_67F068BC60BB6FE6` (`auteur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250601125845', '2025-06-01 12:59:35', 577);

-- --------------------------------------------------------

--
-- Structure de la table `equipement`
--

DROP TABLE IF EXISTS `equipement`;
CREATE TABLE IF NOT EXISTS `equipement` (
  `id` int NOT NULL AUTO_INCREMENT,
  `s_nmp_id` int DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B8B4C6F33303B12C` (`s_nmp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ia`
--

DROP TABLE IF EXISTS `ia`;
CREATE TABLE IF NOT EXISTS `ia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `temperature` double NOT NULL,
  `default_context` longtext COLLATE utf8mb4_unicode_ci,
  `additional_params` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ia`
--

INSERT INTO `ia` (`id`, `nom`, `api_key`, `model`, `temperature`, `default_context`, `additional_params`) VALUES
(1, 'Assistant 3INNOV', '%env(OPENAI_API_KEY)%', 'gpt-3.5-turbo', 0.7, 'Tu es un assistant technique pour 3INNOV.', '{\"max_tokens\": 500, \"presence_penalty\": 0.6}'),
(2, 'Assistant IA', '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', 'gpt-3.5-turbo', 0.7, 'Je suis un assistant helpdesk qui aide les utilisateurs avec leurs problèmes techniques.', '[]'),
(3, 'Assistant IA', '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', 'gpt-3.5-turbo', 0.7, 'Je suis un assistant helpdesk qui aide les utilisateurs avec leurs problèmes techniques.', '[]'),
(4, 'Assistant IA', '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', 'gpt-3.5-turbo', 0.7, 'Je suis un assistant helpdesk qui aide les utilisateurs avec leurs problèmes techniques.', '[]');

-- --------------------------------------------------------

--
-- Structure de la table `mail`
--

DROP TABLE IF EXISTS `mail`;
CREATE TABLE IF NOT EXISTS `mail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `destinataire` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5126AC48FB88E14F` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `chatbox_id` int DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime NOT NULL,
  `message_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reactions` json DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL,
  `sender_id` int NOT NULL,
  `user_message_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B6BD307F53527A38` (`chatbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `chatbox_id`, `message`, `timestamp`, `message_type`, `reactions`, `is_read`, `sender_id`, `user_message_id`) VALUES
(1, 1, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(2, 1, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(3, 1, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(4, 2, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(5, 2, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(6, 2, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(7, 3, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 5, NULL),
(8, 3, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(9, 3, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(10, 4, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 5, NULL),
(11, 4, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(12, 4, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(13, 5, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 6, NULL),
(14, 5, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(15, 5, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(16, 6, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 6, NULL),
(17, 6, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(18, 6, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(19, 7, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 7, NULL),
(20, 7, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(21, 7, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(22, 8, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 7, NULL),
(23, 8, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(24, 8, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(25, 9, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 8, NULL),
(26, 9, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(27, 9, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(28, 10, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 8, NULL),
(29, 10, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(30, 10, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(31, 11, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 9, NULL),
(32, 11, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(33, 11, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(34, 12, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 9, NULL),
(35, 12, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(36, 12, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(37, 13, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 10, NULL),
(38, 13, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(39, 13, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(40, 14, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 10, NULL),
(41, 14, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(42, 14, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(43, 15, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 11, NULL),
(44, 15, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(45, 15, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(46, 16, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 11, NULL),
(47, 16, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(48, 16, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(49, 17, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 12, NULL),
(50, 17, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(51, 17, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(52, 18, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 12, NULL),
(53, 18, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(54, 18, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(55, 19, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 13, NULL),
(56, 19, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(57, 19, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(58, 20, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 13, NULL),
(59, 20, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(60, 20, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(61, 21, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 14, NULL),
(62, 21, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(63, 21, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(64, 22, 'Bonjour, j\'ai un problème avec...', '2025-03-10 19:00:29', 'user', '[]', 0, 14, NULL),
(65, 22, 'Je peux vous aider avec ça.', '2025-03-10 19:00:29', 'user', '[]', 0, 4, NULL),
(66, 22, 'Voici quelques suggestions...', '2025-03-10 19:00:29', 'ai', '[]', 0, 1, NULL),
(67, 23, 'Bonjour ! Je suis votre assistant virtuel. Comment puis-je vous aider aujourd\'hui ? Décrivez votre problème et je ferai de mon mieux pour le résoudre. Si je ne parviens pas à vous aider, vous pourrez créer un ticket d\'assistance.', '2025-03-10 19:07:23', 'ai', '[]', 0, 2, NULL),
(68, 23, 'hello', '2025-03-10 19:07:29', 'user', '[]', 0, 13, NULL),
(69, 23, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-03-10 19:07:30', 'ai', '[]', 0, 2, 68),
(70, 23, 'merci', '2025-03-10 19:19:45', 'user', '[]', 0, 13, NULL),
(71, 23, 'De rien ! N\'hésitez pas à revenir vers moi si vous avez besoin d\'aide. Bonne journée !', '2025-03-10 19:19:46', 'ai', '[]', 0, 2, 70),
(72, 23, 'ok', '2025-03-10 19:22:21', 'user', '[]', 0, 13, NULL),
(73, 23, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-03-10 19:22:22', 'ai', '[]', 0, 2, 72),
(74, 25, 'hello', '2025-03-10 19:22:38', 'user', '[]', 0, 13, NULL),
(75, 25, 'salut', '2025-03-10 19:28:34', 'user', '[]', 0, 13, NULL),
(76, 25, 'qui est tu', '2025-03-10 19:28:46', 'user', '[]', 0, 13, NULL),
(77, 26, 'Bonjour ! Je suis votre assistant virtuel. Comment puis-je vous aider aujourd\'hui ? Décrivez votre problème et je ferai de mon mieux pour le résoudre. Si je ne parviens pas à vous aider, vous pourrez créer un ticket d\'assistance.', '2025-05-23 22:40:38', 'ai', '[]', 0, 3, NULL),
(78, 26, 'hello comment ça va', '2025-05-23 22:40:48', 'user', '[]', 0, 16, NULL),
(79, 26, 'Je suis un programme informatique, je n\'ai pas de sentiments, mais je suis là pour vous aider. Que puis-je faire pour vous aujourd\'hui ?', '2025-05-23 22:40:49', 'ai', '[]', 0, 3, 78),
(80, 26, 'comment crée un ticket', '2025-05-23 22:41:34', 'user', '[]', 0, 16, NULL),
(81, 26, 'Pour créer un ticket d\'assistance, vous pouvez suivre ces étapes :\n\n1. Contactez le service d\'assistance technique de votre entreprise ou organisation par téléphone, par e-mail ou via le portail en ligne dédié.\n2. Fournissez les détails de votre problème ', '2025-05-23 22:41:36', 'ai', '[]', 0, 3, 80),
(82, 27, 'tesdt', '2025-05-23 22:42:39', 'user', '[]', 0, 16, NULL),
(83, 27, 'hello', '2025-05-25 17:25:05', 'user', '[]', 0, 16, NULL),
(84, 25, 'salut', '2025-05-29 10:13:10', 'user', '[]', 0, 4, NULL),
(85, 28, 'salut', '2025-05-29 10:13:39', 'user', '[]', 0, 4, NULL),
(86, 29, 'hello', '2025-05-29 10:15:39', 'user', '[]', 0, 16, NULL),
(87, 29, 'salut tu vas bien', '2025-05-29 10:16:23', 'user', '[]', 0, 4, NULL),
(88, 29, 'ça va merci et toi', '2025-05-29 10:16:55', 'user', '[]', 0, 16, NULL),
(89, 26, 'merci', '2025-05-30 21:44:46', 'user', '[]', 0, 16, NULL),
(90, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-30 21:44:48', 'ai', '[]', 0, 3, 89),
(91, 26, 'je veux te remercier c\'est tout', '2025-05-30 21:45:06', 'user', '[]', 0, 16, NULL),
(92, 26, 'De rien ! N\'hésitez pas à me contacter si vous avez besoin d\'aide à l\'avenir. Merci et bonne journée !', '2025-05-30 21:45:12', 'ai', '[]', 0, 3, 91),
(93, 29, 'bien bien', '2025-05-30 21:49:26', 'user', '[]', 0, 16, NULL),
(94, 29, 'merci bbien', '2025-05-30 23:18:50', 'user', '[]', 0, 4, NULL),
(95, 26, 'salut mon pote', '2025-05-31 08:47:34', 'user', '[]', 0, 16, NULL),
(96, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 08:47:36', 'ai', '[]', 0, 3, 95),
(97, 30, 'salut comment tu vas', '2025-05-31 08:48:16', 'user', '[]', 0, 16, NULL),
(98, 29, 'dzq', '2025-05-31 13:09:17', 'user', '[]', 0, 4, NULL),
(99, 29, 'hello', '2025-05-31 13:09:26', 'user', '[]', 0, 4, NULL),
(100, 29, 'bonjour', '2025-05-31 13:09:56', 'user', '[]', 0, 16, NULL),
(101, 29, 'hezllo', '2025-05-31 14:38:02', 'user', '[]', 0, 4, NULL),
(102, 29, 'dqD', '2025-05-31 14:38:09', 'user', '[]', 0, 4, NULL),
(103, 26, 'test', '2025-05-31 15:31:15', 'user', '[]', 0, 16, NULL),
(104, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:31:17', 'ai', '[]', 0, 3, 103),
(105, 26, 'dzqd', '2025-05-31 15:31:51', 'user', '[]', 0, 16, NULL),
(106, 26, 'Il semble y avoir une erreur de saisie. Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:31:53', 'ai', '[]', 0, 3, 105),
(107, 26, 'test', '2025-05-31 15:35:16', 'user', '[]', 0, 16, NULL),
(108, 26, 'Il semble y avoir un problème avec la communication. Comment puis-je vous aider ?', '2025-05-31 15:35:17', 'ai', '[]', 0, 3, 107),
(109, 26, 'salut', '2025-05-31 15:35:51', 'user', '[]', 0, 16, NULL),
(110, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:35:53', 'ai', '[]', 0, 3, 109),
(111, 26, 'test', '2025-05-31 15:37:13', 'user', '[]', 0, 16, NULL),
(112, 26, 'Il semble y avoir un problème de communication. Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:37:14', 'ai', '[]', 0, 3, 111),
(113, 26, 'salut', '2025-05-31 15:38:08', 'user', '[]', 0, 16, NULL),
(114, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:38:09', 'ai', '[]', 0, 3, 113),
(115, 26, 'hello', '2025-05-31 15:41:31', 'user', '[]', 0, 16, NULL),
(116, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:41:32', 'ai', '[]', 0, 3, 115),
(117, 26, 'testr', '2025-05-31 15:47:50', 'user', '[]', 0, 16, NULL),
(118, 26, 'Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:47:51', 'ai', '[]', 0, 3, 117),
(119, 26, 'merci', '2025-05-31 15:48:35', 'user', '[]', 0, 16, NULL),
(120, 26, 'De rien ! N\'hésitez pas si vous avez d\'autres questions. Bonne journée !', '2025-05-31 15:48:36', 'ai', '[]', 0, 3, 119),
(121, 26, 'fgfdgdf', '2025-05-31 15:55:13', 'user', '[]', 0, 16, NULL),
(122, 26, 'Il semble y avoir une erreur de saisie. Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 15:55:14', 'ai', '[]', 0, 3, 121),
(123, 26, 'lkj', '2025-05-31 16:03:28', 'user', '[]', 0, 16, NULL),
(124, 26, 'Il semble y avoir une erreur de saisie. Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 16:03:28', 'ai', '[]', 0, 3, 123),
(125, 26, 'salut', '2025-05-31 16:05:13', 'user', '[]', 0, 16, NULL),
(126, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 16:05:14', 'ai', '[]', 0, 3, 125),
(127, 26, 'salut', '2025-05-31 16:07:16', 'user', '[]', 0, 16, NULL),
(128, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 16:07:17', 'ai', '[]', 0, 3, 127),
(129, 26, 'merci', '2025-05-31 16:09:07', 'user', '[]', 0, 16, NULL),
(130, 26, 'De rien ! N\'hésitez pas à me poser des questions si vous avez besoin d\'aide.', '2025-05-31 16:09:08', 'ai', '[]', 0, 3, 129),
(131, 26, 'hello', '2025-05-31 16:11:12', 'user', '[]', 0, 16, NULL),
(132, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 16:11:13', 'ai', '[]', 0, 3, 131),
(133, 26, 'merci', '2025-05-31 16:11:22', 'user', '[]', 0, 16, NULL),
(134, 26, 'De rien ! Si vous avez d\'autres questions, n\'hésitez pas à me les poser.', '2025-05-31 16:11:23', 'ai', '[]', 0, 3, 133),
(135, 26, 'salut', '2025-05-31 16:13:39', 'user', '[]', 0, 16, NULL),
(136, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 16:13:40', 'ai', '[]', 0, 3, 135),
(137, 26, 'dz', '2025-05-31 16:50:33', 'user', '[]', 0, 16, NULL),
(138, 26, 'Il semble y avoir une erreur dans votre message. Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 16:50:34', 'ai', '[]', 0, 3, 137),
(139, 26, 'merci', '2025-05-31 16:53:28', 'user', '[]', 0, 16, NULL),
(140, 26, 'De rien ! N\'hésitez pas à me contacter si vous avez besoin d\'aide. Bonne journée !', '2025-05-31 16:53:30', 'ai', '[]', 0, 3, 139),
(141, 26, 'hello', '2025-05-31 16:57:18', 'user', '[]', 0, 16, NULL),
(142, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 16:57:20', 'ai', '[]', 0, 3, 141),
(143, 26, 'salut', '2025-05-31 17:03:45', 'user', '[]', 0, 16, NULL),
(144, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 17:03:46', 'ai', '[]', 0, 3, 143),
(145, 26, 'hello', '2025-05-31 17:07:00', 'user', '[]', 0, 16, NULL),
(146, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 17:07:01', 'ai', '[]', 0, 3, 145),
(147, 26, 'hello', '2025-05-31 17:39:20', 'user', '[]', 0, 16, NULL),
(148, 26, 'Bonjour ! Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 17:39:22', 'ai', '[]', 0, 3, 147),
(149, 26, 'test', '2025-05-31 17:45:23', 'user', '[]', 0, 16, NULL),
(150, 26, 'Comment puis-je vous aider aujourd\'hui ?', '2025-05-31 17:45:24', 'ai', '[]', 0, 3, 149),
(151, 26, 'dzqd', '2025-05-31 17:48:16', 'user', '[]', 0, 16, NULL),
(152, 26, 'Il semble y avoir une erreur dans votre message. Comment puis-je vous assister aujourd\'hui ?', '2025-05-31 17:48:17', 'ai', '[]', 0, 3, 151),
(153, 26, 'test', '2025-05-31 17:59:01', 'user', '[]', 0, 16, NULL),
(154, 26, 'Il semble y avoir un problème de communication. Comment puis-je vous aider ?', '2025-05-31 17:59:02', 'ai', '[]', 0, 3, 153),
(155, 26, 'merci pour ton aide', '2025-05-31 17:59:13', 'user', '[]', 0, 16, NULL),
(156, 26, 'De rien ! N\'hésitez pas si vous avez d\'autres questions ou besoin d\'aide.', '2025-05-31 17:59:14', 'ai', '[]', 0, 3, 155),
(157, 29, 'd\'accord', '2025-05-31 18:04:06', 'user', '[]', 0, 16, NULL),
(158, 26, 'merci pour ton aide', '2025-05-31 18:07:11', 'user', '[]', 0, 16, NULL),
(159, 26, 'De rien ! N\'hésitez pas si vous avez d\'autres questions ou besoin d\'aide.', '2025-05-31 18:07:12', 'ai', '[]', 0, 3, 158),
(160, 26, 'merci', '2025-05-31 19:18:23', 'user', '[]', 0, 16, NULL),
(161, 26, 'Vous êtes le bienvenu ! N\'hésitez pas si vous avez d\'autres questions.', '2025-05-31 19:18:25', 'ai', '[]', 0, 3, 160),
(162, 26, 'hello', '2025-05-31 19:27:13', 'user', '[]', 0, 16, NULL),
(163, 26, 'Bonjour! Comment puis-je vous aider aujourd\'hui?', '2025-05-31 19:27:15', 'ai', '[]', 0, 3, 162),
(164, 26, 'hello', '2025-05-31 22:10:40', 'user', '[]', 0, 16, NULL),
(165, 26, 'hello', '2025-05-31 22:11:51', 'user', '[]', 0, 16, NULL),
(166, 26, 'test', '2025-05-31 22:12:43', 'user', '[]', 0, 16, NULL),
(167, 26, 'yg', '2025-05-31 22:32:08', 'user', '[]', 0, 16, NULL),
(168, 26, 'tesyt', '2025-05-31 22:37:30', 'user', '[]', 0, 16, NULL),
(169, 26, 'test', '2025-05-31 22:38:45', 'user', '[]', 0, 16, NULL),
(170, 26, 'h', '2025-05-31 22:46:11', 'user', '[]', 0, 16, NULL),
(171, 26, 'test', '2025-05-31 22:49:55', 'user', '[]', 0, 16, NULL),
(172, 26, 'hello', '2025-05-31 22:55:53', 'user', '[]', 0, 16, NULL),
(173, 26, 'hello chat gpt', '2025-06-01 08:47:27', 'user', '[]', 0, 16, NULL),
(174, 26, 'salut', '2025-06-01 08:51:27', 'user', '[]', 0, 16, NULL),
(175, 31, 'Bonjour ! Je suis votre assistant virtuel. Comment puis-je vous aider aujourd\'hui ? Décrivez votre problème et je ferai de mon mieux pour le résoudre. Si je ne parviens pas à vous aider, vous pourrez créer un ticket d\'assistance.', '2025-06-01 08:55:06', 'ai', '[]', 0, 4, NULL),
(176, 31, 'merci', '2025-06-01 08:55:10', 'user', '[]', 0, 17, NULL),
(177, 31, 'De rien ! N\'hésitez pas à me contacter si vous avez besoin d\'aide à l\'avenir. Bonne journée !', '2025-06-01 08:55:11', 'ai', '[]', 0, 4, 176);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification`
--

DROP TABLE IF EXISTS `notification`;
CREATE TABLE IF NOT EXISTS `notification` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `ticket_id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lu` tinyint(1) NOT NULL,
  `date_creation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BF5476CAFB88E14F` (`utilisateur_id`),
  KEY `IDX_BF5476CA700047D2` (`ticket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `notification`
--

INSERT INTO `notification` (`id`, `utilisateur_id`, `ticket_id`, `titre`, `message`, `type`, `lu`, `date_creation`) VALUES
(1, 9, 22, 'Ticket résolu', 'Votre ticket \"Ticket #22 - Problème d\'impression\" a été résolu.', 'ticket_resolu', 0, '2025-03-10 19:06:47'),
(2, 13, 53, 'Nouveau ticket créé', 'Votre ticket \"jsp\" a été créé avec succès.', 'nouveau_ticket', 0, '2025-03-10 19:07:48'),
(3, 13, 54, 'Nouveau ticket créé', 'Votre ticket \"dqdqz\" a été créé avec succès.', 'nouveau_ticket', 0, '2025-03-10 19:22:31'),
(4, 13, 28, 'Ticket résolu', 'Votre ticket \"Ticket #28 - Problème d\'impression\" a été résolu.', 'ticket_resolu', 0, '2025-03-10 19:29:30'),
(5, 16, 55, 'Nouveau ticket créé', 'Votre ticket \"dqdqz\" a été créé avec succès.', 'nouveau_ticket', 0, '2025-05-23 22:42:05'),
(6, 16, 55, 'Ticket résolu', 'Votre ticket \"dqdqz\" a été résolu.', 'ticket_resolu', 0, '2025-05-23 22:43:37'),
(7, 16, 55, 'Ticket résolu', 'Votre ticket \"dqdqz\" a été résolu.', 'ticket_resolu', 0, '2025-05-25 17:25:31'),
(8, 16, 55, 'Ticket résolu', 'Votre ticket \"dqdqz\" a été résolu.', 'ticket_resolu', 0, '2025-05-25 17:25:49'),
(9, 12, 4, 'Ticket résolu', 'Votre ticket \"Ticket #4 - Mise à jour requise\" a été résolu.', 'ticket_resolu', 0, '2025-05-29 10:04:05'),
(10, 9, 22, 'Nouveau chat disponible', 'Un technicien a créé un canal de discussion pour votre ticket \"Ticket #22 - Problème d\'impression\". Vous pouvez maintenant communiquer directement avec lui.', 'nouveau_ticket', 0, '2025-05-29 10:13:34'),
(11, 13, 54, 'Ticket résolu', 'Votre ticket \"dqdqz\" a été résolu.', 'ticket_resolu', 0, '2025-05-29 10:14:11'),
(12, 16, 56, 'Nouveau ticket créé', 'Votre ticket \"test chat\" a été créé avec succès.', 'nouveau_ticket', 0, '2025-05-29 10:15:30'),
(13, 16, 57, 'Nouveau ticket créé', 'Votre ticket \"test 123\" a été créé avec succès.', 'nouveau_ticket', 0, '2025-05-31 08:48:05'),
(14, 16, 56, 'Ticket résolu', 'Votre ticket \"test chat\" a été résolu.', 'ticket_resolu', 0, '2025-05-31 14:38:26'),
(15, 16, 56, 'Ticket résolu', 'Votre ticket \"test chat\" a été résolu.', 'ticket_resolu', 0, '2025-05-31 14:38:29');

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

DROP TABLE IF EXISTS `personne`;
CREATE TABLE IF NOT EXISTS `personne` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_FCEC9EFE7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`id`, `nom`, `email`, `password`, `roles`, `type`) VALUES
(1, 'Admin Principal', 'admin@3innov.fr', '$2y$13$t2txacYnk0KyYwMycOO2EOY2QtfRH477FRKg.xrfYwEW5hX8vJExm', '[\"ROLE_ADMIN\"]', 'administrateur'),
(2, 'Admin Système', 'sysadmin@3innov.fr', '$2y$13$//lxx91AxVA7uyiz7sh0luy0SkKLviZDvnrQH7goB5SwzCDpyVLne', '[\"ROLE_ADMIN\"]', 'administrateur'),
(3, 'Admin Réseau', 'netadmin@3innov.fr', '$2y$13$FkAoVUSltZuat3UOwP7vgOxW9Dc/FX2Zlu.yp3JK3UTBk9DKfOomm', '[\"ROLE_ADMIN\"]', 'administrateur'),
(4, 'Tech Support Réseau', 'tech.reseau@3innov.fr', '$2y$13$D5snlge4SsOt2VEwI1i.q.9pz6CrTLYupL5yg2IVyrWn9psuJNhlu', '[\"ROLE_USER\", \"ROLE_TECHNICIEN\"]', 'technicien'),
(5, 'Tech Hardware', 'tech.hardware@3innov.fr', '$2y$13$9zMyAbW1zRMh0ARpH6XwsuPOOCYo5cIEE0Q/lGer/VwUl4Ouf8iRS', '[\"ROLE_USER\", \"ROLE_TECHNICIEN\"]', 'technicien'),
(6, 'Tech Software', 'tech.software@3innov.fr', '$2y$13$IjP4Of4kgo/i2VzyOWg/P.JR17e8dnZEcSsx1a9kUPiOioSdZOmBq', '[\"ROLE_USER\", \"ROLE_TECHNICIEN\"]', 'technicien'),
(7, 'Tech Sécurité', 'tech.security@3innov.fr', '$2y$13$QVkvamHPB6Je5wo8wZNNHuscYCPRAsm.NjB4JZxqgYuOQ1gS40MPu', '[\"ROLE_USER\", \"ROLE_TECHNICIEN\"]', 'technicien'),
(8, 'Tech Support BDD', 'tech.bdd@3innov.fr', '$2y$13$QshUEAv5glQCT8QzbaSdlOs/4gGgQjrzqJ2zsvrVYutWo8yL3E7IK', '[\"ROLE_USER\", \"ROLE_TECHNICIEN\"]', 'technicien'),
(9, 'Jean Dupont', 'jean.dupont@3innov.fr', '$2y$13$zAT6Y5htnpLb1YN0GDsVf.nvDPJ03pr1r1DRF5bmbN5J6ugad0hbe', '[\"ROLE_USER\"]', 'utilisateur'),
(10, 'Marie Martin', 'marie.martin@3innov.fr', '$2y$13$4h64zooa0Tm.yab4No0gYesuaFiKnUYdbGhHalvcD.hWCW8/8uCrS', '[\"ROLE_USER\"]', 'utilisateur'),
(11, 'Pierre Durant', 'pierre.durant@3innov.fr', '$2y$13$MuWwJvx1HPHD0hVnN74bq.x6DdXaqKY7gULGAOO74XaMQmZmVmrQS', '[\"ROLE_USER\"]', 'utilisateur'),
(12, 'Sophie Bernard', 'sophie.bernard@3innov.fr', '$2y$13$ObdXRz0jaTHchMNRh3Pg9edDDITs6FZ02JnKNhrY4ui6Xki/YaPeq', '[\"ROLE_USER\"]', 'utilisateur'),
(13, 'Lucas Petit', 'lucas.petit@3innov.fr', '$2y$13$WRG98hDIXKblEDfowghA9.EM.51RHTWKGpTO6M9TWzgmXiCLZSHpG', '[\"ROLE_USER\"]', 'utilisateur'),
(14, 'Emma Richard', 'emma.richard@3innov.fr', '$2y$13$Oi1w.cdyTD0ZacFJsxTJaOGbYW2.bVuek.aEnWApgwf8NTuL3rSxC', '[\"ROLE_USER\"]', 'utilisateur'),
(15, 'ratebTech', 'ratibtech@gmail.com', '$2y$13$5WbsTbghSXo88yZ6YF/AqOiWUOMqjvzZcstlwahfz1q2T7QI3aKVC', '[\"ROLE_USER\", \"ROLE_TECHNICIEN\"]', 'technicien'),
(16, 'pipi', 'ratib132@gmail.com', '$2y$13$9U6nBmWZmZF73k4dZF2Cc.xelPMvt.xA8hK3X8Q.3lFH/L/cskJ8W', '[\"ROLE_USER\"]', 'utilisateur'),
(17, 'ratebTEST', 'tutii10er@gmail.com', '$2y$13$GIT3FEK7qXj54ixz1obS7e5y.S8Lj8flWDNzzveoK3Rg0viWNspvG', '[\"ROLE_USER\"]', 'utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `rapport`
--

DROP TABLE IF EXISTS `rapport`;
CREATE TABLE IF NOT EXISTS `rapport` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ticket_principal_id` int DEFAULT NULL,
  `auteur_id` int NOT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenu` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` datetime NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `service` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statistiques` json DEFAULT NULL,
  `temps_passe` int DEFAULT NULL,
  `recommandations` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `IDX_BE34A09C879E8C45` (`ticket_principal_id`),
  KEY `IDX_BE34A09C60BB6FE6` (`auteur_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rapport`
--

INSERT INTO `rapport` (`id`, `ticket_principal_id`, `auteur_id`, `titre`, `contenu`, `date_creation`, `type`, `periode`, `service`, `statistiques`, `temps_passe`, `recommandations`) VALUES
(2, NULL, 2, 'zdq', 'dqd', '2025-03-10 19:06:17', 'statistiques', 'hebdomadaire', 'dqzdqzd', '{\"total_tickets\": 31, \"tickets_par_statut\": {\"nouveau\": 25, \"résolu\": 1, \"en cours\": 4, \"clôturé\": 1}, \"tickets_par_categorie\": {\"Cloud\": 3, \"Réseau\": 6, \"Logiciel\": 6, \"Matériel\": 5, \"Sécurité\": 3, \"Maintenance\": 3, \"Base de données\": 5}, \"delai_moyen_resolution\": 1}', NULL, NULL),
(3, 52, 4, 'dqzd', 'dqd', '2025-03-10 19:07:07', 'intervention', NULL, 'dqzd', NULL, 4, 'dqzd'),
(4, 55, 15, 'intervention sur pc', 'dzq', '2025-05-23 22:44:05', 'intervention', NULL, 'IT', NULL, 8, 'dqzdqz'),
(5, NULL, 1, 'client 1', 'qzdqzd', '2025-05-23 22:44:55', 'statistiques', 'mensuel', 'fd', '{\"total_tickets\": 1, \"tickets_par_statut\": {\"résolu\": 1}, \"tickets_par_categorie\": {\"Sécurité\": 1}, \"delai_moyen_resolution\": 0}', NULL, NULL),
(6, 3, 4, 'intervention sur pc', 'sf', '2025-05-30 21:16:31', 'intervention', NULL, 'IT', NULL, 4, 'sdf'),
(7, NULL, 1, 'client 1', 'jghuygfh', '2025-05-30 23:18:00', 'statistiques', 'hebdomadaire', 'dzqd', '{\"total_tickets\": 2, \"tickets_par_statut\": {\"en cours\": 1, \"clôturé\": 1}, \"tickets_par_categorie\": {\"Sécurité\": 1, \"Maintenance\": 1}, \"delai_moyen_resolution\": 0}', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `rapport_ticket`
--

DROP TABLE IF EXISTS `rapport_ticket`;
CREATE TABLE IF NOT EXISTS `rapport_ticket` (
  `rapport_id` int NOT NULL,
  `ticket_id` int NOT NULL,
  PRIMARY KEY (`rapport_id`,`ticket_id`),
  KEY `IDX_F6EB60691DFBCC46` (`rapport_id`),
  KEY `IDX_F6EB6069700047D2` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `rapport_ticket`
--

INSERT INTO `rapport_ticket` (`rapport_id`, `ticket_id`) VALUES
(2, 7),
(2, 10),
(2, 11),
(2, 18),
(2, 19),
(2, 22),
(2, 26),
(2, 29),
(2, 30),
(2, 31),
(2, 32),
(2, 33),
(2, 34),
(2, 35),
(2, 36),
(2, 37),
(2, 38),
(2, 39),
(2, 40),
(2, 41),
(2, 42),
(2, 43),
(2, 44),
(2, 45),
(2, 46),
(2, 47),
(2, 48),
(2, 49),
(2, 50),
(2, 51),
(2, 52),
(5, 55),
(7, 55),
(7, 56);

-- --------------------------------------------------------

--
-- Structure de la table `snmp`
--

DROP TABLE IF EXISTS `snmp`;
CREATE TABLE IF NOT EXISTS `snmp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `technicien`
--

DROP TABLE IF EXISTS `technicien`;
CREATE TABLE IF NOT EXISTS `technicien` (
  `id` int NOT NULL,
  `specialite` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `technicien`
--

INSERT INTO `technicien` (`id`, `specialite`) VALUES
(4, 'Réseau'),
(5, 'Hardware'),
(6, 'Software'),
(7, 'Cybersécurité'),
(8, 'BDD'),
(15, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ticket`
--

DROP TABLE IF EXISTS `ticket`;
CREATE TABLE IF NOT EXISTS `ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `technicien_id` int DEFAULT NULL,
  `categorie_id` int NOT NULL,
  `chatbox_id` int DEFAULT NULL,
  `titre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priorite` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_resolution` datetime DEFAULT NULL,
  `date_cloture` datetime DEFAULT NULL,
  `solution` longtext COLLATE utf8mb4_unicode_ci,
  `solution_validee` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_97A0ADA353527A38` (`chatbox_id`),
  KEY `IDX_97A0ADA3FB88E14F` (`utilisateur_id`),
  KEY `IDX_97A0ADA313457256` (`technicien_id`),
  KEY `IDX_97A0ADA3BCF5E72D` (`categorie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ticket`
--

INSERT INTO `ticket` (`id`, `utilisateur_id`, `technicien_id`, `categorie_id`, `chatbox_id`, `titre`, `description`, `statut`, `priorite`, `date_creation`, `date_resolution`, `date_cloture`, `solution`, `solution_validee`) VALUES
(1, 13, NULL, 2, NULL, 'Ticket #1 - Problème de messagerie', 'L\'écran affiche des artefacts graphiques par intermittence.', 'nouveau', 'basse', '2025-02-21 19:00:28', NULL, NULL, NULL, 0),
(2, 9, 7, 3, NULL, 'Ticket #2 - Erreur de synchronisation', 'L\'écran affiche des artefacts graphiques par intermittence.', 'en cours', 'basse', '2025-02-15 19:00:28', NULL, NULL, NULL, 0),
(3, 11, 8, 7, NULL, 'Ticket #3 - Problème de connexion', 'L\'application se ferme de manière inattendue lors de l\'exportation de données.', 'en cours', 'haute', '2025-02-09 19:00:28', NULL, NULL, NULL, 0),
(4, 12, 4, 1, NULL, 'Ticket #4 - Mise à jour requise', 'L\'utilisateur signale une lenteur importante lors de l\'accès aux ressources réseau.', 'résolu', 'haute', '2025-02-19 19:00:28', '2025-05-29 10:04:05', NULL, '<', 0),
(5, 9, NULL, 3, NULL, 'Ticket #5 - Problème de messagerie', 'L\'application se ferme de manière inattendue lors de l\'exportation de données.', 'nouveau', 'normale', '2025-02-17 19:00:28', NULL, NULL, NULL, 0),
(6, 12, 4, 1, NULL, 'Ticket #6 - Erreur de synchronisation', 'L\'application se ferme de manière inattendue lors de l\'exportation de données.', 'en cours', 'normale', '2025-02-10 19:00:28', NULL, NULL, NULL, 0),
(7, 11, 8, 6, NULL, 'Ticket #7 - Erreur de synchronisation', 'L\'imprimante affiche une erreur de communication et refuse d\'imprimer.', 'en cours', 'normale', '2025-03-09 19:00:28', NULL, NULL, NULL, 0),
(8, 13, NULL, 4, NULL, 'Ticket #8 - Problème d\'impression', 'L\'écran affiche des artefacts graphiques par intermittence.', 'nouveau', 'normale', '2025-02-16 19:00:28', NULL, NULL, NULL, 0),
(9, 14, NULL, 6, NULL, 'Ticket #9 - Mise à jour requise', 'Problèmes de synchronisation avec le serveur de messagerie.', 'nouveau', 'basse', '2025-02-25 19:00:28', NULL, NULL, NULL, 0),
(10, 9, 5, 5, NULL, 'Ticket #10 - Performance dégradée', 'Impossible d\'accéder à certains dossiers partagés depuis ce matin.', 'clôturé', 'haute', '2025-03-07 19:00:28', '2025-03-05 19:00:28', '2025-03-10 19:00:28', 'Solution appliquée : Optimisation des paramètres système et nettoyage des fichiers temporaires.', 1),
(11, 12, NULL, 4, NULL, 'Ticket #11 - Performance dégradée', 'L\'utilisateur signale une lenteur importante lors de l\'accès aux ressources réseau.', 'nouveau', 'normale', '2025-03-07 19:00:28', NULL, NULL, NULL, 0),
(12, 9, NULL, 4, NULL, 'Ticket #12 - Mise à jour requise', 'Problèmes de synchronisation avec le serveur de messagerie.', 'nouveau', 'haute', '2025-02-11 19:00:28', NULL, NULL, NULL, 0),
(13, 13, NULL, 6, NULL, 'Ticket #13 - Problème de messagerie', 'L\'écran affiche des artefacts graphiques par intermittence.', 'nouveau', 'haute', '2025-02-09 19:00:28', NULL, NULL, NULL, 0),
(14, 14, NULL, 5, NULL, 'Ticket #14 - Performance dégradée', 'Impossible d\'accéder à certains dossiers partagés depuis ce matin.', 'nouveau', 'normale', '2025-02-25 19:00:28', NULL, NULL, NULL, 0),
(15, 12, NULL, 7, NULL, 'Ticket #15 - Performance dégradée', 'L\'utilisateur signale une lenteur importante lors de l\'accès aux ressources réseau.', 'nouveau', 'normale', '2025-02-28 19:00:28', NULL, NULL, NULL, 0),
(16, 12, NULL, 7, NULL, 'Ticket #16 - Erreur de synchronisation', 'La sauvegarde automatique ne s\'est pas exécutée cette nuit.', 'nouveau', 'normale', '2025-02-09 19:00:28', NULL, NULL, NULL, 0),
(17, 12, NULL, 3, NULL, 'Ticket #17 - Problème d\'impression', 'Le poste de travail ne démarre plus après la dernière mise à jour.', 'nouveau', 'normale', '2025-02-12 19:00:28', NULL, NULL, NULL, 0),
(18, 10, 5, 2, NULL, 'Ticket #18 - Problème de connexion', 'La sauvegarde automatique ne s\'est pas exécutée cette nuit.', 'résolu', 'normale', '2025-03-04 19:00:28', '2025-03-05 19:00:28', NULL, 'Solution appliquée : Restauration depuis la dernière sauvegarde fonctionnelle.', 0),
(19, 13, 7, 1, NULL, 'Ticket #19 - Problème de connexion', 'Problèmes de synchronisation avec le serveur de messagerie.', 'en cours', 'normale', '2025-03-07 19:00:28', NULL, NULL, NULL, 0),
(20, 14, 5, 2, NULL, 'Ticket #20 - Erreur de synchronisation', 'Le poste de travail ne démarre plus après la dernière mise à jour.', 'en cours', 'urgente', '2025-02-25 19:00:28', NULL, NULL, NULL, 0),
(21, 9, 4, 7, NULL, 'Ticket #21 - Problème d\'impression', 'La sauvegarde automatique ne s\'est pas exécutée cette nuit.', 'en cours', 'haute', '2025-02-12 19:00:28', NULL, NULL, NULL, 0),
(22, 9, 4, 1, 28, 'Ticket #22 - Problème d\'impression', 'La sauvegarde automatique ne s\'est pas exécutée cette nuit.', 'résolu', 'haute', '2025-03-04 19:00:28', '2025-03-10 19:06:47', NULL, 'dzqdzq', 0),
(23, 10, 8, 6, NULL, 'Ticket #23 - Mise à jour requise', 'L\'écran affiche des artefacts graphiques par intermittence.', 'résolu', 'haute', '2025-02-09 19:00:28', '2025-03-05 19:00:28', NULL, 'Solution appliquée : Optimisation des paramètres système et nettoyage des fichiers temporaires.', 0),
(24, 10, 8, 7, NULL, 'Ticket #24 - Problème de connexion', 'L\'utilisateur signale une lenteur importante lors de l\'accès aux ressources réseau.', 'clôturé', 'normale', '2025-02-21 19:00:28', '2025-03-08 19:00:28', '2025-03-10 19:00:28', 'Solution appliquée : Installation des dernières mises à jour et nettoyage du cache.', 1),
(25, 9, 8, 7, NULL, 'Ticket #25 - Performance dégradée', 'L\'écran affiche des artefacts graphiques par intermittence.', 'clôturé', 'basse', '2025-02-20 19:00:28', '2025-03-08 19:00:28', '2025-03-10 19:00:28', 'Solution appliquée : Installation des dernières mises à jour et nettoyage du cache.', 1),
(26, 13, 8, 7, NULL, 'Ticket #26 - Problème de messagerie', 'L\'écran affiche des artefacts graphiques par intermittence.', 'en cours', 'normale', '2025-03-04 19:00:28', NULL, NULL, NULL, 0),
(27, 13, 8, 3, NULL, 'Ticket #27 - Problème d\'impression', 'L\'utilisateur signale une lenteur importante lors de l\'accès aux ressources réseau.', 'en cours', 'normale', '2025-02-11 19:00:28', NULL, NULL, NULL, 0),
(28, 13, 8, 6, NULL, 'Ticket #28 - Problème d\'impression', 'Le poste de travail ne démarre plus après la dernière mise à jour.', 'clôturé', 'normale', '2025-02-09 19:00:28', '2025-03-09 19:00:28', '2025-03-10 19:29:30', 'Solution appliquée : Remplacement du matériel défectueux et mise à jour du firmware.', 1),
(29, 11, NULL, 6, NULL, 'Ticket #29 - Erreur de synchronisation', 'L\'application se ferme de manière inattendue lors de l\'exportation de données.', 'nouveau', 'normale', '2025-03-08 19:00:28', NULL, NULL, NULL, 0),
(30, 11, NULL, 7, NULL, 'Ticket #30 - Problème d\'impression', 'La sauvegarde automatique ne s\'est pas exécutée cette nuit.', 'nouveau', 'normale', '2025-03-06 19:00:28', NULL, NULL, NULL, 0),
(31, 4, NULL, 2, 1, 'Problème #1-1', 'Description du problème #1-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(32, 4, NULL, 6, 2, 'Problème #1-2', 'Description du problème #1-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(33, 5, NULL, 3, 3, 'Problème #2-1', 'Description du problème #2-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(34, 5, NULL, 1, 4, 'Problème #2-2', 'Description du problème #2-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(35, 6, NULL, 3, 5, 'Problème #3-1', 'Description du problème #3-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(36, 6, NULL, 2, 6, 'Problème #3-2', 'Description du problème #3-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(37, 7, NULL, 3, 7, 'Problème #4-1', 'Description du problème #4-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(38, 7, NULL, 1, 8, 'Problème #4-2', 'Description du problème #4-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(39, 8, NULL, 1, 9, 'Problème #5-1', 'Description du problème #5-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(40, 8, NULL, 7, 10, 'Problème #5-2', 'Description du problème #5-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(41, 9, NULL, 4, 11, 'Problème #6-1', 'Description du problème #6-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(42, 9, NULL, 1, 12, 'Problème #6-2', 'Description du problème #6-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(43, 10, NULL, 7, 13, 'Problème #7-1', 'Description du problème #7-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(44, 10, NULL, 3, 14, 'Problème #7-2', 'Description du problème #7-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(45, 11, NULL, 7, 15, 'Problème #8-1', 'Description du problème #8-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(46, 11, NULL, 5, 16, 'Problème #8-2', 'Description du problème #8-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(47, 12, NULL, 4, 17, 'Problème #9-1', 'Description du problème #9-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(48, 12, NULL, 5, 18, 'Problème #9-2', 'Description du problème #9-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(49, 13, NULL, 2, 19, 'Problème #10-1', 'Description du problème #10-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(50, 13, NULL, 2, 20, 'Problème #10-2', 'Description du problème #10-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(51, 14, NULL, 3, 21, 'Problème #11-1', 'Description du problème #11-1', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(52, 14, NULL, 3, 22, 'Problème #11-2', 'Description du problème #11-2', 'nouveau', 'normale', '2025-03-10 19:00:29', NULL, NULL, NULL, 0),
(53, 13, 4, 3, 24, 'jsp', 'Conversation avec l\'assistant IA:\r\n\r\n- hello', 'en cours', 'haute', '2025-03-10 19:07:47', NULL, NULL, NULL, 0),
(54, 13, 4, 1, 25, 'dqdqz', 'Conversation avec l\'assistant IA:\r\n\r\n- hello\r\n- merci\r\n- ok', 'résolu', 'haute', '2025-03-10 19:22:31', '2025-05-29 10:14:11', NULL, 'voilà', 0),
(55, 16, 15, 4, 27, 'dqdqz', 'Conversation avec l\'assistant IA:\r\n\r\n- hello comment ça va\r\n- comment crée un ticket', 'clôturé', 'haute', '2025-05-23 22:42:05', '2025-05-23 22:43:37', '2025-05-25 17:25:49', 'blablabla', 1),
(56, 16, 4, 5, 29, 'test chat', 'Conversation avec l\'assistant IA:\r\n\r\n- hello comment ça va\r\n- comment crée un ticket', 'résolu', 'normale', '2025-05-29 10:15:29', '2025-05-31 14:38:29', NULL, NULL, 0),
(57, 16, NULL, 3, 30, 'test 123', 'Conversation avec l\'assistant IA:\r\n\r\n- hello comment ça va\r\n- comment crée un ticket\r\n- merci\r\n- je veux te remercier c\'est tout\r\n- salut mon pote', 'nouveau', 'normale', '2025-05-31 08:48:03', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`) VALUES
(4),
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12),
(13),
(14),
(15),
(16),
(17);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD CONSTRAINT `FK_32EB52E81DFBCC46` FOREIGN KEY (`rapport_id`) REFERENCES `rapport` (`id`),
  ADD CONSTRAINT `FK_32EB52E8BF396750` FOREIGN KEY (`id`) REFERENCES `personne` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD CONSTRAINT `FK_497DD6347EE5403C` FOREIGN KEY (`administrateur_id`) REFERENCES `administrateur` (`id`);

--
-- Contraintes pour la table `chatbox`
--
ALTER TABLE `chatbox`
  ADD CONSTRAINT `FK_7472FC2F489A6E65` FOREIGN KEY (`ia_id`) REFERENCES `ia` (`id`),
  ADD CONSTRAINT `FK_7472FC2FA76ED395` FOREIGN KEY (`user_id`) REFERENCES `personne` (`id`);

--
-- Contraintes pour la table `commentaire`
--
ALTER TABLE `commentaire`
  ADD CONSTRAINT `FK_67F068BC60BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_67F068BC700047D2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`);

--
-- Contraintes pour la table `equipement`
--
ALTER TABLE `equipement`
  ADD CONSTRAINT `FK_B8B4C6F33303B12C` FOREIGN KEY (`s_nmp_id`) REFERENCES `snmp` (`id`);

--
-- Contraintes pour la table `mail`
--
ALTER TABLE `mail`
  ADD CONSTRAINT `FK_5126AC48FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `FK_B6BD307F53527A38` FOREIGN KEY (`chatbox_id`) REFERENCES `chatbox` (`id`);

--
-- Contraintes pour la table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `FK_BF5476CA700047D2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`),
  ADD CONSTRAINT `FK_BF5476CAFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `rapport`
--
ALTER TABLE `rapport`
  ADD CONSTRAINT `FK_BE34A09C60BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `personne` (`id`),
  ADD CONSTRAINT `FK_BE34A09C879E8C45` FOREIGN KEY (`ticket_principal_id`) REFERENCES `ticket` (`id`);

--
-- Contraintes pour la table `rapport_ticket`
--
ALTER TABLE `rapport_ticket`
  ADD CONSTRAINT `FK_F6EB60691DFBCC46` FOREIGN KEY (`rapport_id`) REFERENCES `rapport` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F6EB6069700047D2` FOREIGN KEY (`ticket_id`) REFERENCES `ticket` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `technicien`
--
ALTER TABLE `technicien`
  ADD CONSTRAINT `FK_96282C4CBF396750` FOREIGN KEY (`id`) REFERENCES `personne` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA313457256` FOREIGN KEY (`technicien_id`) REFERENCES `technicien` (`id`),
  ADD CONSTRAINT `FK_97A0ADA353527A38` FOREIGN KEY (`chatbox_id`) REFERENCES `chatbox` (`id`),
  ADD CONSTRAINT `FK_97A0ADA3BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`),
  ADD CONSTRAINT `FK_97A0ADA3FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `FK_1D1C63B3BF396750` FOREIGN KEY (`id`) REFERENCES `personne` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
