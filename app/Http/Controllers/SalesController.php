<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Customer;
use App\Stock;
use App\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class SalesController extends Controller
{
    //
    public function index(Request $request){

        $datos = $this->QueryIndex();

        return view('sales.sales_list')->with(['datos' => $datos]);
    }

    public function QueryIndex(){
        $data = DB::select('SELECT ventas.ID AS ID_ventas, 
                                  clientes_id, 
                                  stock_id, 
                                  slot, 
                                  medio_venta, 
                                  medio_cobro, 
                                  precio, 
                                  comision, 
                                  ventas.Notas AS ventas_Notas, 
                                  ventas.Day as ventas_Day, 
                                  apellido, 
                                  nombre, 
                                  titulo, 
                                  consola 
                                  FROM ventas
                            LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
                            LEFT JOIN clientes ON ventas.clientes_id = clientes.ID
                            LEFT JOIN stock ON ventas.stock_id = stock.ID ORDER BY ventas.ID DESC');
        return $data;
    }
}
