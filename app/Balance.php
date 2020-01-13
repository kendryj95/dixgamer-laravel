<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Balance extends Model
{
    protected $table = 'saldo';


    public function ScopeAccountBalance($query,$id){
      return DB::select(DB::raw("
      SELECT ID, ex_stock_id as stk_id, 'carga' as concepto, cuentas_id, costo, costo_usd, code, code_prov, n_order, usuario, Day, IF((Day BETWEEN DATE_SUB(NOW(), INTERVAL 1 DAY) AND NOW()),'Mostrar','No Mostrar') AS validacion FROM saldo WHERE cuentas_id = $id
      UNION ALL
      SELECT ID, ID as stk_id, 'descarga' as concepto, cuentas_id, (-1 * SUM(costo)) as costo, (-1 * SUM(costo_usd)) as costo_usd, '' as code, '' as code_prov, '' as n_order, usuario, '' AS Day, '' AS validacion FROM stock WHERE cuentas_id = $id"));

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

    public function reChargeGifCards($minim = false){
      if (!$minim) {
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
                AND costo_usd >= 10
                AND (Q_vta IS NULL) AND (titulo != 'plus-12-meses-slot')
                AND (titulo != 'plus-1-mes') AND (titulo != 'plus-3-meses') AND (titulo != 'gift-card-75-usd')
                AND (titulo != 'gift-card-100-usd') AND (titulo NOT LIKE '%points-fifa%')
                GROUP BY consola, titulo
                ORDER BY consola DESC, titulo ASC, ID ASC"));
      } else {

        $query = "";
		//$nombre = session()->get('usuario')->Nombre;
    $nombre= session()->get('usuario')->Nombre;

        if (session()->get('usuario')->modo_continuo == 0) {

          $dias_modo = DB::table('configuraciones')->where('ID',1)->value('dias_modo_continuo');
          $hours = 24 * $dias_modo;
          
          $query = "SELECT ID AS ID_stk, titulo, consola, FORMAT(costo_usd,2) AS costo_usd, costo, COUNT(*) AS Q_Stock
            FROM (SELECT * FROM stock
              WHERE ID > 100000 
              AND consola='ps'
              AND costo_usd < 10
              GROUP BY costo_usd, TRIM(SUBSTRING(stock.code,1,19))
			  ) as agrupado
          WHERE NOT EXISTS (SELECT TRIM(SUBSTRING(code,1,19)) AS code_subs FROM `saldo` WHERE `costo_usd` < 10 AND ex_stock_id > 100000 AND NOW() <= DATE_ADD(Day, INTERVAL $hours HOUR) HAVING code_subs = TRIM(SUBSTRING(agrupado.code,1,19)))
          GROUP BY titulo
          ORDER BY titulo ASC, ID ASC";
        } else {
          $query = "SELECT ID AS ID_stk, titulo, consola, FORMAT(costo_usd,2) AS costo_usd, costo, SUM(cant) AS Q_Uso, COUNT(cant) AS Q_GC
            FROM (SELECT *, COUNT(ID) as cant FROM stock
              WHERE ID > 100000 
              AND consola='ps'
              AND costo_usd < 10
              GROUP BY costo_usd, TRIM(SUBSTRING(stock.code,1,19))
              ORDER BY ID ASC) as agrupado
          WHERE NOT EXISTS (SELECT TRIM(SUBSTRING(code,1,19)) AS code_subs FROM `saldo` WHERE `costo_usd` < 10 AND ex_stock_id > 100000 AND usuario != '$nombre' AND NOW() <= DATE_ADD(Day, INTERVAL 24 HOUR) HAVING code_subs = TRIM(SUBSTRING(agrupado.code,1,19)))
          GROUP BY titulo
          ORDER BY titulo ASC, ID ASC";
        }

        //dd($query); 

        return DB::select(DB::raw($query));
      }
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

    public function balanceMensual()
    {
      return DB::select("SELECT M_S, qty, precio, comision, costo, IFNULL(gasto,0) as gasto, (precio - comision - costo - IFNULL(gasto,0)) as ganancia FROM
      (SELECT DATE_FORMAT(Day,'%Y-%m') AS M_S, round(SUM(costo)) AS costo FROM stock GROUP BY M_S) as costos
      LEFT JOIN
      (SELECT DATE_FORMAT(Day,'%Y-%m') AS M_V, COUNT(ID) AS qty FROM ventas GROUP BY M_V) as ventas
      ON M_S = M_V
      LEFT JOIN
      (SELECT DATE_FORMAT(Day,'%Y-%m') AS M_C, round(SUM(precio)) AS precio, round(SUM(comision)) AS comision FROM ventas_cobro GROUP BY M_C) as cobros
      ON M_S = M_C
      LEFT JOIN
      (SELECT DATE_FORMAT(Day,'%Y-%m') AS M_G, round(SUM(importe)) AS gasto FROM gastos GROUP BY M_G) as gastos
      ON M_S = M_G
      ORDER BY M_S ASC");
    }

    public function scopeListaSaldos($query, $obj)
    {
      $query = DB::table('saldo')->select('*',DB::raw("(SELECT color FROM usuarios WHERE Nombre = saldo.usuario) AS color_user"));

      if ($obj->column && $obj->word) {
        $query->where($obj->column,'LIKE',"%$obj->word%");
      }

      $query->orderBy('ID','DESC');

      return $query;
    }

    public function scopeGiftMinimAvailable($query,$code_gift) {
      return DB::table('saldo')
      ->select(DB::raw("TRIM(SUBSTRING(code,1,19)) AS code_subs"))
      ->where('costo_usd','<',10)
      ->where(DB::raw("NOW() <= DATE_ADD(Day, INTERVAL 24 HOUR)"))
      ->having('code_subs',$code_gift);
    }




}
