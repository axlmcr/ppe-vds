-- Définit le moteur de stockage par défaut comme InnoDB pour garantir la prise en charge des transactions et des clés étrangères.
SET default_storage_engine = InnoDb;

-- Active les vérifications des clés étrangères pour garantir l'intégrité référentielle.
SET foreign_key_checks = 1;

-- Sélectionne la base de données `ppe` pour exécuter les opérations suivantes.
USE ppe;

-- Supprime la table `Document` si elle existe déjà pour éviter les conflits lors de la création.
DROP TABLE IF EXISTS Document;

-- Crée la table `Document` pour stocker les informations des documents.
CREATE TABLE Document
(
    id      INT PRIMARY KEY AUTO_INCREMENT                NOT NULL, -- Identifiant unique du document, généré automatiquement.
    titre   VARCHAR(255)                                   NOT NULL, -- Titre du document (255 caractères maximum).
    type    ENUM ('Club', '4 saisons', 'Membre', 'Public') NOT NULL, -- Type de document, limité aux valeurs spécifiées.
    fichier VARCHAR(255)                                   NOT NULL  -- Nom du fichier associé au document (255 caractères maximum).
);

-- Insère des données initiales dans la table `Document` pour pré-remplir avec des exemples.
INSERT INTO Document (titre, type, fichier)
VALUES
    ('Autorisation parentale 4 saisons', '4 saisons', 'Autorisation parentale 4 saisons.pdf'),
    ('Autorisation parentale pour l''adhésion', 'Club', 'Autorisation parentale pour adhesion.pdf'),
    ('Les minimas pour les championnats de France', 'Public', 'Les minimas pour les championnats de France.pdf'),
    ('Parcours du 10 Km', '4 saisons', 'Parcours du 10 Km.pdf'),
    ('Parcours du 5 Km', '4 saisons', 'Parcours du 5 Km.pdf'),
    ('Règlement des 4 saisons', '4 saisons', 'Reglement des 4 saisons.pdf'),
    ('Règlement intérieur', 'Club', 'Reglement interieur.pdf'),
    ('Statuts VDS adoptés en AG du 19/11/2021', 'Club', 'STATUTS VDS.pdf'),
    ('Tableau des allures pour les séances de VS', 'Membre', 'Tableau des allures pour séances de VS.pdf'),
    ('Tableau des allures pour les sorties longues', 'Membre', 'Tableau des allures pour les sorties longues.pdf'),
    ('Tableau pour séance VMA', 'Membre', 'Tableau pour seance VMA.pdf');
