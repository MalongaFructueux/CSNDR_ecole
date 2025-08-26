-- CSNDR — Schéma de base de données (MySQL 8+, InnoDB)
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- Drop tables (ordre inverse) — à utiliser avec prudence en dev uniquement
-- DROP TABLE IF EXISTS notes, remises, devoirs, classes_eleves, classes, eleves_parents, messages, menus, evenements, personal_access_tokens, users;

-- Table users
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(191) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','enseignant','parent','eleve') NOT NULL DEFAULT 'eleve',
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Liaison parent↔enfant
CREATE TABLE IF NOT EXISTS eleves_parents (
  parent_id BIGINT UNSIGNED NOT NULL,
  eleve_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  PRIMARY KEY (parent_id, eleve_id),
  CONSTRAINT fk_ep_parent FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_ep_eleve FOREIGN KEY (eleve_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Classes
CREATE TABLE IF NOT EXISTS classes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(191) NOT NULL,
  niveau VARCHAR(50) NOT NULL,
  annee_scolaire VARCHAR(20) NOT NULL,
  professeur_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_classes_prof FOREIGN KEY (professeur_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_classes_professeur ON classes(professeur_id);

-- Affectations élèves↔classes
CREATE TABLE IF NOT EXISTS classes_eleves (
  classe_id BIGINT UNSIGNED NOT NULL,
  eleve_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  PRIMARY KEY (classe_id, eleve_id),
  CONSTRAINT fk_ce_classe FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
  CONSTRAINT fk_ce_eleve FOREIGN KEY (eleve_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_ce_eleve ON classes_eleves(eleve_id);

-- Devoirs
CREATE TABLE IF NOT EXISTS devoirs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  classe_id BIGINT UNSIGNED NOT NULL,
  titre VARCHAR(191) NOT NULL,
  description TEXT NULL,
  date_rendu DATE NOT NULL,
  fichier_path VARCHAR(255) NULL,
  created_by BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_dev_classe FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE CASCADE,
  CONSTRAINT fk_dev_creator FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_devoirs_classe ON devoirs(classe_id);

-- Remises de devoirs (uploads des élèves)
CREATE TABLE IF NOT EXISTS remises (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  devoir_id BIGINT UNSIGNED NOT NULL,
  eleve_id BIGINT UNSIGNED NOT NULL,
  fichier_path VARCHAR(255) NOT NULL,
  commentaire VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_rem_devoir FOREIGN KEY (devoir_id) REFERENCES devoirs(id) ON DELETE CASCADE,
  CONSTRAINT fk_rem_eleve FOREIGN KEY (eleve_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uq_rem_devoir_eleve (devoir_id, eleve_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_rem_eleve ON remises(eleve_id);

-- Notes
CREATE TABLE IF NOT EXISTS notes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  eleve_id BIGINT UNSIGNED NOT NULL,
  devoir_id BIGINT UNSIGNED NULL,
  matiere VARCHAR(100) NOT NULL,
  note DECIMAL(5,2) NOT NULL,
  coef DECIMAL(4,2) NOT NULL DEFAULT 1.00,
  commentaire VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_notes_eleve FOREIGN KEY (eleve_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_notes_devoir FOREIGN KEY (devoir_id) REFERENCES devoirs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_notes_eleve ON notes(eleve_id);
CREATE INDEX idx_notes_devoir ON notes(devoir_id);

-- Évènements
CREATE TABLE IF NOT EXISTS evenements (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titre VARCHAR(191) NOT NULL,
  description TEXT NULL,
  date DATE NOT NULL,
  visible_pour ENUM('all','parents','eleves','enseignants') NOT NULL DEFAULT 'all',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_evenements_date ON evenements(date);

-- Messages (simples + fils)
CREATE TABLE IF NOT EXISTS messages (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sujet VARCHAR(191) NOT NULL,
  contenu TEXT NOT NULL,
  from_user_id BIGINT UNSIGNED NOT NULL,
  to_user_id BIGINT UNSIGNED NOT NULL,
  parent_id BIGINT UNSIGNED NULL,
  lu TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_msg_from FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_msg_to FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_msg_parent FOREIGN KEY (parent_id) REFERENCES messages(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE INDEX idx_messages_to_lu ON messages(to_user_id, lu);

-- Menus cantine
CREATE TABLE IF NOT EXISTS menus (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  date DATE NOT NULL,
  entree VARCHAR(191) NULL,
  plat VARCHAR(191) NULL,
  dessert VARCHAR(191) NULL,
  allergenes JSON NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE UNIQUE INDEX uq_menus_date ON menus(date);

-- Sanctum tokens (simplifié)
CREATE TABLE IF NOT EXISTS personal_access_tokens (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tokenable_type VARCHAR(191) NOT NULL,
  tokenable_id BIGINT UNSIGNED NOT NULL,
  name VARCHAR(191) NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  abilities TEXT NULL,
  last_used_at TIMESTAMP NULL,
  expires_at TIMESTAMP NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS=1;
