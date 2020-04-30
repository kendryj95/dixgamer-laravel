<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Stock;
use App\WpPost;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*',function($settings){
            $stockObj = new Stock;
            $cantidad_por_cargar = 0;
            if (session()->has('usuario')) {
                $user = session()->get('usuario')->Nombre;
                $pedidos = $stockObj->listPedidosPorCargar($user)->get();

                foreach ($pedidos as $i => $value) {
                    $stock = WpPost::linkStoreByCondition($value->titulo,$value->consola)->first();
                    $cantidad_cargada = Stock::getCantidadStockPorCargar(date('Y-m-d',strtotime($value->Day)), $value->titulo, $value->consola, $user)->value('Q_stk');
                    $cantidad_cargada = $cantidad_cargada == '' ? 0 : $cantidad_cargada; 
                    $cantidad_por_cargar += ($value->cantidad - $cantidad_cargada);
                }
            }
            $settings->with('stockCargar', $cantidad_por_cargar);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
