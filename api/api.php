<?php

use Illuminate\Http\Request;
use App\Models\Presupuesto\FuenteFinanciacion;
use Edujugon\PushNotification\PushNotification;
use Illuminate\Support\ServiceProvider;

/*
|--------------------------------------------------------------------------
| Rutas de la API
|--------------------------------------------------------------------------
| Rutas generales de la API. Las otras rutas de la api estan
| definidas en la carpeta routes/api/
*/
Route::middleware(['auth:api'])->group(function () {
	/**
	 * Rutas Payud
	 */
	Route::get('/v1/payment/{order_detail}', 'Payu\PayuController@show');
});

Route::post('oauth/token','\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');


Route::post('/v1/reportes/cartera_vehiculos','Aplicacion\Finanzas\Reportes\ReportesController@reporteCarteraVehiculo');

//Rutra enviar Correo Notificaciones
Route::post('/v1/enviarCorreoAlertas','Aplicacion\Finanzas\Alertas\AlertasController@enviarEmailAlertas');

Route::post('/v1/reportes/cartera_vehiculos','Aplicacion\Finanzas\Reportes\ReportesController@reporteCarteraVehiculo');
Route::post('/v1/reportes/contabilidad_alcaldia','Aplicacion\Finanzas\Reportes\ReportesController@reporteContabilidadIca');
Route::post('/v1/reportes/contabilidad/contribuyente/alcaldia','Aplicacion\Finanzas\Reportes\ReportesController@reporteContribuyenteContabilidadCompleto');




Route::post('/v1/test','Aplicacion\Finanzas\Reportes\ReportesController@test');


//Route::post('/v1/reportes/prueba','Aplicacion\Finanzas\Reportes\ReportesController@prueba');


Route::get('v1/presupuesto/plan/rubros','Aplicacion\Finanzas\Presupuesto\PresupuestoController@selectRubro');


Route::post('v1/backup/yopal/apps','Aplicacion\Backups\BackupController@AllAppsYopal');
Route::post('v1/backup/yopal/omisos','Aplicacion\Backups\BackupController@AllBackupOmisos');

//Configuracion Permisos Usuarios
Route::post('v1/usuarios/configuracion/roles','User\ConfiguracionController@permisosRoles');
//Route::post('v1/usuarios/configuracion/permisos','User\ConfiguracionController@permisosAdd');
//
//
/*
|
|RUTAS PARA CONTABILIDAD - FACTURACIÓN
|
 */
Route::post('v1/importar', 'ImportarExtractoController@guardarExtracto');

/***
 * Rutas de los reportes de acuerdos de pago
 * ica y predial.
 */
Route::get('/v1/ica/reporteAcuerdoPago','Ica\ReporteAcuerdoPagoIcaController@acuerdoPagoIca');
Route::get('/v1/predial/reporteAcuerdoPago','Predial\ReporteAcuerdoPagoPredialController@acuerdoPagoPredial');

/*
 * rutas para la el modulo finanzas
 * */

Route::get('/v1/Predial','moduloFinanzas\PredioController@listPredios');
Route::get('/v1/buscarPredial', 'moduloFinanzas\PredioController@buscarPredios');
Route::get('/v1/PredialDetalle','moduloFinanzas\PredioController@getPredial');
Route::get('/v1/getMatricula', 'moduloFinanzas\PredioController@getMatricula');
Route::get('/v1/terceroPredio', 'moduloFinanzas\PredioController@terceroPredio');
Route::get('/v1/listterceroPredio', 'moduloFinanzas\PredioController@listTerceroPredio');
Route::get('/v1/liketerceroPredio', 'moduloFinanzas\PredioController@likeTerceroPredio');
Route::get('/v1/predialposeedor', 'moduloFinanzas\PredioController@poseedor');
Route::get('/v1/listpredialposeedor', 'moduloFinanzas\PredioController@listPoseedor');
Route::get('/v1/facturasPredio', 'moduloFinanzas\PredioController@facturasPredio');
Route::get('/v1/facturasPredioLike', 'moduloFinanzas\PredioController@facturasPredioLike');
Route::get('/v1/vigenciasFactura', 'moduloFinanzas\PredioController@vigenciasFactura');
Route::get('/v1/vigenciasPredio', 'moduloFinanzas\PredioController@vigenciasPredio');
Route::get('/v1/valorFactura', 'moduloFinanzas\PredioController@valoresVigenciasFactura');
Route::get('/v1/valorVigencias', 'moduloFinanzas\PredioController@valorVigenciasPredio');
Route::post('/v1/insertPrediaAcuerdo', 'moduloFinanzas\PredioController@insertPrediaAcuerdo');
Route::get('/v1/getListAcuerdos', 'moduloFinanzas\PredioController@getListAcuerdos');