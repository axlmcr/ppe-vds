<?php

    /**
     * Classe Document
     *
     * Gère les enregistrements de documents PDF dans la table `document`, avec les champs `id`, `titre` et `fichier`.
     * - Imposе la présence d’un fichier PDF téléversé à la création d’un document.
     * - Valide le titre via des contraintes (longueur, pattern, nettoyage d’espaces).
     * - Configure les règles pour le fichier (répertoire, format, taille, type MIME, etc.).
     * - Fournit un constructeur pour initialiser la structure des colonnes.
     * - Offre deux méthodes statiques :
     *   • getConfig() : retourne la configuration de téléversement des fichiers PDF.
     *   • getAll() : récupère tous les documents, triés par titre, et ajoute un indicateur `present`
     *              précisant si le fichier PDF existe bien sur le disque.
     */
    class Document extends Table
    {
        /**
         * Configuration des fichiers PDF associés aux enregistrements.
         * - `repertoire` : Chemin du répertoire de stockage.
         * - `extensions` : Extensions autorisées.
         * - `types` : Types MIME acceptés.
         * - `maxSize` : Taille maximale autorisée (en octets).
         * - `require` : Indique si le fichier est obligatoire.
         * - `rename` : Indique si le fichier doit être renommé.
         * - `sansAccent` : Indique si les accents doivent être supprimés.
         * - `accept` : Extensions acceptées pour l'upload.
         * - `label` : Libellé affiché pour l'upload.
         */
        private const CONFIG = [
            'repertoire' => '/data/document',
            'extensions' => ['pdf'],
            'types' => ["application/pdf"],
            'maxSize' => 1024 * 1024,
            'require' => true,
            'rename' => false,
            'sansAccent' => false,
            'accept' => '.pdf',
            'label' => 'Fichier PDF (1 Mo max)'
        ];

        /**
         * Chemin complet du répertoire de stockage des fichiers.
         */
        private const DIR = RACINE . self::CONFIG['repertoire'];

        /**
         * Constructeur de la classe Document.
         * Initialise les colonnes de la table `document` :
         * - `titre` : Champ texte avec validation de pattern, longueur et suppression des espaces superflus.
         * - `fichier` : Champ texte pour le nom du fichier PDF.
         * - `type` : Champ liste avec des valeurs prédéfinies.
         */
        public function __construct()
        {
            // Appel du constructeur de la classe parent.

            // Configuration du champ `titre`.
            $input = new InputText();
            $input->Pattern = "^[a-zA-ZÀ-ÿçÇ0-9]([ '\-]?[a-zA-ZÀ-ÿçÇ0-9]*)*$";
            $input->MinLength = 10;
            $input->MaxLength = 100;
            $input->SupprimerEspaceSuperflu = true;
            $this->columns['titre'] = $input;

            // Configuration du champ `fichier`.
            $input = new InputText();
            $input->Require = false; // Le fichier est obligatoire.
            $this->columns['fichier'] = $input;

            // Configuration du champ `type`.
            $input = new InputList();
            $input->Require = false;
            $input->Values = ['C', 'S', 'M', 'P'];
            $this->columns['type'] = $input;

            // Définition des colonnes pouvant être modifiées unitairement.
            $this->listOfColumns->Values = ['titre'];
        }

        // ------------------------------------------------------------------------------------------------
        // Méthodes concernant les opérations de consultation
        // ------------------------------------------------------------------------------------------------

        /**
         * Renvoie la configuration des fichiers PDF.
         * @return array<string, mixed> Tableau associatif contenant les paramètres de configuration.
         */
        public static function getConfig(): array
        {
            return self::CONFIG;
        }

        /**
         * Retourne tous les enregistrements de la table `document`.
         * Ajoute une colonne `present` pour indiquer si le fichier PDF existe sur le disque.
         * @return array Liste des documents avec leurs informations.
         */
        public static function getAll(): array
        {
            $sql = "Select id, titre,type, fichier  from document order by titre;";
            $select = new Select();
            $lesLignes = $select->getRows($sql);
            // Ajout d'une colonne permettant de vérifier l'existence du fichier.
            foreach ($lesLignes as &$ligne) {
                $chemin = self::DIR . '/' . $ligne['fichier'];
                $ligne['present'] = is_file($chemin) ? 1 : 0;
            }
            return $lesLignes;
        }

        /**
         * Retourne les documents visibles selon le statut de l'utilisateur.
         * - Si connecté : tous les documents.
         * - Si non connecté : exclut les documents réservés aux membres.
         * @return array|false Liste des documents visibles ou `false` en cas d'erreur.
         */
        public static function getVisible(): array|false
        {
            $select = new Select();
            if (isset($_SESSION['membre'])) {
                $sql = "SELECT id, titre, type, fichier FROM document ORDER BY titre;";
            } else {
                $sql = "SELECT id, titre, type, fichier FROM document WHERE type != 'Membre'  ORDER BY titre;";
            }
            $lesLignes = $select->getRows($sql);
            foreach ($lesLignes as &$ligne) {
                $chemin = self::DIR . '/' . $ligne['fichier'];
                $ligne['present'] = is_file($chemin) ? 1 : 0;
            }
            return $lesLignes;
        }

        /**
         * Récupère les informations d’un document par son ID.
         * @param int $id Identifiant du document.
         * @return array{id: int, type: string, fichier: string, titre: string}|null Informations du document ou `null` si introuvable.
         */
        public static function getById(int $id): array|false
        {
            $sql = <<<SQL
             select id, fichier, titre from document where id = :id;
            SQL;
            $select = new Select();
            return $select->getRow($sql, ['id' => $id]);
        }

        // ------------------------------------------------------------------------------------------------

        /**
         * Supprime le fichier PDF associé à un document.
         * @param string $fichier Nom du fichier à supprimer.
         * @return void
         */
        public static function supprimerFichier(string $fichier): void
        {
            $chemin = self::DIR . '/' . $fichier;
            if (is_file($chemin)) {
                unlink($chemin);
            }
        }

        /**
         * Supprime un enregistrement de la table `document`.
         * @param int $id Identifiant du document à supprimer.
         * @return void
         */
        public static function supprimer(int $id): void
        {
            $db = Database::getInstance();
            $sql = "Delete from document  where id = :id;";
            $cmd = $db->prepare($sql);
            $cmd->bindValue('id', $id);
            $cmd->execute();
        }
    }