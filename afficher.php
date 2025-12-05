<?php
require $_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php';

/**
 * Vérifie si le paramètre 'id' est présent dans la requête GET.
 * Si le paramètre est manquant ou vide, une réponse d'erreur est affichée.
 */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    Erreur::afficherReponse("Le document n'est pas précisé", 'global');
}

// Récupération du paramètre 'id' depuis la requête GET.
$id = $_GET['id'];

/**
 * Vérifie la validité du paramètre 'id'.
 * Si le paramètre ne correspond pas à un entier, l'accès est bloqué.
 */
if (!preg_match('/^[0-9]+$/', $id)) {
    Erreur::bloquerVisiteur();
}

// Récupération du document correspondant à l'identifiant fourni.
$document = Document::getById($id);

/**
 * Vérifie si le document existe dans la table 'document'.
 * Si le document n'existe pas, une réponse d'erreur est affichée.
 */
if (!$document) {
    Erreur::afficherReponse("Le document demandé n'existe pas", 'global');
}

// Extraction des informations du document.
$id = $document['id'];
$titre = $document['titre'];
//$type = $document['type'];
$fichier = $document['fichier'];

/**
 * Vérifie si le fichier du document existe dans le répertoire de stockage.
 * Si le fichier est introuvable, une réponse d'erreur est affichée.
 */
$repertoire = Document::getConfig()['repertoire'];
$uri = RACINE . "$repertoire/$fichier";
if (!is_file($uri)) {
    Erreur::afficherReponse("Le document demandé '$titre' n'a pas été trouvé.", 'global');
}

/**
 * Affiche le document PDF dans le navigateur.
 * Définit les en-têtes HTTP nécessaires pour afficher le fichier PDF.
 */
header('Content-Type: application/pdf');
header("Content-Disposition: inline; filename=\"$fichier\"");
header('Content-Length: ' . filesize($uri));
readfile($uri);
exit;