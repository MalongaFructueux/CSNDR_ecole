-- Créer la table notes si elle n'existe pas
DROP TABLE IF EXISTS notes;

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    note DECIMAL(4,2) NOT NULL,
    matiere VARCHAR(100) NOT NULL,
    eleve_id INT NOT NULL,
    professeur_id INT NOT NULL,
    commentaire TEXT,
    type_evaluation VARCHAR(100) DEFAULT 'Contrôle',
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_eleve (eleve_id),
    INDEX idx_professeur (professeur_id),
    INDEX idx_matiere (matiere),
    INDEX idx_date (date)
);

-- Insérer quelques données de test
INSERT INTO notes (note, matiere, eleve_id, professeur_id, commentaire, type_evaluation, date) VALUES
(15.5, 'Mathématiques', 1, 2, 'Bon travail', 'Contrôle', '2024-01-15'),
(12.0, 'Français', 1, 3, 'Peut mieux faire', 'Devoir', '2024-01-16'),
(18.0, 'Histoire', 1, 2, 'Excellent', 'Exposé', '2024-01-17'),
(14.5, 'Mathématiques', 2, 2, 'Correct', 'Contrôle', '2024-01-15'),
(16.0, 'Sciences', 2, 3, 'Très bien', 'TP', '2024-01-18');

SELECT 'Table notes créée avec succès!' as message;
SELECT COUNT(*) as nombre_notes FROM notes;
