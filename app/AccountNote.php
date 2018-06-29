<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class AccountNote extends Model
{
  protected $table = "cuentas_notas";

  public function storeNote($data){
    return DB::table('cuentas_notas')->insert($data);
  }
}
