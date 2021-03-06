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
            'stk.cuentas_id',
            'cuentas.usuario',
            DB::raw("(SELECT color FROM usuarios WHERE Nombre = cuentas.usuario) AS color_user")
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
            ->where('cuentas.activa',1)
            ->orderBy('cuentas.id','DESC');
    }
    
    public function ScopeAccountStolen($query,$obj,$cuentas_excluidas){
      $cuentas_excluidas = explode(",", $cuentas_excluidas);
      return DB::table('cuentas')
          ->select(
            'cuentas.ID AS id',
            'mail_fake',
            'stk.juego',
            'stk.cuentas_id',
            'cr.usuario',
            DB::raw("(SELECT color FROM usuarios WHERE Nombre = cr.usuario) AS color_user")
            )
            ->join('cuentas_robadas AS cr','cr.cuentas_id','=','cuentas.ID')
            ->leftjoin(DB::raw("
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
            ->where(function ($query) use ($cuentas_excluidas) {
                if (count($cuentas_excluidas) > 0) {
                  $query->whereNotIn('cuentas.ID', $cuentas_excluidas);
                }
              })
            ->orderBy('cuentas.ID','DESC');
    }

    public function ScopeCuentasNotas($query,$obj){
      $query = DB::table('cuentas_notas')->select('cuentas_notas.*', DB::raw("(SELECT color FROM usuarios WHERE Nombre = cuentas_notas.usuario) AS color_user"))->join('cuentas','cuentas.ID','=','cuentas_notas.cuentas_id')
      ->orderBy('cuentas_notas.ID', 'DESC'); 

      if (!empty($obj->column) && !empty($obj->word)) {
        if ($obj->column == 'Notas' || $obj->column == 'usuario') {
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
            ->whereRaw("(Q_Stk < 3 OR Q_Stk IS NULL) AND (ID = $id) ")
            ->orderBy('cuentas.id','DESC');
    }

    // Detalle de cuentas con Reseteo por ID
    public function ScopeResetAccountDetail($query, $id){
      if (empty($id))
        $id = "-1";

      return $query->select(DB::raw("cuentas.*, reset.*, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, (SELECT color FROM usuarios WHERE Nombre = cuentas.usuario) AS color_user"))
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
    public function ScopeAccountAmounts($query, $console, $order = null, $range){
      $query = DB::table(DB::raw("(SELECT cuentas_id as sa_cta_id, SUM(costo_usd) as sa_costo_usd,
                                SUM(costo) as sa_costo
                                FROM saldo GROUP BY cuentas_id) as saldo"))
                ->select(
                  'sa_cta_id AS cuentas_id',
                  'consola',
                  't_cuentas.usuario',
                  't_cuentas.color_user',
                  't_cuentas.mail_fake',
                  DB::raw("(sa_costo_usd - COALESCE(st_costo_usd,0)) libre_usd"),
                  DB::raw("(sa_costo - COALESCE(st_costo,0)) libre_ars"),
                  DB::raw("IFNULL(reseteo.q,0) as reseteos")
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
                  ->leftjoin(DB::raw("(SELECT c.ID AS cuentas_id, c.usuario, c.mail_fake, u.color AS color_user FROM cuentas c LEFT JOIN usuarios u ON c.usuario = u.Nombre) AS t_cuentas"), function($join){
                    $join->on('t_cuentas.cuentas_id','=','saldo.sa_cta_id');
                  })
                  ->leftjoin(DB::raw("(SELECT Count(*) as q, cuentas_id FROM reseteo group by cuentas_id) AS reseteo"), function($join){
                    $join->on('t_cuentas.cuentas_id','=','reseteo.cuentas_id');
                  })
                  ->whereRaw("(sa_costo_usd - COALESCE(st_costo_usd,0)) != 0.00")
                  ->where(function ($query) use ($console, $range) {
                      if (!empty($console)) {
                        $query->where('consola', 'like', '%'.$console.'%');
                      }
                      if ($range->saldoMin != null && $range->saldoMax != null) {
                        $query->whereRaw("(sa_costo_usd - COALESCE(st_costo_usd,0)) BETWEEN {$range->saldoMin} AND {$range->saldoMax}");
                      }
                    });
      
      if ($order === null) {
        $query->orderBy("libre_usd",'DESC');
      } else {
        if ($order == 'monto') {
          $query->orderBy("libre_usd",'DESC');
        } elseif ($order == 'cuenta') {
          $query->orderBy("cuentas_id",'DESC');
        } elseif ($order == 'monto-cuenta') {
          $query->orderBy("libre_usd",'DESC');
          $query->orderBy("cuentas_id",'DESC');
        } elseif ($order == 'cuenta-monto') {
          $query->orderBy("cuentas_id",'DESC');
          $query->orderBy("libre_usd",'DESC');
        }
      }

      return $query;


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
        'stock.cuentas_id',
        'mail_fake',
        'titulo',
        'consola',
        DB::raw('GROUP_CONCAT(stock.ID) as STK'),
        DB::raw('SUM(costo_usd) as Costo'),
        DB::raw('(SUM(costo_usd)/30) as Costo2, DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day)))) as Day'),
        DB::raw('(SQRT(120/(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))))) as Day2'),
        DB::raw('(SUM(costo_usd)/30) + (SQRT(120/(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))))) + IF(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))>360, -1, 0) + IF(DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day))))>500, -2, 0) as FINAL'),
        'cn.Notas'
      )
      ->leftjoin('cuentas','stock.cuentas_id','=','cuentas.ID')
      ->leftjoin(DB::raw("(SELECT cn1.cuentas_id, cn1.Notas FROM cuentas_notas cn1 INNER JOIN (SELECT cuentas_id, MAX(ID) AS ID FROM cuentas_notas GROUP BY cuentas_id) cn2 ON cn1.ID = cn2.ID) AS cn"),'cn.cuentas_id','=','cuentas.ID')
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
      ->whereIn('s.consola',['ps4','ps5'])
      ->orderBy('v.ID','DESC');

      $query2 = DB::table('ventas AS v')
      ->select(
        DB::raw("CONCAT('#',v.clientes_id,' ', CONCAT_WS(' ',c.nombre,c.apellido), ' ', s.titulo,' (',s.consola,')',IF(v.slot='Secundario',CONCAT(' (',v.slot,')'),'')) AS cliente"),
        'v.clientes_id'
      )
      ->join('stock AS s','v.stock_id','=','s.ID')
      ->join('clientes AS c','v.clientes_id','=','c.ID')
      ->where('s.cuentas_id', $account_id)
      ->whereNotIn('s.consola',['ps3','ps4','ps5'])
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

    public function ScopeDominiosByUser($query)
    {
      $usuario = session()->get('usuario')->Nombre;

      return DB::table('dominios AS d')->select(
        'd.ID',
        'd.dominio',
        DB::raw("IFNULL(du.indicador_habilitado, 1) AS indicador_habilitado"),
        DB::raw("IFNULL(du.update_at, d.create_at) AS update_at")
      )
      ->leftjoin(
        DB::raw("(SELECT du.* FROM dominios_usuarios du INNER JOIN dominios d2 ON d2.ID = du.id_dominio WHERE du.usuario = '$usuario') AS du"),
        "d.ID","=","du.id_dominio"
      )
      ->where('d.indicador_habilitado',1);
    }

    public function ScopeCtasResetear($query) {
      return DB::table(DB::raw("(SELECT resetear.ID, resetear.cuentas_id, DATEDIFF(NOW(), MAX(resetear.Day)) as Day_pedido FROM resetear GROUP BY resetear.cuentas_id) as resetear"))
      ->select("resetear.ID","resetear.cuentas_id","Day_pedido","Day_reset")
      ->leftjoin(DB::raw("(SELECT reseteo.cuentas_id, DATEDIFF(NOW(), MAX(reseteo.Day)) as Day_reset FROM reseteo GROUP BY reseteo.cuentas_id) as reseteado"),"resetear.cuentas_id","reseteado.cuentas_id")
      ->whereRaw("Day_reset > Day_pedido")
      ->orWhereRaw("Day_reset IS NULL")
      ->orderBy("Day_reset","DESC");
    }

    public function ScopeCtasVacias($query, $usuario = null) {

      $response = $query
            ->select('cuentas.*', DB::raw("(SELECT color FROM usuarios WHERE Nombre = cuentas.usuario) AS color_user"))
            ->leftjoin("stock","cuentas.ID","=","stock.cuentas_id")
            ->whereNull("stock.cuentas_id")
            ->where('activa',1);

      if ($usuario != null) {
        $response = $query->where("cuentas.usuario", $usuario);
      }
      $response = $query->orderBy("cuentas.ID","DESC");

      return $response;
    }

    public function ScopeGetSaldoLibreSony($query)
    {
        return DB::table(DB::raw("(SELECT t1.* FROM cuentas_balance_sony t1 JOIN (SELECT MAX(ID) as ID, cuentas_id FROM cuentas_balance_sony GROUP BY cuentas_id) t2 ON t1.ID = t2.ID AND t1.cuentas_id = t2.cuentas_id) as r1"))
            ->select('r1.ID','r1.cuentas_id','r1.balance','s1.consolas', 's1.juegos','rt.reseteos','r1.Day','r1.usuario',DB::raw("(SELECT color FROM usuarios WHERE Nombre = r1.usuario) as color_user"))
            ->leftjoin(DB::raw("(SELECT cuentas_id, GROUP_CONCAT(consola) as consolas, GROUP_CONCAT(titulo) as juegos FROM stock GROUP BY cuentas_id) as s1"),"r1.cuentas_id","s1.cuentas_id")
            ->leftjoin(DB::raw("(SELECT cuentas_id, count(*) as reseteos FROM reseteo GROUP BY cuentas_id) as rt"),"r1.cuentas_id","rt.cuentas_id")
            ->orderBy("balance","DESC");
    }


}
