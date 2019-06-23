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

    public function scopeGetData($query)
    {
        $query->select(
            'ventas.ID AS ID_ventas',
            'clientes_id',
            'stock_id',
            'slot',
            'medio_venta',
            'medio_cobro',
            'precio',
            'comision',
            'ventas.Notas AS ventas_Notas',
            'ventas.Day as ventas_Day',
            'apellido',
            'nombre',
            'titulo',
            'consola'
        )->leftJoin(DB::raw("(select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro"),'ventas.ID','=','ventas_cobro.ventas_id')->leftJoin('clientes','ventas.clientes_id','=','clientes.ID')->leftJoin('stock','ventas.stock_id','=','stock.ID');
    }

    public function ScopeSalesByCustomColumn($query,$obj){
      if (!empty($obj->column) && !empty($obj->word)) {
        $query->where("ventas.$obj->column",'like','%'.$obj->word.'%');
      }else{
        return $query;
      }
    }

    public function ScopeSalesCobrosByCustomColumn($query,$obj){
      if (!empty($obj->column) && !empty($obj->word)) {
        $query->where($obj->column,'like','%'.$obj->word.'%');
      }else{
        return $query;
      }
    }

    public function ScopeGetDatosCobros($query)
    {
        $query->select(
            'medio_venta',
            'medio_cobro',
            'ref_cobro',
            'precio',
            'comision',
            'clientes_id',
            'vc.ID AS id_venta_cobro',
            DB::raw("CONCAT_WS(' ', nombre, apellido) AS cliente")
        )
        ->rightjoin('ventas_cobro AS vc','ventas.ID','=','vc.ventas_id')
        ->join('clientes AS c','ventas.clientes_id','=','c.ID')
        ->orderBy('vc.ID','DESC');
    }

    public function ScopeGetDatosControlVentas($query)
    {
        $query->select(
            'ventas.ID AS ID_ventas',
            'clientes_id',
            'stock_id',
            'slot',
            'medio_venta',
            'medio_cobro',
            'precio',
            'comision',
            'ventas.Notas AS ventas_Notas',
            'ventas.Day as ventas_Day',
            'ventas.usuario as ventas_usuario',
            'apellido',
            'nombre',
            'titulo',
            'consola',
            'cuentas_id',
            'costo',
            'q_vta'
        )
        ->leftJoin(DB::raw("(select ventas_id, medio_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro"),'ventas.ID','=','ventas_cobro.ventas_id')
        ->leftJoin('clientes','ventas.clientes_id','=','clientes.ID')
        ->leftJoin(DB::raw("(select ID, titulo, consola, cuentas_id, costo, q_vta FROM stock LEFT JOIN (select count(*) as q_vta, stock_id from ventas group by stock_id) as vendido ON stock.ID = vendido.stock_id) as stock"),'ventas.stock_id','=','stock.ID')
        ->orderBy('ventas.ID','DESC');
    }

}
