<?php declare(strict_types=1);
// Sin autoload de aplicación, PDO, sesiones ni servicios. Invoca método real con dobles.
namespace Prueba { class InicializadorAlcanzado extends \RuntimeException {} }
namespace gamboamartin\comercial\controllers { class _base_comercial {} }
namespace gamboamartin\direccion_postal\controllers {
 class _init_dps {
  public function init_js(object $controler): never {
   if (!($controler instanceof \gamboamartin\comercial\controllers\controlador_com_sucursal)) throw new \LogicException('Controlador incorrecto');
   throw new \Prueba\InicializadorAlcanzado('Inicializador correcto, sin continuar hacia datos');
  }
 }
}
namespace {
 require __DIR__.'/../../controllers/controlador_com_sucursal.php';
 $c=(new ReflectionClass(\gamboamartin\comercial\controllers\controlador_com_sucursal::class))->newInstanceWithoutConstructor();
 $baseline=in_array('--caracterizar-original',$argv,true);
 try { $c->modifica(false); throw new RuntimeException('No se interceptó inicialización'); }
 catch (\Prueba\InicializadorAlcanzado $e) { if ($baseline) throw new RuntimeException('Original no coincide'); echo "PASS: modifica resuelve inicializador correcto sin DB\n"; }
 catch (\Error $e) { if (!$baseline || $e->getMessage() !== 'Class "controllers\\_init_dps" not found') throw $e; echo "PASS: fallo original caracterizado exactamente\n"; }
}
