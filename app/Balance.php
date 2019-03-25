<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Balance extends Model
{
    protected $table = 'saldo';


    public function ScopeAccountBalance($query,$id){
      return DB::select(DB::raw("
      SELECT ex_stock_id as stk_id, 'carga' as concepto, cuentas_id, costo, costo_usd, code, code_prov, n_order, usuario FROM saldo WHERE cuentas_id = $id
      UNION ALL
      SELECT ID as stk_id, 'descarga' as concepto, cuentas_id, (-1 * SUM(costo)) as costo, (-1 * SUM(costo_usd)) as costo_usd, '' as code, '' as code_prov, '' as n_order, usuario FROM stock WHERE cuentas_id = $id"));

    }


    public function balanceAccount($id){
      return DB::select(DB::raw("
        SELECT SUM(costo) as costo, SUM(costo_usd) as costo_usd FROM
        (SELECT ex_stock_id as stk_id, 'carga' as concepto, cuentas_id, costo, costo_usd, code, code_prov, n_order, usuario FROM saldo WHERE cuentas_id = $id
        UNION ALL
        SELECT ID as stk_id, 'descarga' as concepto, cuentas_id, (-1 * SUM(costo)) as costo, (-1 * SUM(costo_usd)) as costo_usd, '' as code, '' as code_prov, '' as n_order, usuario FROM stock WHERE cuentas_id = $id)
        As resultado
      "));
    }

    public function ScopeAccountHasBalance($query,$id){
      return $query->select('cuentas_id', 'costo_usd', 'code', 'code_prov', 'usuario')
                    ->where('cuentas_id',$id);

    }


    public function ScopeBalanceByExEstockId($query,$id){
      return $query->select("*")->where('ex_stock_id',$id);
    }

    public function reChargeGifCards(){
      return DB::select(DB::raw("SELECT ID AS ID_stk, titulo, consola,
              cuentas_id AS stk_ctas_id, medio_pago, FORMAT(costo_usd,2) AS costo_usd,
              costo, ID_vtas, Q_vta, dayvta, COUNT(*) AS Q_Stock
              FROM stock
              LEFT JOIN
              (SELECT ventas.ID as ID_vtas, stock_id, slot, COUNT(*) AS Q_vta, Day AS dayvta
              FROM ventas
              GROUP BY stock_id
              ORDER BY ID DESC) AS vendido
              ON ID = stock_id
              WHERE (consola = 'ps')
              AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot')
              AND (titulo != 'plus-1-mes') AND (titulo != 'plus-3-meses') AND (titulo != 'gift-card-75-usd')
              AND (titulo != 'gift-card-100-usd') AND (titulo NOT LIKE '%points-fifa%')
              GROUP BY consola, titulo
              ORDER BY consola DESC, titulo ASC, ID ASC"));
    }
    public function storeBalanceAccount($data){

      return DB::table('saldo')->insert($data);
    }


    public function ScopeTotalBalanceAccount($query,$id){
      $query->select(
          'cuentas_id',
          'medio_pago',
          DB::raw("SUM(costo_usd) as costo_usd"),
          DB::raw("SUM(costo) as costo")
          )
          ->where('cuentas_id',$id)
          ->groupBy('cuentas_id')
          ->orderBy('ID','DESC');
    }

    public function ScopeLastAccountIdBalance($query, $seller)
    {
      $query->select('cuentas_id')->where('usuario',$seller)->orderBy('ID','DESC');
    }

    public function ScopeLastGiftsCharged($query,$seller,$lastAccountId)
    {
      $query->select(
        'titulo',
        'Day'
      )
      ->where('usuario',$seller)
      ->where('cuentas_id',$lastAccountId)
      ->orderBy('ID','DESC')
      ->limit(3);
    }




}
