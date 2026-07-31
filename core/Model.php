<?php
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/core/Audit.php';

abstract class Model {
    protected static $table = '';

    public static function getAll($includeInactive = false) {
        return JsonDatabase::findAll(static::$table, $includeInactive);
    }

    public static function getById($id) {
        return JsonDatabase::findById(static::$table, $id);
    }

    public static function where(callable $filterCallback, $includeInactive = false) {
        return JsonDatabase::findWhere(static::$table, $filterCallback, $includeInactive);
    }

    public static function create(array $data) {
        $result = JsonDatabase::insert(static::$table, $data);
        if ($result) {
            Audit::log('Inclusão', static::$table, null, $result);
        }
        return $result;
    }

    public static function update($id, array $data) {
        $result = JsonDatabase::update(static::$table, $id, $data);
        if ($result) {
            Audit::log('Alteração', static::$table, $result['previous'], $result['current']);
            return $result['current'];
        }
        return false;
    }

    public static function delete($id) {
        // Exclusão Lógica: ativo = false
        $previous = static::getById($id);
        $result = JsonDatabase::softDelete(static::$table, $id);
        if ($result) {
            Audit::log('Exclusão Lógica', static::$table, $previous, ['id' => $id, 'ativo' => false]);
            return true;
        }
        return false;
    }
}
