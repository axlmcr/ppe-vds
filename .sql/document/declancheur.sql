USE ppe;

-- Supprime le déclencheur `avant_insert_document` s'il existe déjà.
DROP TRIGGER IF EXISTS avant_insert_document;

-- Supprime le déclencheur `avant_update_document` s'il existe déjà.
DROP TRIGGER IF EXISTS avant_update_document;

-- Définit un délimiteur personnalisé pour encapsuler le code des déclencheurs.
DELIMITER //

-- Crée un déclencheur `avant_insert_document` qui s'exécute avant chaque insertion dans la table `document`.
CREATE TRIGGER avant_insert_document
    BEFORE INSERT
    ON document
    FOR EACH ROW
BEGIN
    DECLARE v_max_id BIGINT; -- Déclare une variable pour stocker l'ID maximum existant.

    -- Vérifie si l'ID est NULL ou déjà existant. Si c'est le cas, attribue à l'ID la valeur max(id) + 1.
    IF NEW.id IS NULL OR EXISTS (SELECT 1 FROM document WHERE id = NEW.id) THEN
        SELECT IFNULL(MAX(id), 0) INTO v_max_id FROM document; -- Récupère le maximum des IDs existants.
        SET NEW.id = v_max_id + 1; -- Définit l'ID du nouvel enregistrement.
    END IF;
END//
DELIMITER ;

-- Définit un délimiteur personnalisé pour encapsuler le code des déclencheurs.
DELIMITER //

-- Crée un déclencheur `avant_update_document` qui s'exécute avant chaque mise à jour dans la table `document`.
CREATE TRIGGER avant_update_document
    BEFORE UPDATE
    ON document
    FOR EACH ROW
BEGIN
    -- Empêche la modification de la colonne `fichier` lors de la mise à jour.
    IF (NEW.fichier <> OLD.fichier) OR ((NEW.fichier IS NULL) <> (OLD.fichier IS NULL)) THEN
        SET NEW.fichier = OLD.fichier; -- Réinitialise la valeur de `fichier` à l'ancienne valeur.
    END IF;
END//
DELIMITER ;

-- Réinitialise le délimiteur par défaut.
DELIMITER ;