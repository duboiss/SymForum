-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le :  jeu. 18 juil. 2019 à 17:17
-- Version du serveur :  5.7.24
-- Version de PHP :  7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `sym_forum`
--

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` smallint(6) NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `title`, `position`, `slug`) VALUES
(5, 'Première catégorie', 2, 'premiere-categorie');

-- --------------------------------------------------------

--
-- Structure de la table `forum`
--

CREATE TABLE `forum` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `forum`
--

INSERT INTO `forum` (`id`, `category_id`, `title`, `description`, `slug`, `parent_id`) VALUES
(17, 5, 'Forum parent', 'Forum parent contenant des sous-forums', 'forum-parent', NULL),
(18, NULL, 'Sous-forum niveau 1', 'Un sous-forum de niveau 1', 'sous-forum-niveau-1', 17),
(19, NULL, 'Second sous-forum niveau 1', 'Le second sous-forum de niveau 1', 'second-sous-forum-niveau-1', 17),
(20, 5, 'Forum parent vierge', 'Un forum parent sans sous-forum', 'forum-parent-vierge', NULL),
(22, 5, 'Forum parent #2', 'Un second forum parent contenant des sous-forums', 'forum-parent-2', NULL),
(23, NULL, 'Sous-forum', 'Un sous forum !', 'un-sous-forum', 22),
(30, NULL, 'Sous-forum niveau 2', 'Un sous-forum de niveau 2', 'sous-forum-de-niveau-2', 18);

-- --------------------------------------------------------

--
-- Structure de la table `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migration_versions`
--

INSERT INTO `migration_versions` (`version`, `executed_at`) VALUES
('20190712131045', '2019-07-17 17:56:11'),
('20190712140240', '2019-07-17 17:56:12'),
('20190716145323', '2019-07-17 17:56:12'),
('20190716150247', '2019-07-17 17:56:12');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `pseudo`, `hash`, `email`, `registration_date`) VALUES
(26, 'mathilde.paris', '$argon2i$v=19$m=65536,t=6,p=1$YXBTNzBRS3JvUGhWVlJnMA$l1LhkyorPPVeAg7UVZJfyWciCZxmq5SFUsYS77tEjD0', 'andre.jean@aubert.fr', '2019-07-18 16:35:55'),
(27, 'gdias', '$argon2i$v=19$m=65536,t=6,p=1$RmVNNnk2NndRS2p6R3ROQQ$pl1wc9eOhFBQv0oPyqSDyqbe740czUgHIwmcGRC7mRM', 'gabrielle.legrand@sfr.fr', '2019-07-18 16:35:55'),
(28, 'gerard.torres', '$argon2i$v=19$m=65536,t=6,p=1$NmNwOUEuaUF3SlBCa3V0TQ$IlWwsbpNvDxNv1wUClEPeWVsYk8pyWLDXlWIU1rJLKE', 'benjamin.peron@morvan.org', '2019-07-18 16:35:55'),
(29, 'whardy', '$argon2i$v=19$m=65536,t=6,p=1$WVRFeWlnUDlTQUxLWXIxUQ$LDsm7gM2cI6F7HZ4YxMwoXKliwosmvQYjkkQDCwoUPU', 'slacombe@club-internet.fr', '2019-07-18 16:35:56'),
(30, 'rgauthier', '$argon2i$v=19$m=65536,t=6,p=1$bVQ2NGlaVU4veG1EU1YyRw$5w1fXwvXszMLwss2eDH7ogeOggTtUR6Pl86xlOCctfw', 'guy09@payet.com', '2019-07-18 16:35:56'),
(31, 'flecoq', '$argon2i$v=19$m=65536,t=6,p=1$L2pYVDluL3diR1JXdjdwVQ$no3zeFWgxdnSJe/c7bknkiN7QJmkf2UeZD94sMlJsC4', 'gauthier.marianne@tele2.fr', '2019-07-18 16:35:56'),
(32, 'nicolas02', '$argon2i$v=19$m=65536,t=6,p=1$RGpWYWVsUkFNc3NTMG1QNA$zwYq7J9KuORvg8sBddwYjosegifoDefhpM0wkxIgGvQ', 'gilles.martel@giraud.fr', '2019-07-18 16:35:57'),
(33, 'laporte.lucie', '$argon2i$v=19$m=65536,t=6,p=1$UXY4cWI1VFFac0JRZUY1YQ$xK8iKGvXN4HVWLvil/h+xZrgmq0UexitJ7jKeBD21Ts', 'uboulay@coulon.fr', '2019-07-18 16:35:57'),
(34, 'fabre.marianne', '$argon2i$v=19$m=65536,t=6,p=1$T01TbGlWZ1J2Qmk5M3RuaQ$UtBnMqLS6Xj4iip/S5P+rI1S96nw9qZkcymv8ujXye0', 'chauveau.dominique@neveu.fr', '2019-07-18 16:35:57'),
(35, 'emmanuel20', '$argon2i$v=19$m=65536,t=6,p=1$ZjdybnNnWHZCdDNFVlQzZg$Ol4+H0GKZig4nMy+IDi7UhxnImSfOvZi9pCuwRSN0Uo', 'alix82@girard.fr', '2019-07-18 16:35:58'),
(36, 'rey.alexandrie', '$argon2i$v=19$m=65536,t=6,p=1$WDFRQkQzZ2M3V1JXNVpNcQ$FJ6koYFIQPmS5TYdBLZmGwm7E97nLPm2uYpHzwHGUyg', 'pierre59@guillet.org', '2019-07-18 16:35:58'),
(37, 'cmarty', '$argon2i$v=19$m=65536,t=6,p=1$Z2J2OTczVU1GMzB4YlF2aQ$hFnt7MmzIVZmpP/lZQYDfgk73SNjRg9Ujs3byU0VuBU', 'claudine.raymond@ribeiro.fr', '2019-07-18 16:35:58'),
(38, 'ruiz.cecile', '$argon2i$v=19$m=65536,t=6,p=1$d0RFN2lUcTBlTkJxTUFvbQ$Sg//6siz4yzvOGB+272Q6+NBBTWaN5SioHx+f6Gm0Bo', 'isaac15@martineau.com', '2019-07-18 16:35:59'),
(39, 'riviere.madeleine', '$argon2i$v=19$m=65536,t=6,p=1$aDRCYlZ3cEZPazdIR3hMcA$G9NOrVRjBr13KkcqQBUwUTQmKdfSKTylMZ9LCvilI/Q', 'ucarpentier@free.fr', '2019-07-18 16:35:59'),
(40, 'louis08', '$argon2i$v=19$m=65536,t=6,p=1$MElFc2tMNVM5ZU9pS2xpMQ$5TNnSPBozPOOPggHsYKPC5xkvzY/g99xEfBPeHFoxT0', 'patrick85@sfr.fr', '2019-07-18 16:35:59'),
(41, 'guy.blanchard', '$argon2i$v=19$m=65536,t=6,p=1$OG45d3VyWi5OMmx6d3I0Lw$4HyMyz40L+AKcs0l3lOZqRSo+NRvD7ter9GlN93kL5E', 'lemaitre.richard@yahoo.fr', '2019-07-18 16:36:00'),
(42, 'edouard.pasquier', '$argon2i$v=19$m=65536,t=6,p=1$Yk1NZ0hXcFpZSlAxZmRGRA$GgwDfFnADLA4XxYoVK+QcU2TcfOlxOWX/FRvgqdpg3U', 'guerin.celina@bouygtel.fr', '2019-07-18 16:36:00'),
(43, 'jgirard', '$argon2i$v=19$m=65536,t=6,p=1$NlVoZnoxSzZOU1FTYUwyNA$ZLcDSRo0jWYD8h+IEsMffAe9SD0FD6KYdlRD0vh6sas', 'emmanuelle04@orange.fr', '2019-07-18 16:36:00'),
(44, 'timothee73', '$argon2i$v=19$m=65536,t=6,p=1$LlpmU2JBWUoySzZ2M3NWYg$GLcsYOqjPkD+OQw1kwymHeegigCkvnNN7vAO67cfmTU', 'gblanchet@moreno.fr', '2019-07-18 16:36:01'),
(45, 'thierry56', '$argon2i$v=19$m=65536,t=6,p=1$VXQ2T2x2Um1waHRVY3RWQg$1x7eFVhAsbkV1CTfS2ALJy6X97BBeGr9OuubqkTZ7YA', 'theophile.munoz@sfr.fr', '2019-07-18 16:36:01'),
(46, 'alice.royer', '$argon2i$v=19$m=65536,t=6,p=1$N0lRT0N1Ny4wLmVqYzNkdQ$knMu0ZKu+qt9FePy6G21SEnenT6ZQRuFpRcRfyHhOzs', 'denis.claude@laporte.com', '2019-07-18 16:36:01'),
(47, 'thomas61', '$argon2i$v=19$m=65536,t=6,p=1$ZWZZQXpPL05GTXBIRTNreg$MUF/CoFB3Itf7zt7K1hPs4vaLogsjP/ni4G1P4ocTqI', 'audrey.bonneau@pierre.org', '2019-07-18 16:36:02'),
(48, 'marc44', '$argon2i$v=19$m=65536,t=6,p=1$VG1XTGZ6djhYTE9GbE5wQw$Vdl7PYidGD5/H4kDtWdn2Ue7uHxzaUvda6fV5pjqEa0', 'alexandrie40@dbmail.com', '2019-07-18 16:36:02'),
(49, 'denis93', '$argon2i$v=19$m=65536,t=6,p=1$TkVvaEVOYURZMzBIcU1IZw$Q7Z7TorFs3RpM3Bfmv5C1YTg/+iVECNeBjN6OicBxGg', 'madeleine12@wanadoo.fr', '2019-07-18 16:36:02'),
(50, 'henri.jourdan', '$argon2i$v=19$m=65536,t=6,p=1$bmQ2OGFqa1VYUXBtZXM5Yg$QTRYmRbx2q8JseeLcEJCSMr+qxaMnVCz/bh1oVHkRX4', 'ohardy@voila.fr', '2019-07-18 16:36:03');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_852BBECD12469DE2` (`category_id`),
  ADD KEY `IDX_852BBECD727ACA70` (`parent_id`);

--
-- Index pour la table `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `forum`
--
ALTER TABLE `forum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `FK_852BBECD12469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  ADD CONSTRAINT `FK_852BBECD727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `forum` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
