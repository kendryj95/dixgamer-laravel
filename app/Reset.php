<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;

class Reset extends Model
{
    protected $table = 'reseteo';

    public function ScopeMaxDayAccountIdReset($query,$id){
      return DB::select(DB::raw("SELECT (SELECT MAX(Day) FROM reseteo WHERE cuentas_id = $id) as Max_Day_Reseteado, (SELECT MAX(Day) FROM resetear WHERE cuentas_id = $id) as Max_Day_Solicitado"));
    }

    public function storeResetAccount($data){
      DB::table('reseteo')->insert($data);
    }


    public function storeRequestResetAccount($data){
      DB::table('resetear')->insert($data);
    }

    public function ScopeGetDatosReseteados($query, $fecha_ini, $fecha_fin, $usuario)
    {
        $sql = $query->select(
          DB::raw('GROUP_CONCAT(cuentas_id) AS cuentas_id'),
          'Day',
          'usuario',
          DB::raw('COUNT(cuentas_id) AS Q')
        )
        ->where(DB::raw('DATE(Day)'),'>=',$fecha_ini)->where(DB::raw('DATE(Day)'),'<=',$fecha_fin)->groupBy(DB::raw('DATE(Day), usuario'));

        if (!empty($usuario)) {
           $sql = $sql->where('usuario','=',$usuario);
        }

        return $sql;
    }

    public function ScopeGetUsersReset($query)
    {
      return $query->select('usuario')->groupBy('usuario');
    }
}
