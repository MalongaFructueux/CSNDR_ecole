-- CSNDR Database - Optimized Version
-- Centre Scolaire Notre Dame du Rosaire
-- Version optimisée avec InnoDB et contraintes FK
SET NAMES utf8mb4;

-- Configuration pour InnoDB et contraintes
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `csndr_db`
-- Version optimisée pour la production
--

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

--
-- Déchargement des données de la table `classes`
--

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
-- Structure de la table `users` (CRÉÉE AVANT LES AUTRES TABLES QUI LA RÉFÉRENCENT)
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password`, `role`, `classe_id`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Système', 'admin@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(2, 'Dupont', 'Marie', 'marie.dupont@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professeur', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(3, 'Martin', 'Pierre', 'pierre.martin@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'parent', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(4, 'Martin', 'Lucas', 'lucas.martin@csndr.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'eleve', 1, 3, '2025-08-10 17:14:55', '2025-08-10 17:14:55');

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `devoirs`
--

INSERT INTO `devoirs` (`id`, `titre`, `description`, `date_limite`, `professeur_id`, `classe_id`, `fichier_attachment`, `nom_fichier_original`, `type_fichier`, `created_at`, `updated_at`) VALUES
(11, 'jukuku', 'tukuktu', '2025-08-31', 1, 3, 'devoirs/2025/08/devoir_1755173077_689dd0d5b5784.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-14 10:04:37', '2025-08-14 10:04:37'),
(4, 'ert', 'etgt', '2025-08-27', 1, 5, 'devoirs/2025/08/devoir_1755003647_689b3affc5bce.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-12 11:00:48', '2025-08-12 11:00:48'),
(10, 'yuoyuo', 'yuoyuoy', '2025-08-17', 1, 4, 'devoirs/2025/08/devoir_1755172504_689dce9829de0.pdf', 'CSNDR_5.7_Methode_Agile.pdf', 'application/pdf', '2025-08-14 09:55:04', '2025-08-14 09:55:04'),
(9, 'yfuku', 'uyououo', '2025-08-24', 1, 2, 'devoirs/2025/08/devoir_1755172481_689dce814f7bf.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-14 09:54:41', '2025-08-14 09:54:41'),
(7, 'mllmolil', 'gyy', '2025-08-24', 1, 2, 'devoirs/2025/08/devoir_1755005318_689b418651969.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-12 11:28:38', '2025-08-12 11:28:38'),
(12, 'ktutukt', 'tuktuktuk', '2025-08-16', 1, 3, 'devoirs/2025/08/devoir_1755173105_689dd0f105a2e.pdf', 'Information entreprise M1 Informatique - 2025.pdf', 'application/pdf', '2025-08-14 10:05:05', '2025-08-14 10:05:05'),
(13, 'PROF', 'uryurtu', '2025-08-23', 4, 2, 'devoirs/2025/08/devoir_1755173156_689dd124e6cbd.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-14 10:05:56', '2025-08-14 10:05:56'),
(14, 'Carine', 'carine prof', '2025-08-30', 1, 2, 'devoirs/2025/08/devoir_1755265045_689f381585027.pdf', 'Mes relevés de comptes de mars à juin 2025 (1).pdf', 'application/pdf', '2025-08-15 11:37:25', '2025-08-15 11:37:25'),
(15, 'Carine 2', 'carine 2 devoir', '2025-08-31', 19, 2, 'devoirs/2025/08/devoir_1755265186_689f38a2c249f.pdf', 'Convention de stage - SCHOLIA maj 12-03-25.pdf', 'application/pdf', '2025-08-15 11:39:46', '2025-08-15 11:39:46'),
(16, 'érét', '\'t\'é', '2025-08-18', 1, 1, 'devoirs/2025/08/devoir_1755266505_689f3dc9d04c3.pdf', 'CV MALONGA FRUCTUEUX(vendeur).pdf.pdf', 'application/pdf', '2025-08-15 12:01:45', '2025-08-15 12:01:45'),
(17, 'Devoir Test Mathématiques', 'Exercices de mathématiques pour tester le système parent', '2025-08-22', 20, 7, NULL, NULL, NULL, '2025-08-15 12:09:52', '2025-08-15 12:09:52'),
(18, 'Devoir Test Français', 'Exercices de français pour tester le système parent', '2025-08-25', 20, 7, NULL, NULL, NULL, '2025-08-15 12:09:52', '2025-08-15 12:09:52'),
(19, 'Carinetytyertyer', 'gbttyt', '2025-08-17', 1, 2, 'devoirs/2025/08/devoir_1755267761_689f42b1877d8.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-15 12:22:41', '2025-08-15 12:22:41'),
(20, 'Devoir Test Parent-Enfant', 'Devoir créé automatiquement pour tester le système parent-enfant', '2025-08-22', 3, 1, NULL, NULL, NULL, '2025-08-15 12:24:22', '2025-08-15 12:24:22'),
(21, 'gfnfgn', 'dghdfhdf', '2025-08-25', 1, 2, 'devoirs/2025/08/devoir_1755268752_689f469021172.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-15 12:39:12', '2025-08-15 12:39:12'),
(22, 'EFEF', 'FRFR', '2025-08-25', 1, 1, 'devoirs/2025/08/devoir_1755268928_689f47400239e.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-15 12:42:08', '2025-08-15 12:42:08'),
(23, 'SEO', 'SE', '2025-08-24', 1, 1, 'devoirs/2025/08/devoir_1755269619_689f49f37de87.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-15 12:53:39', '2025-08-15 12:53:39'),
(24, 'Elene', 'elene', '2025-08-24', 1, 1, 'devoirs/2025/08/devoir_1755269635_689f4a0352485.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-15 12:53:55', '2025-08-15 12:53:55'),
(25, 'carine', 'QDGSD', '2025-08-16', 30, 1, 'devoirs/2025/08/devoir_1755270778_689f4e7aaa539.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-15 13:12:58', '2025-08-15 13:12:58'),
(26, 'DER', 'DER', '2025-08-18', 30, 2, 'devoirs/2025/08/devoir_1755270841_689f4eb9a731d.pdf', 'CV MALONGA FRUCTUEUX (technicien systeme et reseau) (1).pdf', 'application/pdf', '2025-08-15 13:14:01', '2025-08-15 13:14:01');

-- --------------------------------------------------------

--
-- Structure de la table `evenements`
--

DROP TABLE IF EXISTS `evenements`;
CREATE TABLE IF NOT EXISTS `evenements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `auteur_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evenements_auteur_id_foreign` (`auteur_id`),
  CONSTRAINT `evenements_auteur_id_foreign` FOREIGN KEY (`auteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `evenements`
--

INSERT INTO `evenements` (`id`, `titre`, `description`, `date`, `auteur_id`, `created_at`, `updated_at`) VALUES
(1, 'Demain on bois', 'rrr', '2025-08-31', 1, '2025-08-10 17:17:44', '2025-08-10 17:17:44'),
(2, 'Fructueux', 'Malonga', '2025-08-23', 1, '2025-08-11 19:42:24', '2025-08-11 19:42:24'),
(3, 'Bo', 'dzz', '2025-08-16', 1, '2025-08-15 11:36:23', '2025-08-15 11:36:23');

-- --------------------------------------------------------

--
-- Structure de la table `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `jour` date NOT NULL,
  `plat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `allergenes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menus_jour_unique` (`jour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `expediteur_id` bigint UNSIGNED NOT NULL,
  `destinataire_id` bigint UNSIGNED NOT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lu` tinyint(1) NOT NULL DEFAULT '0',
  `date_envoi` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_expediteur_id_foreign` (`expediteur_id`),
  KEY `messages_destinataire_id_foreign` (`destinataire_id`),
  CONSTRAINT `messages_expediteur_id_foreign` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_destinataire_id_foreign` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `expediteur_id`, `destinataire_id`, `contenu`, `lu`, `date_envoi`, `created_at`, `updated_at`) VALUES
(1, 1, 14, 'oiuoi', 0, '2025-08-12 21:16:48', '2025-08-12 19:16:48', '2025-08-12 19:16:48'),
(2, 1, 5, 'Hey druand', 1, '2025-08-13 11:15:55', '2025-08-13 09:15:55', '2025-08-13 09:36:01'),
(3, 5, 1, 'ya quoi boss', 1, '2025-08-13 11:16:26', '2025-08-13 09:16:26', '2025-08-13 09:26:28'),
(4, 1, 14, 'rien petit', 0, '2025-08-13 11:26:40', '2025-08-13 09:26:40', '2025-08-13 09:26:40'),
(5, 1, 14, 'zrgry', 0, '2025-08-13 11:35:22', '2025-08-13 09:35:22', '2025-08-13 09:35:22'),
(6, 1, 5, 'petut', 1, '2025-08-13 11:35:35', '2025-08-13 09:35:35', '2025-08-13 09:36:01'),
(7, 5, 1, 'ekiee mon chere', 1, '2025-08-13 11:36:14', '2025-08-13 09:36:14', '2025-08-13 09:36:29'),
(8, 1, 15, 'petit', 0, '2025-08-13 11:38:23', '2025-08-13 09:38:23', '2025-08-13 09:38:23'),
(9, 1, 6, 'rrrr', 1, '2025-08-13 11:40:23', '2025-08-13 09:40:23', '2025-08-13 09:40:44'),
(10, 6, 1, 'PETIT', 1, '2025-08-13 11:40:51', '2025-08-13 09:40:51', '2025-08-13 09:41:13'),
(11, 1, 16, 'bienvenue', 1, '2025-08-14 11:25:03', '2025-08-14 09:25:03', '2025-08-14 09:25:12'),
(12, 16, 1, 'merci', 1, '2025-08-14 11:25:18', '2025-08-14 09:25:18', '2025-08-14 09:28:41'),
(13, 1, 18, 'Parent', 1, '2025-08-15 13:36:41', '2025-08-15 11:36:41', '2025-08-15 11:40:21'),
(14, 1, 17, 'eleve', 0, '2025-08-15 13:36:50', '2025-08-15 11:36:50', '2025-08-15 11:36:50'),
(15, 1, 19, 'prof', 1, '2025-08-15 13:36:59', '2025-08-15 11:36:59', '2025-08-15 11:38:51'),
(16, 19, 1, 'recu', 1, '2025-08-15 13:38:55', '2025-08-15 11:38:55', '2025-08-15 11:59:42'),
(17, 18, 1, 'paretn recu*', 1, '2025-08-15 13:40:26', '2025-08-15 11:40:26', '2025-08-15 11:59:41'),
(18, 21, 1, 'errr', 1, '2025-08-15 14:19:18', '2025-08-15 12:19:18', '2025-08-15 12:19:31'),
(19, 30, 1, 'devoir fais', 1, '2025-08-15 15:14:15', '2025-08-15 13:14:15', '2025-08-15 13:16:08'),
(20, 34, 30, 'bien recu', 1, '2025-08-15 15:14:51', '2025-08-15 13:14:51', '2025-08-15 13:15:48'),
(21, 31, 30, 'note d\'angela bien recu', 1, '2025-08-15 15:15:34', '2025-08-15 13:15:34', '2025-08-15 13:15:47');

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(9, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(10, '2025_08_09_132621_create_users_table', 1),
(11, '2025_08_09_132627_create_classes_table', 1),
(12, '2025_08_09_132633_create_devoirs_table', 1),
(13, '2025_08_09_132639_create_notes_table', 1),
(14, '2025_08_09_132644_create_evenements_table', 1),
(15, '2025_08_09_132650_create_menus_table', 1),
(16, '2025_08_09_132720_create_messages_table', 1),
(17, '2025_08_10_220000_add_commentaire_to_notes_table', 2),
(18, '2025_08_13_111907_add_lu_to_messages_table', 3),
(19, '2025_08_14_122500_add_type_evaluation_to_notes_table', 4);

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

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'auth_token', '9720f08e66508c74a8122003fa9d1eba003e61794e08c5304299879f4d426dee', '[\"*\"]', '2025-08-10 17:15:34', '2025-08-10 17:15:20', '2025-08-10 17:15:34'),
(2, 'App\\Models\\User', 2, 'auth_token', '1cebe6f713c9612b2f8ffcf76413695c1b431f84694c3c5ea4e29b03d9c1f9ee', '[\"*\"]', NULL, '2025-08-10 17:15:23', '2025-08-10 17:15:23'),
(3, 'App\\Models\\User', 8, 'auth_token', '5e6bfa766939af3cb07c6cc9dd7ecd235a7a03c4e440cf29cb92ba409fd0044e', '[\"*\"]', NULL, '2025-08-10 17:15:27', '2025-08-10 17:15:27'),
(4, 'App\\Models\\User', 5, 'auth_token', 'a784528fe07fa485fd52118d860c8c116688d296fe2311019d93af3d3133ec43', '[\"*\"]', NULL, '2025-08-10 17:15:30', '2025-08-10 17:15:30'),
(5, 'App\\Models\\User', 1, 'auth_token', 'daaa8e112edcb842d02c356b77a9b46d47f7810025e656de104a50e2f13bc08f', '[\"*\"]', '2025-08-10 17:18:19', '2025-08-10 17:17:20', '2025-08-10 17:18:19'),
(11, 'App\\Models\\User', 1, 'auth_token', 'd2b44fcf943ea3b639e1d2723317aec6996dd378fb0a02b98b96cbaca793220b', '[\"*\"]', '2025-08-11 19:38:25', '2025-08-10 19:11:13', '2025-08-11 19:38:25'),
(57, 'App\\Models\\User', 1, 'auth_token', '6e48a82c9df253fe4d3f6172c21d5494a3fd1c1cbc4aafadf1aed4279ccaf0a3', '[\"*\"]', '2025-08-13 09:41:14', '2025-08-13 09:41:06', '2025-08-13 09:41:14'),
(124, 'App\\Models\\User', 1, 'auth_token', 'a6b8f64818f3a75df7559a6ed7e259b7bd693172b58652b6db97b7150de7e083', '[\"*\"]', '2025-08-15 13:30:03', '2025-08-15 13:19:29', '2025-08-15 13:30:03'),
(125, 'App\\Models\\User', 1, 'auth_token', 'a6b8f64818f3a75df7559a6ed7e259b7bd693172b58652b6db97b7150de7e083', '[\"*\"]', '2025-08-15 13:30:03', '2025-08-15 13:19:29', '2025-08-15 13:30:03');

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
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `classe_id`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Principal', 'admin@csndr.test', '$2y$10$nGX3hcwn3cniwHDNgB/fsuldrpkIiT5I7OLkrHqWvfgx5NTshs6PK', 'admin', NULL, NULL, '2025-08-10 17:14:55', '2025-08-10 17:14:55'),
(40, 'sadio', 'facho', 'facho@csndr.test', '$2y$10$zjLZ6/2ONxLupK6ElY9Ht.Iv8g61bzHVEiAWp/tnDzKaxT6ler8tO', 'eleve', 1, 37, '2025-08-15 13:23:17', '2025-08-15 13:23:17'),
(38, 'ér', '\"r\"', 'e@csndr.test', '$2y$10$4IagGZMPu9B6RDUI9c3aqe307qjg4T3NFeb3H.Ilw5Pc.6u/sClq.', 'eleve', 1, 36, '2025-08-15 13:22:11', '2025-08-15 13:22:11'),
(39, 'RE', 'RR4', 'RE@csndr.test', '$2y$10$csYLKoVkBkRI2enCJNFVbeIPeUaB1TrEKD6JfL.A3zAM2RhiQ75Q2', 'eleve', 1, 36, '2025-08-15 13:22:26', '2025-08-15 13:22:26'),
(36, 'MAK', 'Boss', 'boss@csndr.test', '$2y$10$.PVh1m5y2xR3NQGjghnVp.M5zvz6B/iPWWj0TYzZwfPaevmZQf/He', 'parent', NULL, NULL, '2025-08-15 13:21:34', '2025-08-15 13:21:34'),
(37, 'autre', 'jard', 'jard@csndr.test', '$2y$10$fEAGEo9ozASpnF6DuZS8QuXltyA2tbzD/uiUqHIsq9zyCoiZgcd/S', 'parent', NULL, NULL, '2025-08-15 13:21:51', '2025-08-15 13:21:51');
-- Réactiver les vérifications de clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
