<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Customer extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
      'ID',
      'apellido',
      'nombre',
      'pais',
      'provincia',
      'ciudad',
      'cp',
      'direc',
      'carac',
      'tel',
      'cel',
      'email',
      'ml_user',
      'face',
      'auto',
      'Level',
      'usuario',
    ];

    // Creamos un scope para hacer un filtro por correo, si hay un parametro y no esta vacio lo buscara
    // Si no, solo retorna los datos que se mandan en $query
    public function ScopeCustomerByEmail($query,$email){
      if (!empty($email)) {
        $query->leftjoin('clientes_email AS ce','ce.clientes_id','=','clientes.clientes_id')->where('email','like','%'.$email.'%')->orWhere('ce.email','like','%'.$email.'%');
      }else{
        return $query;
      }
    }

    // No lo hacemos por like
    public function ScopeCustomerEmail($query,$email){
      if (!empty($email)) {
        $query
        ->leftjoin('clientes_email','clientes.id','=','clientes_email.clientes_id')
        ->where('clientes_email.email',$email);
      }else{
        return $query;
      }
    }

    // No lo hacemos por like
    public function ScopeCustomerId($query,$id){
      if (!empty($id)) {
        $query->where('ID',$id);
      }else{
        return $query;
      }
    }

    public function ScopeCustomerNotesByCustomerId($query,$id){
      return DB::table('clientes_notas')
                  ->select('*')
                  ->where('clientes_id',$id)
                  ->orderBy('ID','DESC');
    }


    public function ScopeDataCustomerId($query,$id){
      return DB::table('stock')
                ->select(
                  'ID AS ID_stock',
                  'ID_cobro',
                  'titulo',
                  'consola',
                  'costo',
                  'code',
                  'code_prov',
                  'n_order',
                  'Notas AS stock_Notas',
                  'stock.cuentas_id',
                  'q_reset',
                  'max_Day',
                  'client.*'
                  )
                ->leftjoin(DB::raw("
                  (SELECT cuentas_id, COUNT(*) AS q_reset, MAX(Day) as max_Day FROM reseteo GROUP BY cuentas_id) AS ctas_reseteadas
                "),'stock.cuentas_id','=','ctas_reseteadas.cuentas_id')
                ->rightjoin(
                  DB::raw("
                    (SELECT ventas.ID AS ID_ventas, ID_cobro, clientes_id, stock_id, slot, medio_venta, order_item_id, order_id_web, order_id_ml, medio_cobro, ref_cobro, precio, comision, estado, ventas.Notas AS ventas_Notas, ventas.Day, ventas.usuario as usuario, clientes.ID AS ID_clientes, apellido, nombre, email, mail.*
                    FROM ventas
                    LEFT JOIN (select GROUP_CONCAT(ID SEPARATOR ',') as ID_cobro, ventas_id, medio_cobro, GROUP_CONCAT(ref_cobro SEPARATOR ',') as ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
                    LEFT JOIN
                    (SELECT ventas_id, COUNT(case when concepto = 'datos1' then 1 else null end) AS datos1, COUNT(case when concepto = 'evitar_candado' then 1 else null end) AS evitar_candado FROM mailer GROUP BY ventas_id) AS mail
                    ON ventas.ID = mail.ventas_id
                    LEFT JOIN
                    clientes
                    ON ventas.clientes_id = clientes.ID) AS client
                  "),'stock.ID','client.stock_id')
                ->where('clientes_id',$id)
                ->orderBy('client.Day','DESC');
    }


    public function ScopeSalesLowByCustomerId($query,$id){
      return DB::table('stock')
                ->select(
                  'ID AS ID_stock',
                  'titulo',
                  'consola',
                  'costo',
                  'Notas AS stock_Notas',
                  'cuentas_id',
                  'client.*'
                  )
                ->rightjoin(
                  DB::raw("
                    (SELECT ventas_baja.ventas_id AS ID_ventas, clientes_id, stock_id, slot, medio_venta, medio_cobro, precio, comision, ventas_baja.Notas AS ventas_Notas, ventas_baja.Day, ventas_baja.Notas_baja AS Notas_baja, Day_baja, clientes.ID AS ID_clientes, apellido, nombre, email, mail.*
                    FROM ventas_baja
                    LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas_baja.ventas_id = ventas_cobro.ventas_id
                    LEFT JOIN
                    (SELECT ventas_id, COUNT(case when concepto = 'datos1' then 1 else null end) AS datos1, COUNT(case when concepto = 'evitar_candado' then 1 else null end) AS evitar_candado FROM mailer GROUP BY ventas_id) AS mail
                    ON ventas_baja.ventas_id = mail.ventas_id
                    LEFT JOIN
                    clientes
                    ON ventas_baja.clientes_id = clientes.ID) AS client
                  "),'stock.ID','client.stock_id')
                ->where('clientes_id',$id)
                ->orderBy('client.Day','DESC');
    }


    // Buscamos cliente por filtro
    public function ScopeCustomerByCustomColumn($query,$obj){
      if (!empty($obj->column) && !empty($obj->word)) {
        if ($obj->column == 'email') {
          $query->select('clientes.*')->leftjoin('clientes_email AS ce','ce.clientes_id','=','clientes.ID')->where('clientes.email','like','%'.$obj->word.'%')->orWhere('ce.email','like','%'.$obj->word.'%')->groupBy('clientes.ID');
        } else {

          $query->where($obj->column,'like','%'.$obj->word.'%');
        }

        if (!(\Helper::validateAdministrator(session()->get('usuario')->Level))) { 
          $query->where('clientes.ID','!=',371);
        }
      }else{
        if (!(\Helper::validateAdministrator(session()->get('usuario')->Level))) { 
          $query->where('clientes.ID','!=',371);
        } else {

          return $query;
        }
      }
    }

    // No lo hacemos por like
    public function ScopeCustomerCustomColumn($query,$obj){
      if (!empty($obj->column) && !empty($obj->word)) {
        $query->where($obj->column,$obj->word);
      }else{
        return $query;
      }
    }

    // Almacenando clientes
    public function storeCustomer($customer){
      return DB::table('clientes')->insertGetId($customer);
    }

    public function scopeInfoCustomerVentas($query, $id)
    {
      return DB::table('ventas')
              ->select(
                'ventas.ID',
                'clientes_id',
                'stock_id',
                'slot',
                'order_item_id',
                'order_id_web',
                'order_id_ml',
                'medio_venta',
                'Notas',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.email',
                'ventas.Day'
              )
              ->leftjoin('clientes', 'ventas.clientes_id', '=', 'clientes.ID')
              ->where('ventas.ID', $id)
              ->first();
    }

    public function scopeInfoCustomerVentasProductos($query, $id)
    {
      return DB::table('ventas')
              ->select(
                'ventas.ID',
                'clientes_id',
                'stock_id',
                'order_item_id',
                'cons',
                'slot',
                'medio_venta',
                'ventas.Notas',
                'clientes.nombre',
                'clientes.apellido',
                'clientes.email',
                'titulo',
                'consola'
              )
              ->leftjoin('clientes', 'ventas.clientes_id', '=', 'clientes.ID')
              ->leftjoin('stock', 'ventas.stock_id', '=', 'stock.ID')
              ->where('ventas.ID', $id)
              ->first();
    }
}
