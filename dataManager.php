<?php
/**
 * Gestionnaire de données JSON
 * Parc Informatique DITIB France
 */

class DataManager {
    private $dataDir = 'data/';
    
    /**
     * Lire les données depuis un fichier JSON
     */
    public function read($filename) {
        $filepath = $this->dataDir . $filename . '.json';
        
        if (!file_exists($filepath)) {
            return [];
        }
        
        $json = file_get_contents($filepath);
        return json_decode($json, true) ?? [];
    }
    
    /**
     * écrire les données dans un fichier JSON
     */
    public function write($filename, $data) {
        $filepath = $this->dataDir . $filename . '.json';
        
        // CrÃƒÂ©er le dossier si nÃƒÂ©cessaire
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
        
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filepath, $json);
    }
    
    /**
     * Gérer l'upload d'image
     */
    public function handleImageUpload($fileInput) {
        if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $file = $_FILES[$fileInput];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            return null;
        }
        
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newName = uniqid('img_') . '.' . $ext;
        $dest = $uploadDir . $newName;
        
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return $dest;
        }
        
        return null;
    }
    
    /**
     * Ajouter un ÃƒÂ©lÃƒÂ©ment
     */
    public function add($filename, $item) {
        $data = $this->read($filename);
        
        // GÃƒÂ©nÃƒÂ©rer un ID unique
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
     * Obtenir un ÃƒÂ©lÃƒÂ©ment par ID
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
     * Mettre 0 jour un ÃƒÂ©lÃƒÂ©ment
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
     * Supprimer un ÃƒÂ©lÃƒÂ©ment
     */
    public function delete($filename, $id) {
        $data = $this->read($filename);
        
        foreach ($data as $key => $item) {
            if (isset($item['id']) && $item['id'] == $id) {
                unset($data[$key]);
                $data = array_values($data); // RÃƒÂ©indexer
                $this->write($filename, $data);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Compter les ÃƒÂ©lÃƒÂ©ments
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

?>