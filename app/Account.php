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
                              cuentas.ID as ID,
                              mail,
                              mail_fake FROM `stock`
                              LEFT JOIN cuentas
                              ON stock.cuentas_id = cuentas.ID
                              WHERE cuentas_id IS NOT NULL
                              GROUP BY cuentas_id) AS rdo"))
                      ->whereRaw("cons NOT LIKE '%ps3%'
                      and cons <> 'ps'
                      and ID <> '5288'
                      and costo_usd='10.00'
                      and costo_usd='20.00'
                      and costo_usd='30.00'
                      and costo_usd='40.00'
                      and costo_usd='50.00'
                      and costo_usd='60.00'
                      and costo_usd='70.00'
                      and costo_usd='80.00'
                      and costo_usd='90.00'
                      and costo_usd='100.00'
                      and costo_usd='110.00'
                      and costo_usd='120.00'
                      and costo_usd='130.00'
                      and costo_usd='140.00'")
                      ->orderBy('costo_usd','DESC');
    }

    // cuentas para juegos PS3
    public function ScopeAccountGamesPs4($query){
        return DB::table(DB::raw("
                      (SELECT COUNT(*) as Q, stock.ID as ID_stk,
                              costo_usd,
                              GROUP_CONCAT(consola) as cons,
                              cuentas_id,
                              cuentas.ID as ID,
                              mail,
                              mail_fake FROM `stock`
                              LEFT JOIN cuentas
                              ON stock.cuentas_id = cuentas.ID
                              WHERE cuentas_id IS NOT NULL
                              GROUP BY cuentas_id) AS rdo"))
            ->whereRaw("cons NOT LIKE '%ps4%'
                      and cons <> 'ps'
                      and ID <> '5288'
                      and costo_usd='10.00'
                      and costo_usd='20.00'
                      and costo_usd='30.00'
                      and costo_usd='40.00'
                      and costo_usd='50.00'
                      and costo_usd='60.00'
                      and costo_usd='70.00'
                      and costo_usd='80.00'
                      and costo_usd='90.00'
                      and costo_usd='100.00'
                      and costo_usd='110.00'
                      and costo_usd='120.00'
                      and costo_usd='130.00'
                      and costo_usd='140.00'")
            ->orderBy('costo_usd','DESC');
    }


    public function createAccount($arrayAcount){
      return DB::table('cuentas')->insert($arrayAcount);
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


}
