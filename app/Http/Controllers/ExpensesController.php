<?php

namespace App\Http\Controllers;

use App\Expenses;
use Illuminate\Http\Request;
use Auth;
use Schema;
use DB;
use Validator;
class ExpensesController extends Controller
{

    private $exp;
    function __construct(){
      $this->exp = new Expenses;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // cuentas con filtro
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $concepts = Expenses::expensesGroup()->get();
        $expenses = Expenses::expenses($request->concepto,$obj)->paginate(50);

        // Columnas de la base de datos
        $columns = Schema::getColumnListing('gastos');
        return view('expenses.index',compact(
          'expenses',
          'columns',
          'concepts'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // Mensajes de alerta
      $msgs = [
        'concepto.required' => 'Concepto requerido',
        'importe.required' => 'Importe requerido',
        'nro_transac.required' => 'Número de transacción requerido',
        'medio_pago.required' => 'Medio de pago requerido'
      ];
      // Validamos
      $v = Validator::make($request->all(), [
          'concepto' => 'required',
          'importe' => 'required',
          'nro_transac' => 'required',
          'medio_pago' => 'required'
      ], $msgs);


      // Si hay errores retornamos a la pantalla anterior con los mensajes
      if ($v->fails())
      {
          return redirect()->back()->withErrors($v->errors());
      }


      try {
        $expensesArray = [];
        $expensesArray['concepto'] = $request->concepto;
        $expensesArray['importe'] = $request->importe;
        $expensesArray['nro_transac'] = $request->nro_transac;
        $expensesArray['medio_pago'] = $request->medio_pago;
        $expensesArray['Notas'] = $request->Notas;
        $expensesArray['Day'] = date('Y-m-d H:i:s', time());

        $this->exp->storeExpenses($expensesArray);



        \Helper::messageFlash('Gastos','Gasto guardado');

        return redirect('/gastos');
      } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Intentelo nuevamente']);
      }




    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function show(Expenses $expenses)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function edit(Expenses $expenses)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Expenses $expenses)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expenses  $expenses
     * @return \Illuminate\Http\Response
     */
    public function destroy(Expenses $expenses)
    {
        //
    }
}
