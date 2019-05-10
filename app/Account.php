<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'cuentas';

    public function ScopeAccountGames($query,$obj){
      return DB::table('cuentas')
          ->select(
            'ID AS id',
            'mail_fake',
            'stk.juego',
            'stk.cuentas_id'
            )->leftjoin(DB::raw("
              (SELECT cuentas_id, group_concat(concat(titulo, ':', consola)) AS juego
              FROM stock WHERE cuentas_id IS NOT NULL GROUP BY cuentas_id)
              as stk"
            ), function($join)
            {
              $join->on('cuentas.id', '=', 'stk.cuentas_id');
            })
            ->where(function ($query) use ($obj) {
                if (!empty($obj->column) && !empty($obj->word)) {
                  $query->where($obj->column,'like','%'.$obj->word.'%');
                }
              })
            ->orderBy('cuentas.id','DESC');
    }

    public function ScopeCuentasNotas($query,$obj){
      $query = DB::table('cuentas_notas')->select('cuentas_notas.*')->join('cuentas','cuentas.ID','=','cuentas_notas.cuentas_id')
      ->orderBy('cuentas_notas.ID', 'DESC');

      if (!empty($obj->column) && !empty($obj->word)) {
        if ($obj->column == 'Notas') {
          $query->where("cuentas_notas.$obj->column",'like',"%$obj->word%");
        } else {

          $query->where("cuentas.$obj->column",'like',"%$obj->word%");
        }
      }

      return $query;
    }

    public function ScopeAccountStockId($query,$id){
      return DB::table('cuentas')
          ->select(
            'cuentas.ID',
            'mail_fake',
            'name',
            'surname',
            'stk.Q_Stk'
            )->leftjoin(DB::raw("
              (SELECT cuentas_id, COUNT(*) AS Q_Stk FROM stock GROUP BY cuentas_id) AS stk"
            ), function($join)
            {
              $join->on('cuentas.ID', '=', 'stk.cuentas_id');
            })
            ->whereRaw("(Q_Stk < 2 OR Q_Stk IS NULL) AND (ID = $id) ")
            ->orderBy('cuentas.id','DESC');
    }

    // Detalle de cuentas con Reseteo por ID
    public function ScopeResetAccountDetail($query, $id){
      if (empty($id))
        $id = "-1";

      return $query->select(DB::raw("cuentas.*, reset.*, DATEDIFF(NOW(), dayreseteo) AS days_from_reset"))
                    ->leftjoin(DB::raw("
                    (SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
                    FROM reseteo
                    GROUP BY cuentas_id
                    ORDER BY ID DESC) AS reset"
                    ), function($join)
                    {
                      $join->on('ID', '=', 'r_cuentas_id');
                    })
                    ->where('ID',$id)
                    ->orderBy('ID','DESC');

    }


    // cuentas con saldo, pasando el parametro de consola a buscar
    public function ScopeAccountAmounts($query, $console){
      return DB::table(DB::raw("(SELECT cuentas_id as sa_cta_id, SUM(costo_usd) as sa_costo_usd,
                                SUM(costo) as sa_costo
                                FROM saldo GROUP BY cuentas_id) as saldo"))
                ->select(
                  'sa_cta_id AS cuentas_id',
                  'consola',
                  DB::raw("(sa_costo_usd - COALESCE(st_costo_usd,0)) libre_usd"),
                  DB::raw("(sa_costo - COALESCE(st_costo,0)) libre_ars")
                  )->leftjoin(DB::raw("
                    (SELECT cuentas_id as st_cta_id,
                    SUM(costo_usd) as st_costo_usd,
                    SUM(costo) as st_costo,
                    GROUP_CONCAT(consola) as consola
                    FROM stock GROUP BY cuentas_id) as stock"
                  ), function($join)
                  {
                    $join->on('saldo.sa_cta_id', '=', 'stock.st_cta_id');
                  })
                  ->whereRaw("(sa_costo_usd - COALESCE(st_costo_usd,0)) != 0.00")
                  ->where(function ($query) use ($console) {
                      if (!empty($console)) {
                        $query->where('consola', 'like', '%'.$console.'%');
                      }
                    })
                  ->orderBy("libre_usd",'DESC');
    }

    // cuentas para juegos PS3
    public function ScopeAccountGamesPs3($query){
      return DB::table(DB::raw("
                      (SELECT COUNT(*) as Q, stock.ID as ID_stk,
                              costo_usd,
                              GROUP_CONCAT(consola) as cons,
                              cuentas_id,
                              cuentas.ID as id,
                              mail,
                              mail_fake FROM `stock`
                              LEFT JOIN cuentas
                              ON stock.cuentas_id = cuentas.ID
                              WHERE cuentas_id IS NOT NULL
                              GROUP BY cuentas_id) AS rdo"))
          ->whereRaw("rdo.cons NOT LIKE '%ps3%'
                      and rdo.cons != 'ps'
                      and rdo.id != '5288'
                      and (costo_usd='10.00'
                      or costo_usd='20.00'
                      or costo_usd='30.00'
                      or costo_usd='40.00'
                      or costo_usd='50.00'
                      or costo_usd='60.00'
                      or costo_usd='70.00'
                      or costo_usd='80.00'
                      or costo_usd='90.00'
                      or costo_usd='100.00'
                      or costo_usd='110.00'
                      or costo_usd='120.00'
                      or costo_usd='130.00'
                      or costo_usd='140.00')")
          ->orderBy('costo_usd','DESC');
    }

    // cuentas para juegos PS4
    public function ScopeAccountGamesPs4($query){
        return DB::table(DB::raw("
                      (SELECT COUNT(*) as Q, stock.ID as ID_stk,
                              costo_usd,
                              GROUP_CONCAT(consola) as cons,
                              cuentas_id,
                              cuentas.ID as id,
                              mail,
                              mail_fake FROM `stock`
                              LEFT JOIN cuentas
                              ON stock.cuentas_id = cuentas.ID
                              WHERE cuentas_id IS NOT NULL
                              GROUP BY cuentas_id) AS rdo"))
            ->whereRaw("rdo.cons NOT LIKE '%ps4%'
                      and rdo.cons != 'ps'
                      and rdo.id != '5288'
                      and (costo_usd='10.00'
                      or costo_usd='20.00'
                      or costo_usd='30.00'
                      or costo_usd='40.00'
                      or costo_usd='50.00'
                      or costo_usd='60.00'
                      or costo_usd='70.00'
                      or costo_usd='80.00'
                      or costo_usd='90.00'
                      or costo_usd='100.00'
                      or costo_usd='110.00'
                      or costo_usd='120.00'
                      or costo_usd='130.00'
                      or costo_usd='140.00')")
            ->orderBy('costo_usd','DESC');
    }

    public function scopeSiguiente($query, $id){
        $dato = $query->select('ID')
            ->where('ID','>',$id)
            ->orderBy('ID','asc');

        return $dato;
    }

    public function scopePrevio($query, $id){
        $dato = $query->select('ID')
            ->where('ID','<',$id)
            ->orderBy('ID','desc');

        return $dato;
    }

    public function createAccount($arrayAcount){
      return DB::table('cuentas')->insertGetId($arrayAcount);
    }

    public function updateAccount($data,$id){
      return DB::table('cuentas')->where('ID',$id)->update($data);
    }

    public function createAccountMod($data){
    }

    public function createAccPass($data){
      return DB::table('cta_pass')->insert($data);
    }

    public function ScopeAccountByColumnWord($query, $obj){

      if (!empty($obj->column) && !empty($obj->word)) {
        return DB::table('cuentas')->where($obj->column,$obj->word);
      }

    }

    public function ScopeListaYopmail($query)
    {
      return DB::table('stock')
      ->select(
        'cuentas_id',
        'mail_fake',
        'titulo',
        'consola',
        DB::raw('GROUP_CONCAT(stock.ID) as STK'),
        DB::raw('SUM(costo_usd) as Costo'),
        DB::raw('(SUM(costo_usd)/30) as Costo2, DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day)))) as Day'),
        DB::raw('(SQRT(120/(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))))) as Day2'),
        DB::raw('(SUM(costo_usd)/30) + (SQRT(120/(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))))) + IF(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))>360, -1, 0) + IF(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))>500, -2, 0) as FINAL')
      )
      ->leftjoin('cuentas','stock.cuentas_id','=','cuentas.ID')
      ->whereNotNull('stock.cuentas_id')
      ->where('cuentas.mail_fake','LIKE','%yopmail%')
      ->groupBy('stock.cuentas_id')
      ->orderBy('FINAL','DESC');
    }

    public function getClientsSales($account_id)
    {

      $query1 = DB::table('ventas AS v')
      ->select(
        DB::raw("CONCAT('#',v.clientes_id,' ', CONCAT_WS(' ',c.nombre,c.apellido), ' ', s.titulo,' (',s.consola,')',IF(v.slot='Secundario',CONCAT(' (',v.slot,')'),'')) AS cliente"),
        'v.clientes_id'
      )
      ->join('stock AS s','v.stock_id','=','s.ID')
      ->join('clientes AS c','v.clientes_id','=','c.ID')
      ->where('s.cuentas_id', $account_id)
      ->where('s.consola','ps4')
      ->orderBy('v.ID','DESC');

      $query2 = DB::table('ventas AS v')
      ->select(
        DB::raw("CONCAT('#',v.clientes_id,' ', CONCAT_WS(' ',c.nombre,c.apellido), ' ', s.titulo,' (',s.consola,')',IF(v.slot='Secundario',CONCAT(' (',v.slot,')'),'')) AS cliente"),
        'v.clientes_id'
      )
      ->join('stock AS s','v.stock_id','=','s.ID')
      ->join('clientes AS c','v.clientes_id','=','c.ID')
      ->where('s.cuentas_id', $account_id)
      ->whereNotIn('s.consola',['ps3','ps4'])
      ->orderBy('v.ID','DESC');

      return DB::table('ventas AS v')
      ->select(
        DB::raw("CONCAT('#',v.clientes_id,' ', CONCAT_WS(' ',c.nombre,c.apellido), ' ', s.titulo,' (',s.consola,')',IF(v.slot='Secundario',CONCAT(' (',v.slot,')'),'')) AS cliente"),
        'v.clientes_id'
      )
      ->join('stock AS s','v.stock_id','=','s.ID')
      ->join('clientes AS c','v.clientes_id','=','c.ID')
      ->where('s.cuentas_id', $account_id)
      ->where('s.consola','ps3')
      ->limit(4)
      ->orderBy('v.ID','DESC')
      ->union($query1)
      ->union($query2);


    }


}
