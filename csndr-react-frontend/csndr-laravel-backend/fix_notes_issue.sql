-- Script SQL pour créer la table notes et insérer des données de test

-- Utiliser la base de données
USE csndr_db;

-- Supprimer la table si elle existe
DROP TABLE IF EXISTS notes;

-- Créer la table notes
CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note DECIMAL(4,2) NOT NULL,
    matiere VARCHAR(100) NOT NULL,
    commentaire TEXT,
    eleve_id INT NOT NULL,
    professeur_id INT NOT NULL,
    coefficient DECIMAL(3,2) DEFAULT 1.0,
    date DATE,
    type_evaluation VARCHAR(100) DEFAULT 'Contrôle',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insérer des données de test
INSERT INTO notes (note, matiere, commentaire, eleve_id, professeur_id, coefficient, date, type_evaluation) VALUES
(15.5, 'Mathématiques', 'Très bon travail sur les équations', 1, 2, 2.0, CURDATE(), 'Contrôle'),
(12.0, 'Français', 'Peut mieux faire en orthographe', 1, 2, 1.5, CURDATE(), 'Devoir'),
(18.0, 'Sciences', 'Excellent travail !', 1, 2, 1.0, CURDATE(), 'Exposé'),
(14.0, 'Histoire', 'Bonne connaissance du sujet', 1, 2, 1.0, CURDATE(), 'Contrôle'),
(16.5, 'Mathématiques', 'Progrès remarquables', 1, 2, 2.0, CURDATE(), 'Devoir');

-- Vérifier les données
SELECT COUNT(*) as 'Nombre de notes' FROM notes;
SELECT * FROM notes;
