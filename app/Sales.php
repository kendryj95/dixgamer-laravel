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
          if ($obj->column == "titulo") {
              $query->whereRaw("stock.titulo LIKE '%" . str_replace(' ','-',$obj->word) . "%'");
        } else {
            $query->where("ventas.$obj->column",'like','%'.$obj->word.'%');
          } 
          
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

    public function ScopeGetSalesSinEntregar($query, $obj, $ventas_excluidas) {
        $ventas_excluidas = explode(",", $ventas_excluidas);
        $query->select('ventas.ID','clientes_id','ventas.cons',DB::raw("DATE_FORMAT(ventas.Day,'%d/%m/%Y %H:%i:%s') AS Day"))
        ->leftJoin(DB::raw("(SELECT * from mailer group by ventas_id) as mailer"),'ventas.ID','=','mailer.ventas_id')
        ->whereNull('mailer.ID')
        ->whereRaw('((ventas.cons="ps3" or ventas.cons="ps4") or (ventas.cons="ps" and ventas.Day>"2018-01-01"))')
        ->where('clientes_id','>',2)
        ->where('ventas.ID','>',1000);
        
        if (count($ventas_excluidas) > 0) {
            $query->whereNotIn('ventas.ID', $ventas_excluidas);
        }

        if (!empty($obj->column) && !empty($obj->word)) {
            $query->where("ventas.$obj->column",'like','%'.$obj->word.'%');
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
            'costo_usd',
            'q_vta',
            DB::raw("(SELECT IFNULL(color, 'secondary') FROM usuarios WHERE Nombre = ventas.usuario) AS color_user")
        )
        ->leftJoin(DB::raw("(select ventas_id, medio_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro"),'ventas.ID','=','ventas_cobro.ventas_id')
        ->leftJoin('clientes','ventas.clientes_id','=','clientes.ID')
        ->leftJoin(DB::raw("(select ID, titulo, consola, cuentas_id, costo_usd, q_vta FROM stock LEFT JOIN (select count(*) as q_vta, stock_id from ventas group by stock_id) as vendido ON stock.ID = vendido.stock_id) as stock"),'ventas.stock_id','=','stock.ID')
        ->orderBy('ventas.ID','DESC');
    }

    public function totalVentas()
    {
        return DB::table('ventas_cobro')
        ->select(
            DB::raw("SUM(precio) AS Ingresos"),
            DB::raw("SUM(comision) AS Comisiones"),
            DB::raw("COUNT(*) AS Cantidad")
        )
        ->where('precio','>',0);
    }

    public function stockVendido()
    {
        return DB::select("SELECT SUM(costo_usd) as costo FROM (select stock_id from ventas group by stock_id) as vendido LEFT JOIN stock ON vendido.stock_id = stock.ID WHERE costo_usd>=0 and DATEDIFF(NOW(), (stock.Day)) < 270")[0];
		///// 2019-12-05 cambio para mejorar calculo return DB::select("SELECT stock.ID, SUM(costo_usd) as costo FROM (SELECT stock_id FROM `ventas` GROUP BY stock_id) as vendido LEFT JOIN stock ON vendido.stock_id = stock.ID")[0];
    }

    public function datosVentasBalance($tipo = '')
    {
        $query_rsCicloVtaGRAL = "SELECT AVG(diasfromcompra) as diasfromcompra FROM
        (SELECT t1.*, stock.Day, titulo, consola, cuentas_id, TIMESTAMPDIFF(DAY, stock.Day, prom_dia_venta) as diasfromcompra FROM
        (Select ID, from_unixtime(AVG(unix_timestamp(Day))) AS prom_dia_venta, stock_id
        FROM ventas 
        GROUP BY stock_id) as t1
        LEFT JOIN
        stock
        ON t1.stock_id=stock.ID  
        ORDER BY `diasfromcompra` DESC) as t2
        ";

        $condicion = '';

        if ($tipo == 'ciclo_vta') {
            $condicion .= " WHERE diasfromcompra > 0";
        } elseif ($tipo == 'vta_ps4') {
            $condicion .= " WHERE diasfromcompra >= 0 AND consola ='ps4'";
        } elseif ($tipo == 'vta_ps3') {
            $condicion .= " WHERE diasfromcompra >= 0 AND consola ='ps3'";
        } elseif ($tipo == 'vta_ps') {
            $condicion .= " WHERE diasfromcompra >= 0 AND consola ='ps'";
        }

        return DB::select($query_rsCicloVtaGRAL . $condicion)[0];
    }

    public function scopeVentasRecupero($query, $obj) {
        $query = DB::table('ventas_notas AS a')
        ->select('b.clientes_id', 'a.id_ventas', 's.titulo', 's.consola', 'b.slot', 'a.Notas', 'a.Day', 'a.usuario', 'b.recup', DB::raw("(SELECT color FROM usuarios WHERE BINARY Nombre = BINARY a.usuario) AS color_user"))
        ->leftjoin('ventas AS b','a.id_ventas','=','b.ID')
        ->leftjoin('stock AS s','b.stock_id','=','s.ID')
        ->whereRaw("a.ID IN (
            SELECT MAX(ID)
            FROM ventas_notas
            GROUP BY id_ventas
        )")
        ->whereRaw("(a.Notas LIKE 'intento recuperar%' OR b.recup = 2)");

        if (!empty($obj->word) && !empty($obj->column)) {
            if ($obj->column == 'titulo' || $obj->column == 'consola') {
                $query = $query->where("s.$obj->column",'like',str_replace(' ','-',trim($obj->word))."%");
            } elseif ($obj->column == 'clientes_id' || $obj->column == 'slot' || $obj->column == 'recup') {
                $query = $query->where("b.$obj->column",'like',"$obj->word%");
            } elseif ($obj->column == 'id_ventas' || $obj->column == 'Notas' || $obj->column == 'Day' || $obj->column == 'usuario') {
                $query = $query->where("a.$obj->column",'like',"$obj->word%");
            }
        }
        
        return $query->orderBy('a.Day');
    }

    public function ScopeGetDataNotes($query,$obj){
      $query = DB::table('ventas_notas')->select('ventas_notas.*','ventas.clientes_id', DB::raw("(SELECT color FROM usuarios WHERE BINARY Nombre = BINARY ventas_notas.usuario) AS color_user"))->join('ventas','ventas.ID','=','ventas_notas.id_ventas')
      ->orderBy('ventas_notas.ID', 'DESC'); 

      if (!empty($obj->column) && !empty($obj->word)) {
        $query->where("ventas_notas.$obj->column",'like',"%$obj->word%");
      }

      return $query;
    }

}
