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
Route::get('logout',function(){
  Auth::logout();
  return redirect('login');
});


// Solo usuarios logueados
Route::group(['middleware' => ['auth']], function()
{
  Route::get('/', 'HomeController@index');
  Route::get('home', 'HomeController@index');

  // Rutas para clientes
  Route::resource('clientes', 'CustomerController');
  Route::post('customer_ctrl_email', 'CustomerController@customerCtrlEmail');
  Route::post('customer_ctrl_ml_user', 'CustomerController@customerCtrlMlUsr');

  //Card functions buttons
  Route::post('getDataName','EditButtonsController@getDataName')->name('getDataName');
    Route::post('saveDataName','EditButtonsController@saveDataName')->name('saveDataName');
    Route::post('saveDataEmail','EditButtonsController@saveDataEmail')->name('saveDataEmail');
    Route::post('saveDataML','EditButtonsController@saveDataML')->name('saveDataML');
    Route::post('saveDataOther','EditButtonsController@saveDataOther')->name('saveDataOther');
    Route::post('saveNotes','EditButtonsController@saveNotes')->name('saveNotes');
  Route::post('updateStatusReseller','CustomerController@updateStatusReseller')->name('updateStatusReseller');

  Route::get('prubasam', function (){
     $veamos = \DB::select('*')
         ->from(\DB::raw('(SELECT
			COUNT(*) AS Q,
			stock.ID AS ID_stk,
			costo_usd,
			GROUP_CONCAT(consola) AS cons,
			cuentas_id,
			cuentas.ID AS ID,
			mail,
			mail_fake
		FROM
			stock
		LEFT JOIN cuentas ON stock.cuentas_id = cuentas.ID
		WHERE
			cuentas_id IS NOT NULL
		GROUP BY
			cuentas_id) as rdo'))
             ->whereNotLike('cons')
             ->where( 'cons', '!=', 'ps')
             ->where('ID', '<>',5288)
            ->whereRaw("costo_usd = '10.00'
                OR costo_usd = '20.00'
                OR costo_usd = '30.00'
                OR costo_usd = '40.00'
                OR costo_usd = '50.00'
                OR costo_usd = '60.00'
                OR costo_usd = '70.00'
                OR costo_usd = '80.00'
                OR costo_usd = '90.00'
                OR costo_usd = '100.00'
                OR costo_usd = '110.00'
                OR costo_usd = '120.00'
                OR costo_usd = '130.00'
                OR costo_usd = '140.00'")
         ->orderBy('costo_usd','DESC')

         ->get();

     return $veamos;
  });

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



  // Pueden acceder todos menos administrador
  Route::group(['middleware' => ['less.admin']], function()
  {

    Route::resource('horario','ScheduleController');

  });


});
