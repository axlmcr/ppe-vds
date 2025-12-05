<?php
/**
 * Script de mise à jour d'un fichier existant via une requête AJAX.
 * Ce script vérifie les paramètres reçus, valide le fichier téléversé,
 * et remplace le fichier existant si toutes les conditions sont remplies.
 */

require $_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php';

// Vérification de l'existence des paramètres attendus dans la requête
if (!isset($_FILES['fichier'], $_POST['nomFichier'])) {
    // Envoie une réponse d'erreur si les paramètres sont manquants
    Erreur::envoyerReponse('Paramètre manquant', 'global');
}

// Récupération des paramètres de configuration pour les fichiers PDF
$lesParametres = Document::getConfig();

// Instanciation et paramétrage d'un objet InputFile pour gérer le fichier téléversé
$file = new InputFile($_FILES['fichier'], $lesParametres);

// Forcer le nom du fichier à celui déjà existant (remplacement)
$file->Value = $_POST['nomFichier'];

// Passer en mode 'update' pour autoriser le remplacement du fichier existant
$file->Mode = 'update';

// Vérification de la validité du fichier téléversé
if ($file->checkValidity()) {
    // Si le fichier est valide, effectuer la copie pour remplacer l'ancien fichier
    $file->copy();
    // Retourner une réponse JSON indiquant le succès de l'opération
    echo json_encode(['success' => 'Le fichier a été remplacé']);
} else {
    // Si le fichier n'est pas valide, retourner une réponse JSON avec le message d'erreur
    echo json_encode(['error' => $file->getValidationMessage()]);
}