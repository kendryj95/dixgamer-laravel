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

  // Rutas para clientes
  Route::resource('clientes', 'CustomerController');
  Route::post('customer_ctrl_email', 'CustomerController@customerCtrlEmail');
  Route::post('customer_ctrl_ml_user', 'CustomerController@customerCtrlMlUsr');
  Route::get('customer_ventas_modificar/{id}/{opt}', 'CustomerController@ventasModificar');
  Route::post('customer_ventas_modificar_store', 'CustomerController@ventasModificarStore');
  Route::get('customer_ventas_modificar_producto/{id}', 'CustomerController@ventasModificarProductos');
  Route::post('customer_ventas_modificar_producto_store', 'CustomerController@ventasModificarProductosStore');
  Route::get('customer_ventas_eliminar/{id}', 'CustomerController@ventasEliminar');
  Route::post('customer_ventas_eliminar', 'CustomerController@ventas_delete');
  Route::get('customer_ventas_quitar_producto/{id}', 'CustomerController@ventarQuitarProducto');

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
  Route::get('cuentas_con_saldo', 'AccountController@accountAmount');
  Route::get('cuentas_para_ps3', 'AccountController@accountGamePs3');
  Route::get('cuentas_para_ps4', 'AccountController@accountGamePs4');
  Route::post('account_ctrl_column', 'AccountController@accountCtrlColumn');
  Route::get('recharge_account/{id}', 'AccountController@rechargeBalance');
  Route::get('crear_saldo_cuenta/{account_id}/{title}/{console}', 'AccountController@storeBalanceAccount');
  Route::get('crear_nota_cuenta/{account_id}', 'AccountController@createNote');
  Route::get('editar_direccion_cuenta/{account_id}', 'AccountController@editAddressAccount');
  Route::post('guardar_nota_cuenta/{account_id}', 'AccountController@storeNote');
  Route::post('actualizar_password_cuenta/{account_id}', 'AccountController@updatePassword');
  Route::post('actualizar_direccion_cuenta/{account_id}', 'AccountController@updateAddressAccount');
  Route::post('devolver_saldo_cuentas', 'AccountController@rollbackBalance');
  Route::post('resetear_cuenta/{id}', 'AccountController@resetAccount');
  Route::get('solicitar_reseteo_cuenta/{id}', 'AccountController@requestReset');
  Route::post('solicitar_reseteo_cuenta/{id}', 'AccountController@storeRequestReset');
  Route::get('stock_insertar_cuenta/{id}', 'AccountController@createStockAccount');
  Route::post('stock_insertar_cuenta/{id}', 'AccountController@storeStockAccount');
  Route::get('stock_pre_insertar_cuenta/{id}', 'AccountController@createLastStock');
  Route::post('guardar_stock_masivo/{id}', 'AccountController@storeLastStock');
  Route::get('actualizar_stock_cuenta/{stock_id}/{account_id}', 'AccountController@editStockAccount');
  Route::post('actualizar_stock_cuenta/{account_id}', 'AccountController@updateStockAccount');
  Route::post('repetir_ultima_cuenta/{account_id}', 'AccountController@repeatLastAccount');

  // Rutas para stock
  Route::resource('stock', 'StockController');
  Route::get('catalogo_link_ps_store', 'StockController@indexLinkPsStore');
  Route::get('productos_catalogo', 'StockController@indexCatalogueProduct');




  // Rutas donde solo podran acceder analistas y administradores
  Route::group(['middleware' => ['analyst']], function()
  {
    // P1
    Route::get('stock_insertar_codigo','StockController@createCode');
    Route::post('stock_insertar_codigo','StockController@storeCode');
    Route::get('validaCodigo','StockController@validaCodigo');

  });

    // Rutas donde solo podran acceder administradores
  Route::group(['middleware' => ['administrator']], function()
  {
    // P2
    Route::get('stock_insertar_codigo_g','StockController@createCodeG');
    Route::post('stock_insertar_codigo_g','StockController@storeCodeG');


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
        Route::get('horarios/verificar/{id}', 'ScheduleController@verificarHorario');
        Route::get('horarios/edit/{id}', 'ScheduleController@editarHorario')->where(['id' => '[0-9]+']);
        Route::post('horarios/editar', 'ScheduleController@editHorario');
    });



  // Pueden acceder todos menos administrador
  Route::group(['middleware' => ['less.admin']], function()
  {

    Route::resource('horario','ScheduleController');

  });

  Route::get('sales/list','SalesController@index')->name('sales/list');
  Route::get('sales/manual/add/{consola}/{titulo}/{slot}','SalesController@addManualSale')->name('sales/manual/add/{consola}/{titulo}/{slot}');
  Route::post('saveManualSale','SalesController@saveManualSale')->name('saveManualSale');
  //Route::get('web/sales','Pedidos_CobradosController@index')->name('web/sales');
    Route::get('web/sales','Pedidos_CobradosController@index')->name('web/sales');
    Route::post('getDataClientWebSales','Pedidos_CobradosController@getDataClientWebSales')->name('getDataClientWebSales');

  Route::get('usuario', 'UsuariosController@create');

  Route::post('usuario/create', 'UsuariosController@store');

  Route::get('usuario/list', 'UsuariosController@listar');

  Route::get('usuario/edit/{id}', 'UsuariosController@edit')->where(['id' => '[0-9]+']);

  Route::post('usuario/edit', 'UsuariosController@storeEdit');

});
