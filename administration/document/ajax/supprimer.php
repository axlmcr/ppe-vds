<?php
require $_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php';

/**
 * Vérifie l'existence du paramètre 'id' dans la requête POST.
 * Si le paramètre est manquant, une réponse d'erreur est envoyée.
 */
if (!isset($_POST['id'])) {
    Erreur::envoyerReponse("Paramètre manquant", 'global');
}

$id = $_POST['id'];

/**
 * Récupère le document correspondant à l'id fourni.
 * Si le document n'existe pas, une réponse d'erreur est envoyée.
 */
$ligne = Document::getById($id);
if (!$ligne) {
    Erreur::envoyerReponse("Ce document n'existe pas", 'global');
}

/**
 * Supprime l'enregistrement du document dans la base de données.
 */
document::supprimer($id);

/**
 * Supprime le fichier PDF associé au document.
 */
document::supprimerFichier($ligne['fichier']);

/**
 * Renvoie une réponse JSON indiquant le succès de l'opération.
 */
$reponse = ['success' => "Le document a été supprimé"];
echo json_encode($reponse, JSON_UNESCAPED_UNICODE);