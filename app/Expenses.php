<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Expenses extends Model
{
  protected $table = 'gastos';


  public function ScopeExpensesGroup($query){
    return $query->groupBy('concepto')
                ->orderBy('concepto','ASC');
  }

  public function ScopeExpenses($query,$concept,$obj){
    return $query->where(function ($query) use ($concept) {

        if (!empty($concept)) {
          $query->where('concepto','like','%'.$concept.'%');
        }

      })->where(function ($query) use ($obj) {
        if (!empty($obj->column) && !empty($obj->word)) {
          $query->where($obj->column,'like','%'.$obj->word.'%');
        }
      })->orderBy('ID','DESC');
  }

  public function storeExpenses($expenses){
      return DB::table('gastos')->insert($expenses);
  }


  public function ScopeExpensesIncome($query){
    return DB::select(DB::raw("
        SELECT (gasto/ingreso) as gto_x_ing
        FROM (SELECT (SELECT SUM(importe)
        as Gto_Tot FROM gastos WHERE
        concepto NOT LIKE '%IIBB%') as gasto,
        (SELECT SUM(precio) as Ing_Tot FROM ventas_cobro) as ingreso) as
        resultado
    "));

  }

  public function gastosControlVentas()
  {
    return DB::table(DB::raw("(SELECT (SELECT SUM(importe) as Gto_Tot FROM gastos WHERE concepto NOT LIKE '%IIBB%') as gasto, (SELECT SUM(precio) as Ing_Tot FROM ventas_cobro) as ingreso) as resultado"));
  }

}
