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
          'usuario_real',
          DB::raw('COUNT(cuentas_id) AS Q'),
          DB::raw("(SELECT color FROM usuarios WHERE Nombre = usuario_real) AS color_user")
        )
        ->where(DB::raw('DATE(Day)'),'>=',$fecha_ini)->where(DB::raw('DATE(Day)'),'<=',$fecha_fin)->groupBy(DB::raw('DATE(Day), usuario_real'));

        if (!empty($usuario)) {
           $sql = $sql->where('usuario_real','=',$usuario);
        }

        return $sql;
    }

    public function ScopeGetUsersReset($query)
    {
      return $query->select('usuario_real AS usuario')->groupBy('usuario_real');
    }
}
