<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ControlsController extends Controller
{
    public function ventasPerBancos()
    {
    	$ventas = DB::table('ventas_cobro')
    				->select(
    					'ventas.ID AS ID_ventas',
    					'ventas_cobro.ID AS cobro_ID',
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
    					'verificado'
    				)
    				->leftjoin('ventas','ventas_cobro.ventas_id','=','ventas.ID')
    				->leftjoin('clientes','ventas.clientes_id','=','clientes.ID')
    				->leftjoin('stock','ventas.stock_id','=','stock.ID')
    				->leftjoin('ventas_cobro_bancos AS vcb','ventas_cobro.ID','=','vcb.cobros_id')
    				->where('medio_cobro','Banco')
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
    	$ventas = DB::table('ventas_cobro')
    				->select(
    					'ventas.ID AS ID_ventas',
    					'ventas_cobro.ID AS cobro_ID',
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
    					'verificado'
    				)
    				->leftjoin('ventas','ventas_cobro.ventas_id','=','ventas.ID')
    				->leftjoin('clientes','ventas.clientes_id','=','clientes.ID')
    				->leftjoin('stock','ventas.stock_id','=','stock.ID')
    				->leftjoin('ventas_cobro_bancos AS vcb','ventas_cobro.ID','=','vcb.cobros_id')
    				->where('medio_cobro','Banco')
    				->offset($inicio)
    				->limit($fin)
    				->get();

    	return $ventas;
    }

    public function verificarVentaPerBanco($id)
    {
    	DB::beginTransaction();

    	try {
    		DB::table('ventas_cobro_bancos')->where('cobros_id',$id)->update(['verificado' => 1]);
    		DB::commit();

    		\Helper::messageFlash('Ventas Cobro','Venta verificada.');

    		return redirect()->back();
    	} catch (Exception $e) {
    		DB::rollback();
    		return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
    	}
    }
}
