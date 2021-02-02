<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;
use App\Stock;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

      $OII = '';

      if ($request->order_item_id) {
        $OII .= "?order_item_id=$request->order_item_id";
      }

      if (isset($request->order_item_id)) {
        // Extraemos variable de item id
        $order_item_id = $request->order_item_id;
        // Buscamos por medio de un scope creado la orden
        $sales_order_item = Sale::salesFromOrderId($order_item_id)->first();

        // si la venta existe aviso al gestor
        if ($sales_order_item) {
          echo "Este pedido ya fue asignado por ".$sales_order_item->usuario." <br><a href='/clientes_detalles/".$sales_order_item->clientes_id."' target='_blank'>Ver Venta</a>";
        }
      }


      /***  Stock Nuevo  ***/
      $obj = new \stdClass;
      $obj->console_1 = 'ps4';
      $obj->console_2 = 'ps3';
      $obj->console_3 = 'ps5';
      $obj->title = 'plus-12-meses-slot';

      $stocks = Stock::showStock($obj)->get();
      /***     PS4 PRIMARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps5';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'primary';
      $ps5_primary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      //dd($stocks);
      /***     PS4 SECUNDARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps5';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'secundary';
      $ps5_secundary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      /***     PS4 PRIMARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'primary';
      $ps4_primary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      //dd($stocks);
      /***     PS4 SECUNDARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'secundary';
      $ps4_secundary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      /***     PS3     ***/
      $ps3_stocks =  Stock::ps3();

      /***     PS3 Resetear    ***/
      $ps3_reset_stocks =  Stock::ps3Resetear();
      return view('home',
            compact(
              'stocks',
              'ps5_primary_stocks',
              'ps5_secundary_stocks',
              'ps4_primary_stocks',
              'ps4_secundary_stocks',
              'ps3_reset_stocks',
              'ps3_stocks',
              'OII'
            ));
    }

    public function asignar_producto(Request $request)
    {

      $OII = '';

      if ($request->order_item_id) {
        $OII .= "?order_item_id=$request->order_item_id";
      }

      if (isset($request->order_item_id)) {
        // Extraemos variable de item id
        $order_item_id = $request->order_item_id;
        // Buscamos por medio de un scope creado la orden
        $sales_order_item = Sale::salesFromOrderId($order_item_id)->first();

        // si la venta existe aviso al gestor
        if ($sales_order_item) {
          echo "Este pedido ya fue asignado por ".$sales_order_item->usuario." <br><a href='". url('clientes',$sales_order_item->clientes_id) ."' target='_blank'>Ver Venta</a>";
        }
      }


      /***  Stock Nuevo  ***/
      $obj = new \stdClass;
      $obj->console_1 = 'ps4';
      $obj->console_2 = 'ps3';
      $obj->console_3 = 'ps5';
      $obj->title = 'plus-12-meses-slot';

      $stocks = Stock::showStock($obj)->get();
      /***     PS5 PRIMARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps5';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'primary';
      $ps5_primary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      //dd($stocks);
      /***     PS5 SECUNDARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps5';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'secundary';
      $ps5_secundary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      /***     PS4 PRIMARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'primary';
      $ps4_primary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      //dd($stocks);
      /***     PS4 SECUNDARIO     ***/

      $obj = new \stdClass;
      $obj->console = 'ps4';
      $obj->title = 'plus-12-meses-slot';
      $obj->type = 'secundary';
      $ps4_secundary_stocks =  Stock::primaryOrSecundaryConsole($obj)->get();

      /***     PS3     ***/
      $ps3_stocks =  Stock::ps3();

      /***     PS3 Resetear    ***/
      $ps3_reset_stocks =  Stock::ps3Resetear();
      return view('asignar_producto',
            compact(
              'stocks',
              'ps4_primary_stocks',
              'ps4_secundary_stocks',
              'ps5_primary_stocks',
              'ps5_secundary_stocks',
              'ps3_reset_stocks',
              'ps3_stocks',
              'OII'
            ));
    }
}
