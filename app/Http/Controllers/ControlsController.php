<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ControlsController extends Controller
{
    public function ventasPerBancos()
    {
    	$ventas = DB::table('ventas_cobro_bancos AS vcb')
    				->select(
    					'vcb.ventas_id AS ID_ventas',
    					'clientes_id',
    					'stock_id',
    					'slot',
    					'medio_venta',
    					'medio_cobro',
    					'precio',
    					'comision',
    					'ventas.Notas AS ventas_Notas',
    					'ventas.Day as ventas_Day',
    					'ventas.usuario as ventas_usuario',
    					'apellido',
    					'nombre',
    					'titulo',
    					'consola',
    					'cuentas_id',
    					'costo',
    					'q_vta',
    					'verificado'
    				)
    				->leftjoin('ventas','ventas.ID','=','vcb.ventas_id')
    				->leftjoin(DB::raw("(SELECT * FROM ventas_cobro WHERE medio_cobro='Banco') AS ventas_cobro"),'ventas_cobro.ventas_id','=','vcb.ventas_id')
    				->leftjoin('clientes','ventas.clientes_id','=','clientes.ID')
    				->leftjoin(DB::raw("(select ID, titulo, consola, cuentas_id, costo, q_vta FROM stock LEFT JOIN (select count(*) as q_vta, stock_id from ventas group by stock_id) AS vendido ON stock.ID = vendido.stock_id) AS stock"),'ventas.stock_id','=','stock.ID')
    				->groupBy('ID_ventas')
    				->orderBy('ID_ventas','DESC')
    				->get();

    	$tamPag = 50;
    	$numReg = count($ventas);
    	$paginas = ceil($numReg/$tamPag);
    	$limit = "";
    	$paginaAct = "";
    	if (!isset($_GET['pag'])) {
    	    $paginaAct = 1;
    	    $limit = 0;
    	} else {
    	    $paginaAct = $_GET['pag'];
    	    $limit = ($paginaAct-1) * $tamPag;
    	}

    	$ventas = $this->consultaPagination($limit, $tamPag);

    	return view('control.control_ventas_bancos', compact('ventas', 'paginas', 'paginaAct'));
    }

    private function consultaPagination($inicio,$fin)
    {
    	$ventas = DB::table('ventas_cobro_bancos AS vcb')
    				->select(
    					'vcb.ventas_id AS ID_ventas',
    					'clientes_id',
    					'stock_id',
    					'slot',
    					'medio_venta',
    					'medio_cobro',
    					'precio',
    					'comision',
    					'ventas.Notas AS ventas_Notas',
    					'ventas.Day as ventas_Day',
    					'ventas.usuario as ventas_usuario',
    					'apellido',
    					'nombre',
    					'titulo',
    					'consola',
    					'cuentas_id',
    					'costo',
    					'q_vta',
    					'verificado'
    				)
    				->leftjoin('ventas','ventas.ID','=','vcb.ventas_id')
    				->leftjoin(DB::raw("(SELECT * FROM ventas_cobro WHERE medio_cobro='Banco') AS ventas_cobro"),'ventas_cobro.ventas_id','=','vcb.ventas_id')
    				->leftjoin('clientes','ventas.clientes_id','=','clientes.ID')
    				->leftjoin(DB::raw("(select ID, titulo, consola, cuentas_id, costo, q_vta FROM stock LEFT JOIN (select count(*) as q_vta, stock_id from ventas group by stock_id) AS vendido ON stock.ID = vendido.stock_id) AS stock"),'ventas.stock_id','=','stock.ID')
    				->groupBy('ID_ventas')
    				->orderBy('ID_ventas','DESC')
    				->offset($inicio)
    				->limit($fin)
    				->get();

    	return $ventas;
    }

    public function verificarVentaPerBanco($id)
    {
    	DB::beginTransaction();

    	try {
    		DB::table('ventas_cobro_bancos')->where('ventas_id',$id)->update(['verificado' => 1]);
    		DB::commit();

    		\Helper::messageFlash('Ventas Cobro','Venta verificada.');

    		return redirect()->back();
    	} catch (Exception $e) {
    		DB::rollback();
    		return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
    	}
    }
}
