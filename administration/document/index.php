<?php

require $_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php';

/**
 * Récupère tous les documents disponibles et les encode en JSON.
 * La variable `$lesDocuments` contiendra les données JSON des documents.
 */
$lesDocuments = json_encode(Document::getAll());

/**
 * Génère un script HTML contenant une variable JavaScript `lesDocuments`
 * initialisée avec les données JSON des documents.
 */
$head = <<<HTML
<script >
    const lesDocuments = $lesDocuments;

</script>
HTML;

/**
 * Charge l'interface utilisateur en incluant le fichier `interface.php`.
 */
require RACINE . '/include/interface.php';