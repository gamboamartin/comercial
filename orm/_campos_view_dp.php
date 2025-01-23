<?php
namespace gamboamartin\comercial\models;

use gamboamartin\direccion_postal\models\dp_cp;
use gamboamartin\direccion_postal\models\dp_estado;
use gamboamartin\direccion_postal\models\dp_municipio;
use gamboamartin\direccion_postal\models\dp_pais;
use PDO;

class _campos_view_dp{
    final public function campos_view(array $campos_view, PDO $link): array
    {
        $campos_view['dp_pais_id'] = array('type' => 'selects', 'model' => new dp_pais(link: $link));
        $campos_view['dp_estado_id'] = array('type' => 'selects', 'model' => new dp_estado(link: $link));
        $campos_view['dp_municipio_id'] = array('type' => 'selects', 'model' => new dp_municipio(link: $link));
        $campos_view['dp_cp_id'] = array('type' => 'selects', 'model' => new dp_cp(link: $link));

        return $campos_view;
    }
}
