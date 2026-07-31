<?php
require_once ROOT_PATH . '/core/Model.php';

class LocalizacaoModel {
    public static function getPaises() {
        return JsonDatabase::findAll('paises');
    }

    public static function getEstados() {
        return JsonDatabase::findAll('estados');
    }

    public static function getMunicipios($idEstado = null) {
        if ($idEstado) {
            return JsonDatabase::findWhere('municipios', function ($m) use ($idEstado) {
                return (string)$m['id_estado'] === (string)$idEstado;
            });
        }
        return JsonDatabase::findAll('municipios');
    }

    public static function getBairros() {
        return JsonDatabase::findAll('bairros');
    }

    public static function getTiposLogradouro() {
        return JsonDatabase::findAll('tipos_logradouro');
    }

    public static function getLogradouros() {
        return JsonDatabase::findAll('logradouros');
    }
}
