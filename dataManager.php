<?php
/**
 * Gestionnaire de données JSON
 * Parc Informatique DITIB France
 *
 * SÉCURITÉ : auth.php est chargé ici pour que TOUTES les pages
 * utilisant les données vérifient la connexion AVANT de traiter
 * les formulaires (POST). Sans ça, un visiteur non connecté
 * pouvait ajouter/modifier/supprimer des données.
 */

require_once __DIR__ . '/auth.php';

class DataManager {
    private $dataDir = 'data/';

    /**
     * Lire les données depuis un fichier JSON
     */
    public function read($filename) {
        $filepath = $this->dataDir . basename($filename) . '.json';

        if (!file_exists($filepath)) {
            return [];
        }

        $json = file_get_contents($filepath);
        return json_decode($json, true) ?? [];
    }

    /**
     * Écrire les données dans un fichier JSON (avec verrou anti-corruption)
     */
    public function write($filename, $data) {
        $filepath = $this->dataDir . basename($filename) . '.json';

        // Créer le dossier si nécessaire
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0750, true);
        }

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filepath, $json, LOCK_EX);
    }

    /**
     * Ajouter un élément
     */
    public function add($filename, $item) {
        $data = $this->read($filename);

        // Générer un ID unique
        $maxId = 0;
        foreach ($data as $d) {
            if (isset($d['id']) && $d['id'] > $maxId) {
                $maxId = $d['id'];
            }
        }

        $item['id'] = $maxId + 1;
        $item['date_creation'] = date('Y-m-d H:i:s');

        $data[] = $item;
        $this->write($filename, $data);

        return $item['id'];
    }

    /**
     * Obtenir un élément par ID
     */
    public function getById($filename, $id) {
        $data = $this->read($filename);

        foreach ($data as $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Mettre à jour un élément
     */
    public function update($filename, $id, $newData) {
        $data = $this->read($filename);

        foreach ($data as $key => $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                $data[$key] = array_merge($item, $newData);
                $data[$key]['date_modification'] = date('Y-m-d H:i:s');
                $this->write($filename, $data);
                return true;
            }
        }

        return false;
    }

    /**
     * Supprimer un élément
     */
    public function delete($filename, $id) {
        $data = $this->read($filename);

        foreach ($data as $key => $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                unset($data[$key]);
                $data = array_values($data); // Réindexer
                $this->write($filename, $data);
                return true;
            }
        }

        return false;
    }

    /**
     * Compter les éléments
     */
    public function count($filename) {
        $data = $this->read($filename);
        return count($data);
    }

    /**
     * Filtrer les données
     */
    public function filter($filename, $callback) {
        $data = $this->read($filename);
        return array_filter($data, $callback);
    }
}

// Instance globale
$dataManager = new DataManager();