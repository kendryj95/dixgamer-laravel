<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sales extends Model
{
    //

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

    public function scopeFirstSection($query){
        $colname_rsCON = "-1";
        if (isset($_GET['consola'])) {
            $colname_rsCON = (get_magic_quotes_gpc()) ? $_GET['consola'] : addslashes($_GET['consola']);}

        $colname_rsTIT = "-1";
        if (isset($_GET['titulo'])) {
            $colname_rsTIT = (get_magic_quotes_gpc()) ? $_GET['titulo'] : addslashes($_GET['titulo']);}

        $colname_rsSlot = "-1";
        if (isset($_GET['slot'])) {
            $colname_rsSlot = (get_magic_quotes_gpc()) ? $_GET['slot'] : addslashes($_GET['slot']);}
    }
}
