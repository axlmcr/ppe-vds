"use strict";

// -----------------------------------------------------------------------------------
// Import des fonctions nécessaires
// -----------------------------------------------------------------------------------

import {appelAjax} from "/composant/fonction/ajax.js"; // Fonction pour effectuer des requêtes AJAX
import {afficherToast} from '/composant/fonction/afficher.js'; // Fonction pour afficher des notifications/toasts

// -----------------------------------------------------------------------------------
// Déclaration des variables globales
// -----------------------------------------------------------------------------------

/* global lesDocuments */ // Variable globale contenant les informations des documents

// -----------------------------------------------------------------------------------
// Fonctions de traitement
// -----------------------------------------------------------------------------------

/**
 * Supprime un document en fonction de son ID.
 * @param {number} id - Identifiant unique du document à supprimer.
 */
function supprimer(id) {
    appelAjax({
        url: 'ajax/supprimer.php', // URL de l'API pour la suppression
        data: { id: id }, // Données envoyées à l'API
        success: () => document.getElementById(id.toString())?.remove() // Supprime l'élément du DOM en cas de succès
    });
}

/**
 * Met à jour un document en fonction de son ID.
 * @param {number} id - Identifiant unique du document à mettre à jour.
 */
function maj(id) {
    const fd = new FormData(); // Création d'un objet FormData pour envoyer les données
    fd.append('id', id); // Ajout de l'ID au FormData

    // Si un champ fichier est lié à cet ID, décommentez et ajustez :
    // const input = document.querySelector(`#file-input-${id}`);
    // if (input && input.files[0]) fd.append('file', input.files[0]);

    appelAjax({
        url: 'ajax/maj.php', // URL de l'API pour la mise à jour
        method: 'POST', // Méthode HTTP utilisée
        data: fd, // Données envoyées
        processData: false, // Indique que les données ne doivent pas être transformées
        contentType: false, // Indique que le type de contenu est géré automatiquement
        success: () => afficherToast("Opération réalisée avec succès"), // Affiche un message en cas de succès
        error: (err) => { console.error(err); afficherToast("Erreur lors de la mise à jour"); } // Gère les erreurs
    });
}

// -----------------------------------------------------------------------------------
// Programme principal
// -----------------------------------------------------------------------------------

// Affiche le tableau des documents
for (const element of lesDocuments) {

    let tr = lesLignes.insertRow(); // Ajoute une nouvelle ligne au tableau
    tr.style.verticalAlign = 'middle'; // Aligne verticalement les cellules

    // Cellule contenant les actions "voir", "modifier" et "supprimer"
    let td = tr.insertCell();

    if (element.present) {
        // Lien pour afficher le document
        let view = document.createElement('a');
        view.href = "afficher.php?id=" + element.id; // URL pour afficher le document
        view.target = 'pdf'; // Ouvre dans une nouvelle fenêtre
        view.innerText = '📄'; // Icône pour "voir"
        view.className = 'doc-link'; // Classe CSS pour le style
        td.appendChild(view);
    } else {
        // Indicateur de document manquant
        let missing = document.createElement('span');
        missing.innerText = '❓'; // Icône pour "manquant"
        missing.href = "afficher.php?id=" + element.id;
        missing.className = 'doc-missing'; // Classe CSS pour le style
        td.appendChild(missing);
        console.log("Le document " + element.id + " n'a pas été trouvé"); // Log dans la console
    }

    // Séparateur puis lien pour modifier
    let sep = document.createElement('span');
    sep.innerText = ' '; // Espace entre les actions
    td.appendChild(sep);

    let modif = document.createElement('a');
    modif.target = 'pdf'; // Ouvre dans une nouvelle fenêtre
    modif.innerText = '✏️'; // Icône pour "modifier"
    modif.className = 'modif-link'; // Classe CSS pour le style
    modif.style.cursor = 'pointer'; // Change le curseur au survol
    modif.addEventListener('click', function () { maj(element.id); }); // Ajoute un événement pour la mise à jour
    td.appendChild(modif);

    // Séparateur puis lien pour supprimer
    let sep2 = document.createElement('span');
    sep2.innerText = ' '; // Espace entre les actions
    td.appendChild(sep2);

    let sup = document.createElement('a');
    sup.target = '_self'; // Ouvre dans la même fenêtre
    sup.innerText = '❌'; // Icône pour "supprimer"
    sup.className = 'sup-link'; // Classe CSS pour le style
    sup.style.cursor = 'pointer'; // Change le curseur au survol
    sup.addEventListener('click', function() {
        if (confirm("Confirmer la suppression ?")) supprimer(element.id); // Confirmation avant suppression
    });
    td.appendChild(sup);

    // Colonne : le titre du document
    tr.insertCell().innerText = element.titre;
    // Colonne : le type de document
    tr.insertCell().innerText = element.type;
    // Colonne : le fichier associé
    tr.insertCell().innerText = element.fichier;
}