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
}
