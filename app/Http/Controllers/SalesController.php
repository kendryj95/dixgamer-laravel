<?php

namespace App\Http\Controllers;

use App\Sales;
use Illuminate\Http\Request;
use App\User;
use App\Customer;
use App\Stock;
use App\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    //
    public function index(Request $request){

        $datos = $this->QueryIndex();

        return view('sales.sales_list')->with(['datos' => $datos]);
    }

    public function addManualSale(Request $request, $consola, $titulo, $slot){

        $colname_rsCON = $consola;
        $colname_rsTIT = $titulo;
        $colname_rsSlot = $slot;


        $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);
        dd($row_rsSTK);

        if(! count($row_rsSTK) > 0) {
            exit('No hay stock del Juego:'. $titulo ($consola));
        }

        $stk_ID = $row_rsSTK[0]->ID_stk;



        $sqlAA = DB::select("SELECT CONCAT('[ ',ID,' ] ',nombre,' ',apellido,' - ',email) as nombre FROM clientes ORDER BY ID DESC");

        $rowsAA = [];
        for ($i=0;$i < count($sqlAA); $i++){
            $rowsAA[] = $sqlAA[$i]->nombre;
        }



        $row_rsClientes = DB::select("SELECT clientes.ID, apellido, nombre, vta.Q_Vta, email
                FROM clientes
                LEFT JOIN
                (SELECT clientes_id, COUNT(*) AS Q_Vta FROM ventas GROUP BY clientes_id) AS vta
                ON clientes.ID = vta.clientes_id
                ORDER BY ID DESC");

        $row_rsStock2 = DB::select(DB::raw("SELECT SUM(precio) as precio, ventas.ID as ventas_ID, stock.ID as stock_ID, titulo, consola, slot FROM ventas_cobro 
                    LEFT JOIN ventas ON ventas_cobro.ventas_id=ventas.ID
                    LEFT JOIN stock ON ventas.stock_id=stock.ID
                    WHERE consola='".$consola."' and titulo='".$titulo."' and slot='".$slot."'
                    GROUP BY ventas_id
                    ORDER BY ventas_ID DESC"));

        $stock_id = $row_rsSTK[0]->ID_stk;


        return view('sales.add_sale')->with([
            'colname_rsCON'     => $consola,
            'colname_rsTIT'     => $titulo,
            'colname_rsSlot'    => $slot,
            'row_rsClientes'    => $row_rsClientes,
            'rowsAA'            => $rowsAA,
            'row_rsStock2'      => $row_rsStock2,
            'stk_ID'            => $stk_ID
        ]);
    }

    public function saveManualSale(Request $request){


        $insert = DB::table('ventas')->insertGetId([
            'clientes_id'   => $request->clientes_id,
            'stock_id'      => $request->stk_ID,
            'cons'          => $request->consola,
            'slot'          => $request->slot,
            'medio_venta'   => $request->medio_venta,
            'estado'        => $request->estado,
            'Day'           => \Carbon\Carbon::now('America/New_York'),
            'Notas'         => $request->Notas,
            'usuario'       => session()->get('usuario')->Nombre

        ]);

        $venta_ID = $insert;

        $insert2 = DB::table('ventas_cobro')->insert([
            'ventas_id'     => $venta_ID,
            'medio_cobro'   => $request->medio_cobro,
            'ref_cobro'     => $request->ref_cobro,
            'precio'        => $request->precio,
            'comision'      => $request->comision,
            'Day'           => \Carbon\Carbon::now('America/New_York'),
            'Notas'         => $request->Notas,
            'usuario'       => session()->get('usuario')->Nombre
        ]);

        return redirect(url('clientes',$request->clientes_id));
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
