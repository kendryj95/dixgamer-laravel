<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// borrar cache desde el navegador
Route::get('clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Route::get('clear-config', function() {
    Artisan::call('config:clear');
    return "Config is cleared";
});

# Ruta de los procesos automaticos (crontabs)
Route::get('config/proceso/{tipo}', 'ControlsController@procesosAutomaticos');

// Rutas login y logout
Route::get('login', 'Auth\LoginController@index')->name('login');
Route::post('login', 'Auth\LoginController@auth');
Route::get('logout', 'Auth\LoginController@logout');

/*Route::get('logout',function(){
  Auth::logout();
  return redirect('login');
});
*/
//API
Route::get('getDataPaginaAnt','AccountController@previo')->name('getDataPaginaAnt');
Route::get('getDataPaginaSig','AccountController@siguiente')->name('getDataPaginaSig');


// Solo usuarios logueados
Route::group(['middleware' => ['login']], function()
{
  Route::get('/', 'HomeController@index');
  Route::get('home', 'HomeController@index');
  Route::get('asignar_producto', 'HomeController@asignar_producto');
  

  // Rutas para clientes
  Route::resource('clientes', 'CustomerController');
  Route::get('clientes/tipo/{revendedor?}', 'CustomerController@index');
  Route::post('customer_ctrl_email', 'CustomerController@customerCtrlEmail');
  Route::post('customer_ctrl_ml_user', 'CustomerController@customerCtrlMlUsr');
  Route::get('customer_ventas_modificar/{id}/{opt}', 'CustomerController@ventasModificar');
  Route::post('customer_ventas_modificar_store', 'CustomerController@ventasModificarStore');
  Route::get('customer_ventas_modificar_producto/{id}', 'CustomerController@ventasModificarProductos');
  Route::get('customer_ventas_modificar_producto_store/{consola}/{titulo}/{slot}/{id_ventas}', 'CustomerController@ventasModificarProductosStore')->name('producto.modificar');
  Route::get('customer_duplicar_venta/{id}', 'CustomerController@duplicarVenta');
  Route::get('customer_duplicar_venta_store/{consola}/{titulo}/{slot}/{id_ventas}', 'CustomerController@duplicarVentaStore');
  Route::get('customer_ventas_eliminar/{id}', 'CustomerController@ventasEliminar');
  Route::post('customer_ventas_eliminar', 'CustomerController@ventas_delete');
  Route::get('customer_ventas_quitar_producto/{id}', 'CustomerController@ventarQuitarProducto');
  Route::get('customer_confirmUpdateProduct/{consola}/{titulo}/{slot}/{id_ventas}', 'CustomerController@confirmUpdateProduct');
  Route::get('customer_confirmDuplicarVenta/{consola}/{titulo}/{slot}/{id_ventas}', 'CustomerController@confirmDuplicarVenta');
  Route::post('customer_saveML', 'CustomerController@storeML');
  Route::get('customer_ventas_cobro_modificar/{id}', 'CustomerController@ventasCobroModificar');
  Route::post('customer_ventas_cobro_modificar', 'CustomerController@ventasCobroModificarStore');
  Route::get('customer_addVentasCobro/{id_ventas}/{id_cliente}', 'CustomerController@addVentasCobro');
  Route::post('customer_addVentasCobro', 'CustomerController@addVentasCobroStore');
  Route::get('customer_setEmailPrimary/{id}/{id_cliente}', 'CustomerController@setEmailPrimary');
  Route::get('createCustomerWeb/{oii}', 'CustomerController@createCustomerWeb');
  Route::get('enviar_email_venta/{venta_id}/{tipo}/{consola?}/{slot?}/{cuentas_id?}', 'CustomerController@emails');
  Route::get('update_amounts/{cobro}/{cliente_id}', 'CustomerController@updateAmounts');
  Route::get('delete_amounts/{id}', 'CustomerController@deleteAmount');
  Route::get('delete_notes/{id}/{tipo}', 'CustomerController@deleteNotes');
  Route::get('saleToClient/{id_stock}/{consola}/{slot}', 'CustomerController@saleToClient');
  Route::get('marcar_enviado/{id_venta}', 'CustomerController@marcarEnviadoVenta');
  Route::get('cargar_notas_ventas/{id_venta}', 'CustomerController@loadNotesSales');

  //Card functions buttons
  Route::post('getDataName','EditButtonsController@getDataName')->name('getDataName');
    Route::post('saveDataName','EditButtonsController@saveDataName')->name('saveDataName');
    Route::post('saveDataEmail','EditButtonsController@saveDataEmail')->name('saveDataEmail');
    Route::post('saveDataML','EditButtonsController@saveDataML')->name('saveDataML');
    Route::post('saveDataOther','EditButtonsController@saveDataOther')->name('saveDataOther');
    Route::post('saveNotes','EditButtonsController@saveNotes')->name('saveNotes');
    Route::post('saveFB','EditButtonsController@saveFB')->name('saveFB');
    Route::post('locateFB','EditButtonsController@locateFB')->name('locateFB');
  Route::post('updateStatusReseller','CustomerController@updateStatusReseller')->name('updateStatusReseller');

  // Rutas para cuentas

  Route::resource('cuentas', 'AccountController');
  Route::post('notas_predefinidas', 'AccountController@notasPredefinidas');
  Route::post('recuperar_cuenta', 'AccountController@recoverAccount');
  Route::get('cuentas_robadas', 'AccountController@cuentasRobadas');
  Route::get('listaYopmail', 'AccountController@listaYopmail');
  Route::get('change_email_dixgamer/{account_id}', 'AccountController@changeEmailDixgamer');
  Route::get('nota_siguejugando/{account_id}', 'AccountController@sigueJugando');
  Route::get('nota_siguejugandopri/{account_id}', 'AccountController@sigueJugandoPri');
  Route::get('nota_intentorecuperar/{account_id}', 'AccountController@intentoRecuperar');
  Route::get('agregar_20_off/{account}/{title}/{console}', 'AccountController@product20off');
  Route::get('cuentas_con_saldo', 'AccountController@accountAmount');
  Route::get('cuentas_para_ps3', 'AccountController@accountGamePs3');
  Route::get('cuentas_para_ps4', 'AccountController@accountGamePs4');
  Route::post('account_ctrl_column', 'AccountController@accountCtrlColumn');
  Route::get('recharge_account/{id}', 'AccountController@rechargeBalance');
  Route::get('recharge_minim_account/{id}', 'AccountController@rechargeBalanceMinim');
  Route::get('crear_saldo_cuenta/{account_id}/{title}/{console}', 'AccountController@storeBalanceAccount');
  Route::get('crear_saldo_minimo_cuenta/{account_id}/{stock_id}', 'AccountController@storeBalanceMinimAccount');
  Route::get('crear_nota_cuenta/{account_id}', 'AccountController@createNote');
  Route::get('editar_direccion_cuenta/{account_id}', 'AccountController@editAddressAccount');
  Route::post('guardar_nota_cuenta/{account_id}', 'AccountController@storeNote');
  Route::post('actualizar_password_cuenta/{account_id}/{param?}', 'AccountController@updatePassword');
  Route::post('actualizar_direccion_cuenta/{account_id}', 'AccountController@updateAddressAccount');
  Route::post('devolver_saldo_cuentas', 'AccountController@rollbackBalance');
  Route::post('congelar_tc', 'AccountController@congelarTC');
  Route::post('resetear_cuenta/{id}/{recup?}', 'AccountController@resetAccount');
  Route::get('solicitar_reseteo_cuenta/{id}', 'AccountController@requestReset');
  Route::post('solicitar_reseteo_cuenta/{id}', 'AccountController@storeRequestReset');
  Route::get('stock_insertar_cuenta/{id}', 'AccountController@createStockAccount');
  Route::post('stock_insertar_cuenta/{id}', 'AccountController@storeStockAccount');
  Route::get('stock_pre_insertar_cuenta/{id}', 'AccountController@createLastStock');
  Route::post('guardar_stock_masivo/{id}', 'AccountController@storeLastStock');
  Route::get('actualizar_stock_cuenta/{stock_id}/{account_id}/{opt}', 'AccountController@editStockAccount');
  Route::post('actualizar_stock_cuenta/{account_id}', 'AccountController@updateStockAccount');
  Route::post('repetir_ultima_cuenta/{account_id}', 'AccountController@repeatLastAccount');
  Route::get('repetir_gift_juego/{account_id}', 'AccountController@repetirGiftAndJuego');
  Route::get('modify_date_operations/{id}/{tipo}', 'AccountController@modifyDateOperations');
  Route::post('modify_date_operations_store', 'AccountController@modifyDateOperationsStore');
  Route::get('delete_operations/{id}/{tipo}', 'AccountController@deleteOperation');
  Route::get('cuentas_reseteadas', 'AccountController@indexReseteados');
  Route::get('cuentas_notas', 'AccountController@indexCuentasNotas');
  Route::get('modo_continuo', 'AccountController@modoContinuo');
  Route::get('con_tc_sin_juego', 'AccountController@conTCSinJuego');
  Route::post('dominios_cuentas', 'AccountController@dominiosByUser');
  Route::get('cuentas_resetear', 'AccountController@ctasResetear');
  Route::get('cuentas_vacias/{usuario?}', 'AccountController@ctasVacias');
  Route::get('cuentas/fornite/{id_account}', 'AccountController@ctaFornite')->name('cuenta-fornite');
  Route::get('cuentas/sendEmail/{type}/{cliente_id}/{account_id}/{id_venta}/{id_stock}', 'AccountController@emailMsjReactPass')->name('email-info');

  Route::get('balance/{page}', 'ControlsController@balanceProductosDiasCondicionado'); // Acceso solo para BETINA


  // Rutas para stock
  Route::resource('stock', 'StockController');
  Route::get('stock_notas', 'StockController@indexNotes');
  Route::get('falta_cargar', 'StockController@indexFaltaCargar');
  Route::get('catalogo_link_ps_store', 'StockController@indexLinkPsStore');
  Route::get('productos_catalogo', 'StockController@indexCatalogueProduct');
  Route::get('publicaciones_secundarias_ml', 'StockController@publicacionesSecundariasML');
  Route::get('stocks_cargados', 'StockController@indexCargados');
  Route::get('pedidos_cargar/{user?}','StockController@pedidosCargar');
  Route::get('update_product_x/{id_stock}/{accion}', 'StockController@updateTitleProductX');
  Route::get('delete_product/{id_stock}', 'StockController@deleteProduct');
  Route::post('stock/controlar_code', 'StockController@controlarCode');


  Route::get('saldos','BalanceController@listarSaldos');

  // Rutas donde solo podran acceder analistas y administradores
  Route::group(['middleware' => ['analyst']], function()
  {
    // P1
    Route::get('stock_insertar_codigo','StockController@createCode');
    Route::post('stock_insertar_codigo','StockController@storeCode');
    Route::get('stock_insertar_codigo_control','StockController@createCodeControl');
    Route::post('stock_insertar_codigo_control','StockController@storeCodeControl');
    Route::get('validaCodigo','StockController@validaCodigo');
    Route::get('stock_insertar_codigo_poff','StockController@createCodePoff');
    Route::post('stock_insertar_codigo_poff','StockController@storeCodePoff');

  });

    // Rutas donde solo podran acceder administradores
  Route::group(['middleware' => ['administrator']], function()
  {
    // P2
    Route::get('stock_insertar_codigo_g','StockController@createCodeG');
    Route::post('stock_insertar_codigo_g','StockController@storeCodeG');
    Route::get('stock_insertar_codigo_g_vcc','StockController@createCodeGVCC');
    Route::post('stock_insertar_codigo_g_vcc','StockController@storeCodeGVCC');

    Route::post('asignar_stock','StockController@asignarStockStore');
    Route::get('pedidos_carga/admin', 'StockController@pedCargaAdmin');
    Route::get('pedidos_finalizados/admin', 'StockController@pedCargaFinalizados');
    Route::get('confirmar_pedido/{id}', 'StockController@confirmPedCarga');
    Route::get('get_pedidos_edit/{id}', 'StockController@getPedidosEdit');
    Route::get('stock_cm', 'StockController@listCM');
    Route::get('stock_cm/{code}', 'StockController@listCMByCode');
    Route::post('delete_cm', 'StockController@deleteCM');
    Route::get('detail_stock/{id_stock}/{slot}', 'StockController@detailStock');


    // Gastos
    Route::resource('gastos', 'ExpensesController');
  });

    Route::group(['middleware' => ['administrator']], function()
    {
        // P3
        Route::get('stock_insertar_codigo_p3','StockController@createCodep3');
        Route::post('stock_insertar_codigo_p3','StockController@storeCodep3');

        // Gastos
        Route::resource('gastos', 'ExpensesController');
    });

    Route::group(['middleware' => ['administrator']], function()
    {
        Route::get('horarios', 'ScheduleController@indexAdmin');
        Route::get('horarios/{filtro?}', 'ScheduleController@indexAdmin');
        Route::get('horarios/verificar/{id}', 'ScheduleController@verificarHorario');
        Route::get('horarios/edit/{id}', 'ScheduleController@editarHorario')->where(['id' => '[0-9]+']);
        Route::post('horarios/editar', 'ScheduleController@editHorario');

        Route::get('carga_gc/{carga?}', 'ControlsController@cargaGC');
        Route::post('carga_gc_store', 'ControlsController@cargaGC_store');
        Route::get('control_mp/{version2?}', 'ControlsController@controlMP');
        Route::get('control_mp_baja/{nro_mov}', 'ControlsController@controlMPBaja');
        Route::get('control_mp_baja_envio/{dif}/{ref_cobro}', 'ControlsController@controlMPBajaEnvio');
        Route::get('control_mp_crear_venta_cero/{ref_cobro}/{importe}/{c_id}', 'ControlsController@controlMPCrearVentaCero');
        Route::get('control_mp_actualizar_importes/{ref_op}', 'ControlsController@controlMPActualizarImportes');
        Route::get('control_mp_baja_slot_libre/{dif}/{ref_cobro}', 'ControlsController@controlMPBajaSlotLibre');
        Route::get('config/general', 'ControlsController@configGeneral');
        Route::post('config/general', 'ControlsController@configGeneralStore');
        Route::get('excel', 'ControlsController@excel');
        Route::get('control_ventas', 'ControlsController@controlVentas');
        Route::get('balance', 'ControlsController@balance');
        Route::get('balance_productos', 'ControlsController@balanceProductos');
        Route::get('balance_productos_dias', 'ControlsController@balanceProductosDias');
    });



  // Pueden acceder todos menos administrador
  Route::group(['middleware' => ['less.admin']], function()
  {

    ### COMENTADO POR KENDRY POR PETICION DE VICTOR 10/06/20
    //Route::resource('horario','ScheduleController');



  });

  Route::get('sales/list','SalesController@index')->name('sales/list');
  Route::post('sales/list','SalesController@index')->name('sales/list');
  Route::get('sales/lista_sin_entregar','SalesController@sinEntregar')->name('sales/lista_sin_entregar');
  Route::get('sales/lista_cobro','SalesController@listaCobros')->name('sales/listaCobros');
  Route::post('sales/lista_cobro','SalesController@listaCobros')->name('sales/listaCobros');
  Route::get('sales/manual/add/{consola}/{titulo}/{slot}','SalesController@addManualSale')->name('sales/manual/add/{consola}/{titulo}/{slot}');
  Route::post('saveManualSale','SalesController@saveManualSale')->name('saveManualSale');
  //Route::get('web/sales','Pedidos_CobradosController@index')->name('web/sales');
    // Route::get('web/sales','Pedidos_CobradosController@test')->name('web/sales');
    Route::get('web/sales/{filtro?}','Pedidos_CobradosController@index')->name('web/sales');
    Route::get('web/sales_nofifa/{filtro?}','Pedidos_CobradosController@salesNoFifa')->name('web/sales_nofifa');
    Route::get('web/sales_fifa/{filtro?}','Pedidos_CobradosController@salesFifa')->name('web/sales_fifa');
    //Route::get('/web/sales', function() { return redirect('web/sales/list'); });
    Route::post('getDataClientWebSales','Pedidos_CobradosController@getDataClientWebSales')->name('getDataClientWebSales');
    Route::get('salesInsertWeb/{oii}/{titulo}/{consola}/{slot?}','SalesController@salesInsertWeb');
    Route::get('sales/{id_sale}/cliente','SalesController@salesClient');
    Route::get('sales/recupero','SalesController@salesListRecupero');
    Route::get('sales/list/notas','SalesController@indexNotes')->name('sales-notas');

  Route::get('usuario', 'UsuariosController@create');

  Route::post('usuario/create', 'UsuariosController@store');

  Route::get('usuario/list', 'UsuariosController@listar');

  Route::get('usuario/edit/{id}', 'UsuariosController@edit')->where(['id' => '[0-9]+']);

  Route::post('usuario/edit', 'UsuariosController@storeEdit');

  Route::get('adwords', 'ControlsController@adwords');

  Route::get('verificarOii/{oii}/{clientes_id}', 'SalesController@verificarOrderItemId');

  // Rutas de Control

  Route::get('evolucion','ControlsController@indexEvolucion');
  Route::get('data_evolucion','ControlsController@dataEvolucion');
  Route::get('getDatosSaldoProv/{id}','ControlsController@getDatosSaldoProv');
  Route::post('edit_saldo_prov','ControlsController@editSaldoProv');
  Route::get('control_ventas_bancos','ControlsController@ventasPerBancos');
  Route::get('verificar_venta_banco/{id}','ControlsController@verificarVentaPerBanco')->where(['id' => '[\d]+']);

});
