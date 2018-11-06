<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ControlsController extends Controller
{
    public function ventasPerBancos()
    {
        $ventas = DB::table('ventas_cobro')
                    ->select(
                        'ventas.ID AS ID_ventas',
                        'ventas_cobro.ID AS cobro_ID',
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
                        'verificado'
                    )
                    ->leftjoin('ventas','ventas_cobro.ventas_id','=','ventas.ID')
                    ->leftjoin('clientes','ventas.clientes_id','=','clientes.ID')
                    ->leftjoin('stock','ventas.stock_id','=','stock.ID')
                    ->leftjoin('ventas_cobro_bancos AS vcb','ventas_cobro.ID','=','vcb.cobros_id')
                    ->where('medio_cobro','Banco')
                    ->get();

        $tamPag = 50;
        $numReg = count($ventas);
        $paginas = ceil($numReg/$tamPag);
        $limit = "";
        $paginaAct = "";
        if (!isset($_GET['pag'])) {
            $paginaAct = 1;
            $limit = 0;
        } else {
            $paginaAct = $_GET['pag'];
            $limit = ($paginaAct-1) * $tamPag;
        }

        $ventas = $this->consultaPagination($limit, $tamPag);

        return view('control.control_ventas_bancos', compact('ventas', 'paginas', 'paginaAct'));
    }

    private function consultaPagination($inicio,$fin)
    {
        $ventas = DB::table('ventas_cobro')
                    ->select(
                        'ventas.ID AS ID_ventas',
                        'ventas_cobro.ID AS cobro_ID',
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
                        'verificado'
                    )
                    ->leftjoin('ventas','ventas_cobro.ventas_id','=','ventas.ID')
                    ->leftjoin('clientes','ventas.clientes_id','=','clientes.ID')
                    ->leftjoin('stock','ventas.stock_id','=','stock.ID')
                    ->leftjoin('ventas_cobro_bancos AS vcb','ventas_cobro.ID','=','vcb.cobros_id')
                    ->where('medio_cobro','Banco')
                    ->offset($inicio)
                    ->limit($fin)
                    ->get();

        return $ventas;
    }

    public function verificarVentaPerBanco($id)
    {
        DB::beginTransaction();

        try {
            DB::table('ventas_cobro_bancos')->where('cobros_id',$id)->update(['verificado' => 1]);
            DB::commit();

            \Helper::messageFlash('Ventas Cobro','Venta verificada.');

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Vuelva a intentarlo por favor.']);
        }
    }

    ############### MODULO DE CONFIG ######################

    public function adwords()
    {
        $query = "select
                p.ID,
                REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') AS titulo,
                max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
                max( CASE WHEN pm.meta_key = '_price' and p.ID = pm.post_id THEN pm.meta_value END ) as price,
                round(max( CASE WHEN pm.meta_key = '_max_variation_price' and p.ID = pm.post_id THEN pm.meta_value END )) as max_price,
                round(max( CASE WHEN pm.meta_key = '_min_variation_price' and p.ID = pm.post_id THEN pm.meta_value END )) as min_price,
                post_status
            from
                cbgw_posts as p
            LEFT JOIN
                cbgw_postmeta as pm
            ON
               p.ID = pm.post_id
            where
                post_type = 'product' and
                post_status = 'publish'
            group by
                p.ID
            ORDER BY ID DESC, consola ASC, titulo ASC";

        $adwords = DB::select($query);

        return view('adwords.index', compact('adwords'));
    }

    public function cargaGC($carga = null)
    {
        if ($carga != null) {
            $vendedor = $carga;
        } else {

            $vendedor = session()->get('usuario')->Nombre;
        }

        $query_Diario = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
        (SELECT titulo, consola, costo_usd, Day as D, usuario FROM `stock` where usuario='$vendedor' and DATE_FORMAT(Day, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')
        UNION ALL
        SELECT titulo, consola, costo_usd, ex_Day_stock as D, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor' and DATE_FORMAT(ex_Day_stock, '%Y-%m-%d') >= DATE_FORMAT(NOW(), '%Y-%m-%d')) AS resultado
        GROUP BY consola, titulo
        ORDER BY consola, titulo ASC";

        $row_Diario = DB::select($query_Diario);

        $query_Mensual = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
        (SELECT titulo, consola, costo_usd, Day as D, usuario FROM `stock` where usuario='$vendedor' and DATE_FORMAT(Day, '%Y-%m') >= DATE_FORMAT(NOW(), '%Y-%m')
        UNION ALL
        SELECT titulo, consola, costo_usd, ex_Day_stock as D, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor' and DATE_FORMAT(ex_Day_stock, '%Y-%m') >= DATE_FORMAT(NOW(), '%Y-%m')) AS resultado
        GROUP BY consola, titulo
        ORDER BY consola, titulo ASC";

        $row_Mensual = DB::select($query_Mensual);

        $query_Total = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
        (SELECT titulo, consola, costo_usd, Day as D, usuario FROM `stock` where usuario='$vendedor'
        UNION ALL
        SELECT titulo, consola, costo_usd, ex_Day_stock as D, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor') AS resultado
        GROUP BY consola, titulo
        ORDER BY consola, titulo ASC";

        $row_Total = DB::select($query_Total);

        $query_SaldoP = "SELECT Q, (costo_usd - (Q*0.01)) as costo_usd, costo_ars, SUM(usd) as carga_usd, SUM(ars) as carga_ars, saldo_prov.usuario FROM saldo_prov
        LEFT JOIN 
        (SELECT COUNT(*) as Q, SUM(costo_usd) as costo_usd, SUM(costo) as costo_ars, usuario FROM 
        (SELECT costo_usd, costo, usuario FROM `stock` where usuario='$vendedor'
        UNION ALL
        SELECT costo_usd, costo, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor') AS resultado GROUP by usuario) as gastado
        ON saldo_prov.usuario = gastado.usuario
        where saldo_prov.usuario='$vendedor'";

        $row_SaldoP = DB::select($query_SaldoP);

        return view('control.carga_gc', compact('row_Diario','row_Mensual','row_Total', 'row_SaldoP', 'vendedor'));
    }

    public function cargaGC_store(Request $request)
    {
        DB::beginTransaction();
        try {

            $data = [];
            $data['usd'] = $request->carga_usd;
            $data['cotiz'] = $request->carga_cotiz;
            $data['ars'] = $request->carga_ars;
            $data['usuario'] = $request->usuario . "-GC";
            $data['Day'] = date('Y-m-d H:i:s');

            DB::table('saldo_prov')->insert($data);

            DB::commit();

            \Helper::messageFlash('Carga GC','Carga GC agregada a '.$request->usuario);

            return redirect()->back();
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withErrors(['Ha ocurrido algo inesperado, por favor vuelva a intentarlo']);
        }
    }
}
