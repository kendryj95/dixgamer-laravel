<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Balance;
use Schema;

class BalanceController extends Controller
{
    public function listarSaldos(Request $request) {

    	// Columnas de la base de datos
      	$columns = Schema::getColumnListing('saldo');

    	// saldos con filtro
	      $obj = new \stdClass;
	      $obj->column = $request->column;
	      $obj->word = $request->word;

    	$saldos = Balance::listaSaldos($obj)->paginate(200);

    	return view('balance.index',compact('columns','saldos'));
    }
}
