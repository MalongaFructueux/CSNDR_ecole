-- CSNDR Database - Version Finale Corrigée
-- Base de données optimisée pour production IONOS
-- InnoDB + Contraintes FK + Champs corrects

SET NAMES utf8mb4;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classes_nom_unique` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `classes` (`id`, `nom`, `created_at`, `updated_at`) VALUES
(1, 'CP-A', '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(2, 'CE1-A', '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(3, 'CE2-A', '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(4, 'CM1-A', '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(5, 'CM2-A', '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(6, '6eme B', '2025-08-11 19:42:12', '2025-08-11 19:42:12'),
(7, 'CE1-Test', '2025-08-15 12:09:51', '2025-08-15 12:09:51');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','professeur','parent','eleve') COLLATE utf8mb4_unicode_ci NOT NULL,
  `classe_id` bigint UNSIGNED DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_classe_id_foreign` (`classe_id`),
  KEY `users_parent_id_foreign` (`parent_id`),
  CONSTRAINT `users_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password`, `role`, `classe_id`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Principal', 'admin@csndr.test', '$2y$10$nGX3hcwn3cniwHDNgB/fsuldrpkIiT5I7OLkrHqWvfgx5NTshs6PK', 'admin', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(2, 'Dupont', 'Marie', 'marie.dupont@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professeur', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(3, 'Martin', 'Jean', 'jean.martin@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professeur', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(4, 'Moreau', 'Sophie', 'sophie.moreau@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professeur', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(5, 'Leroy', 'Pierre', 'pierre.leroy@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(6, 'Bernard', 'Claire', 'claire.bernard@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(7, 'Roux', 'Antoine', 'antoine.roux@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(8, 'Leroy', 'Lucas', 'lucas.leroy@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eleve', 1, 5, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(9, 'Bernard', 'Emma', 'emma.bernard@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eleve', 2, 6, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(10, 'Roux', 'Hugo', 'hugo.roux@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eleve', 3, 7, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(11, 'Petit', 'Camille', 'camille.petit@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eleve', 4, 5, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(12, 'Garnier', 'Louis', 'louis.garnier@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eleve', 5, 6, '2025-08-10 17:14:55', '2025-08-10 17:14:55');

-- --------------------------------------------------------

--
-- Structure de la table `devoirs`
--

DROP TABLE IF EXISTS `devoirs`;
CREATE TABLE IF NOT EXISTS `devoirs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_limite` date NOT NULL,
  `professeur_id` bigint UNSIGNED NOT NULL,
  `classe_id` bigint UNSIGNED NOT NULL,
  `fichier_attachment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom_fichier_original` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_fichier` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `devoirs_professeur_id_foreign` (`professeur_id`),
  KEY `devoirs_classe_id_foreign` (`classe_id`),
  CONSTRAINT `devoirs_professeur_id_foreign` FOREIGN KEY (`professeur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `devoirs_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evenements` (Compatible avec EventController)
--

DROP TABLE IF EXISTS `evenements`;
CREATE TABLE IF NOT EXISTS `evenements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `auteur_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evenements_auteur_id_foreign` (`auteur_id`),
  CONSTRAINT `evenements_auteur_id_foreign` FOREIGN KEY (`auteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `evenements` (`id`, `titre`, `description`, `date`, `auteur_id`, `created_at`, `updated_at`) VALUES
(1, 'Réunion Parents-Professeurs', 'Réunion générale pour discuter des progrès des élèves', '2025-09-15 18:00:00', 1, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(2, 'Sortie Scolaire', 'Visite au musée local', '2025-10-05 09:00:00', 2, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(3, 'Vacances de Noël', 'Période de vacances', '2025-12-20 00:00:00', 1, '2025-08-10 17:14:55', '2025-08-10 17:14:55');

-- --------------------------------------------------------

--
-- Structure de la table `messages` (Compatible avec MessageController)
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `expediteur_id` bigint UNSIGNED NOT NULL,
  `destinataire_id` bigint UNSIGNED NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `date_envoi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_expediteur_id_foreign` (`expediteur_id`),
  KEY `messages_destinataire_id_foreign` (`destinataire_id`),
  CONSTRAINT `messages_expediteur_id_foreign` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_destinataire_id_foreign` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `messages` (`id`, `expediteur_id`, `destinataire_id`, `contenu`, `lu`, `date_envoi`, `created_at`, `updated_at`) VALUES
(1, 2, 5, 'Bonjour, votre enfant fait de bons progrès en maths.', 0, '2025-08-10 17:14:55', '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(2, 5, 2, 'Bonjour, j\'ai une question sur les devoirs de cette semaine.', 0, '2025-08-10 17:14:55', '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(3, 1, 2, 'Veuillez assister à la réunion demain.', 0, '2025-08-10 17:14:55', '2025-08-10 17:14:55', '2025-08-10 17:14:55');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

DROP TABLE IF EXISTS `notes`;
CREATE TABLE IF NOT EXISTS `notes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `eleve_id` bigint UNSIGNED NOT NULL,
  `matiere` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` decimal(5,2) NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `coefficient` decimal(3,1) NOT NULL DEFAULT '1.0',
  `date` date NOT NULL,
  `type_evaluation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Devoir',
  `professeur_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notes_eleve_id_foreign` (`eleve_id`),
  KEY `notes_professeur_id_foreign` (`professeur_id`),
  CONSTRAINT `notes_eleve_id_foreign` FOREIGN KEY (`eleve_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notes_professeur_id_foreign` FOREIGN KEY (`professeur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Réactiver les contraintes FK
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
