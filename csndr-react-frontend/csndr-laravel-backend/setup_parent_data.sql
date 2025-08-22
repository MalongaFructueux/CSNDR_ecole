-- Script SQL pour créer des données de test parent-enfant
-- À exécuter dans phpMyAdmin ou MySQL

-- 1. Trouver l'ID du parent existant
SELECT id, nom, prenom, email FROM users WHERE role = 'parent' LIMIT 1;

-- 2. Mettre à jour quelques élèves pour les lier au parent (remplacez 3 par l'ID du parent trouvé ci-dessus)
UPDATE users 
SET parent_id = (SELECT id FROM users WHERE role = 'parent' LIMIT 1)
WHERE role = 'eleve' 
AND parent_id IS NULL 
LIMIT 2;

-- 3. Vérifier les liens créés
SELECT 
    p.id as parent_id, 
    p.prenom as parent_prenom, 
    p.nom as parent_nom,
    e.id as enfant_id,
    e.prenom as enfant_prenom,
    e.nom as enfant_nom,
    e.classe_id
FROM users p 
JOIN users e ON e.parent_id = p.id 
WHERE p.role = 'parent';

-- 4. Vérifier les devoirs pour les classes des enfants
SELECT 
    h.id,
    h.titre,
    h.description,
    h.classe_id,
    c.nom as classe_nom
FROM homework h
JOIN classes c ON c.id = h.classe_id
WHERE h.classe_id IN (
    SELECT DISTINCT e.classe_id 
    FROM users p 
    JOIN users e ON e.parent_id = p.id 
    WHERE p.role = 'parent' AND e.classe_id IS NOT NULL
);

-- 5. Vérifier les notes pour les enfants
SELECT 
    g.id,
    g.matiere,
    g.note,
    e.prenom as eleve_prenom,
    e.nom as eleve_nom,
    p.prenom as parent_prenom,
    p.nom as parent_nom
FROM notes g
JOIN users e ON e.id = g.eleve_id
JOIN users p ON p.id = e.parent_id
WHERE p.role = 'parent';
