<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Sale extends Model
{
    protected $table = 'ventas';

    protected $fillable =
    [
      'clientes_id',
      'stock_id',
      'order_item_id',
      'cons',
      'slot',
      'medio_venta',
      'order_id_ml',
      'order_id_web',
      'estado',
      'Day',
      'Notas',
      'usuario',
    ];


    // Ventas por orden id
    public function ScopeSalesFromOrderId($query,$order_id){
      if (!empty($orden_id)) {
        return $query->where('order_item_id',$order_id);
      }else{
        return $query->where('order_item_id','!=',null);
      }
    }


    public function ScopeSalesByCustomerId($query,$id){
      return $query->select(DB::raw('COUNT(*) as Q'))
                    ->where('clientes_id',$id)
                    ->groupBy('clientes_id');
    }

}
