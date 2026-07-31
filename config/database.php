<?php
require_once __DIR__ . '/config.php';

class JsonDatabase {
    private static function getFilePath($table) {
        if (!is_dir(DATA_PATH)) {
            @mkdir(DATA_PATH, 0777, true);
        }
        return DATA_PATH . '/' . $table . '.json';
    }

    public static function read($table) {
        $filePath = self::getFilePath($table);
        if (!file_exists($filePath)) {
            return [];
        }
        
        $fp = fopen($filePath, 'r');
        if (!$fp) {
            return [];
        }
        
        flock($fp, LOCK_SH);
        $content = stream_get_contents($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    public static function write($table, array $data) {
        $filePath = self::getFilePath($table);
        $fp = fopen($filePath, 'c+');
        if (!$fp) {
            logError("Erro ao abrir arquivo para escrita: " . $table);
            return false;
        }
        
        flock($fp, LOCK_EX);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
        
        return true;
    }

    public static function findAll($table, $includeInactive = false) {
        $records = self::read($table);
        if ($includeInactive) {
            return $records;
        }
        return array_values(array_filter($records, function ($item) {
            return isset($item['ativo']) ? (bool)$item['ativo'] : true;
        }));
    }

    public static function findById($table, $id) {
        $records = self::read($table);
        foreach ($records as $item) {
            if (isset($item['id']) && (string)$item['id'] === (string)$id) {
                return $item;
            }
        }
        return null;
    }

    public static function findWhere($table, callable $filterCallback, $includeInactive = false) {
        $records = self::findAll($table, $includeInactive);
        return array_values(array_filter($records, $filterCallback));
    }

    public static function insert($table, array $data) {
        $records = self::read($table);
        
        // Calcular novo ID autoincremento
        $maxId = 0;
        foreach ($records as $r) {
            if (isset($r['id']) && $r['id'] > $maxId) {
                $maxId = (int)$r['id'];
            }
        }
        
        $data['id'] = $maxId + 1;
        if (!isset($data['ativo'])) {
            $data['ativo'] = true;
        }
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        $data['updated_at'] = date('Y-m-d H:i:s');

        $records[] = $data;
        if (self::write($table, $records)) {
            return $data;
        }
        return false;
    }

    public static function update($table, $id, array $newData) {
        $records = self::read($table);
        $updated = false;
        $previousItem = null;

        foreach ($records as $index => $item) {
            if (isset($item['id']) && (string)$item['id'] === (string)$id) {
                $previousItem = $item;
                $newData['id'] = $item['id'];
                $newData['created_at'] = $item['created_at'] ?? date('Y-m-d H:i:s');
                $newData['updated_at'] = date('Y-m-d H:i:s');
                if (!isset($newData['ativo'])) {
                    $newData['ativo'] = $item['ativo'] ?? true;
                }
                
                $records[$index] = array_merge($item, $newData);
                $updated = true;
                break;
            }
        }

        if ($updated && self::write($table, $records)) {
            return [
                'previous' => $previousItem,
                'current' => self::findById($table, $id)
            ];
        }
        return false;
    }

    public static function softDelete($table, $id) {
        $item = self::findById($table, $id);
        if (!$item) {
            return false;
        }
        $result = self::update($table, $id, ['ativo' => false]);
        return $result ? $item : false;
    }
}
