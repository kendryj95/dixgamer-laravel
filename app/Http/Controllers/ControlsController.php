<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Zipper;
use App\Sales;
use App\Expenses;
use App\Stock;
use App\Balance;
use App\WpPost;

class ControlsController extends Controller
{
    private $expenses;
    private $sales;
    private $stock;
    private $balance;

    public function __construct()
    {
        $this->expenses = new Expenses();
        $this->sales = new Sales();
        $this->stock = new Stock();
        $this->balance = new Balance();
        $this->wp_p = new WpPost();
    }

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

    public function cargaGC(Request $request, $carga = null)
    {
        $conFiltro = "No";
        if ($carga != null) {
            $vendedor = $carga;
            $conFiltro = "Si";
        } else {

            $vendedor = session()->get('usuario')->Nombre . '-GC';
        }

        $condicion1 = '';

        $fecha_fin = isset($request->fecha_fin) ? $request->fecha_fin : date('Y-m-d');
        $fecha_ini = isset($request->fecha_ini) ? $request->fecha_ini : $this->defaultFechaIni();

        if (isset($request->fecha_ini) && isset($request->fecha_fin)) {
            $condicion1 .= " and DATE(Day) between '$request->fecha_ini' and '$request->fecha_fin'";
        }

        /*$query_Diario = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
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

        $row_Mensual = DB::select($query_Mensual);*/

        $query_Total = "SELECT COUNT(*) as Q, titulo, consola, round(AVG(costo_usd),0) as costo_usd, usuario FROM 
        (SELECT titulo, consola, costo_usd, Day as D, usuario FROM `stock` where usuario='$vendedor' $condicion1
        UNION ALL
        SELECT titulo, consola, costo_usd, ex_Day_stock as D, ex_usuario as usuario FROM `saldo` where ex_usuario='$vendedor' $condicion1) AS resultado
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

        $query_saldo_prov = "SELECT * FROM saldo_prov where usuario = '$vendedor' $condicion1 ORDER BY 1 DESC";

        $row_saldo_prov = DB::select($query_saldo_prov);

        $users = $this->usersGC();

        return view('control.carga_gc', compact('fecha_ini','fecha_fin','row_Total', 'row_SaldoP', 'vendedor','users','conFiltro','row_saldo_prov'));
    }

    public function getDatosSaldoProv($id)
    {
        $datos = DB::table('saldo_prov')->where('ID',$id)->first();

        return view('control.edit_saldo_prov',compact('datos'));
    }

    public function editSaldoProv(Request $request)
    {
        $id = $request->ID;
        $hora = date('H:i:s', strtotime($request->fecha_anterior));
        $data = [];
        $data['usd'] = $request->usd;
        $data['cotiz'] = $request->cotiz;
        $data['ars'] = $request->ars;
        $data['Day'] = $request->Day;

        DB::table('saldo_prov')->where('ID',$id)->update($data);

        \Helper::messageFlash('Carga GC','Saldo prov editado correctamente.');

        return redirect()->back();
    }

    private function defaultFechaIni()
    {
      $hoy = strtotime(date('Y-m-d'));
      $fecha_ini = strtotime('-7 days', $hoy);
      $fecha_ini = date('Y-m-d', $fecha_ini);

      return $fecha_ini;
    }

    private function usersGC()
    {
        $users = DB::table('saldo')->select('ex_usuario')->where('ex_usuario','LIKE',"%-GC")->groupBy('ex_usuario')->get();

        return $users;
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

    public function controlMP($version2 = null)
    {
        $conceptos = $this->getConceptos();

        $cobros_mp_pareja = $this->getCobrosMPPareja($version2);

        $cobros_bd_pareja = $this->getCobrosBDPareja($version2);

        $cobros_bd_sin_pareja = $this->getCobrosBDSinPareja($version2);

        $cobros_mp_sin_pareja = $this->getCobrosMPSinPareja($version2);

        return view('control.control_mp', compact('conceptos','cobros_mp_pareja','cobros_bd_pareja','cobros_bd_sin_pareja','cobros_mp_sin_pareja'));

    }

    public function controlMPBaja(Request $request)
    {
        $status = 0;
        if (isset($request->nro_mov)) {
            DB::beginTransaction();

            try {
                $insert = "INSERT INTO mercadopago_baja (nro_mov, concepto, ref_op, importe, saldo) SELECT nro_mov, concat('Anulación de ',concepto), ref_op, (importe * -1), saldo FROM mercadopago WHERE nro_mov=?";

                DB::insert($insert, [$request->nro_mov]);

                DB::commit();

                // \Helper::messageFlash('Cobros MP','Anulación aplicada correctamente.');

                // return redirect()->back();
                $status = 1;
            } catch (Exception $e) {
                DB::rollback();
                // return redirect()->back()->withErrors(['Ha ocurrido un error en el proceso. Por favor vuelva a intentarlo']);
                $status = 2;
            }

            echo json_encode([
                "status" => $status
            ]);
        }
    }

    public function controlMPBajaEnvio(Request $request)
    {
        if (isset($request->dif) && isset($request->ref_cobro)) {
            DB::beginTransaction();

            try {
                $insert = "INSERT INTO mercadopago_baja (concepto, ref_op, importe, saldo) SELECT 'Anulación de Ingreso por envío', ref_op, (? * -1), saldo FROM mercadopago WHERE ref_op=? LIMIT 1";

                DB::insert($insert, [$request->dif, $request->ref_cobro]);

                DB::commit();

                \Helper::messageFlash('Cobros MP','Anulación de Ingreso por envío aplicada correctamente.');

                return redirect()->back();
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withErrors(['Ha ocurrido un error en el proceso. Por favor vuelva a intentarlo']);
            }
        }
    }

    public function controlMPCrearVentaCero(Request $request)
    {
        $ref_cobro = isset($request->ref_cobro) ? $request->ref_cobro : false;
        $importe = isset($request->importe) ? $request->importe : false;
        $colname_rsCliente = isset($request->c_id) ? $request->c_id : false;

        $date = date('Y-m-d H:i:s');

        if ($ref_cobro && $importe && $colname_rsCliente) {
            DB::beginTransaction();

            try {
                $insertSQL = "INSERT INTO ventas (clientes_id, stock_id, cons, slot, medio_venta, estado, Day, usuario, Notas) VALUES ('$colname_rsCliente', '0', 'ps', 'No', 'Mail', 'listo', '$date', '$vendedor', 'Creado por control para reflejar realidad de cobro')";

                DB::insert($insertSQL);

                $ventaid = DB::getPdo()->lastInsertId();

                $importeTotal = ($importe / 0.9446);
                $comision = ($importeTotal * 0.0538);
                
                $insertSQL222 = "INSERT INTO ventas_cobro (ventas_id, medio_cobro, ref_cobro, precio, comision, Day, usuario) VALUES ('$ventaid', 'MercadoPago', '$ref_cobro', '$importeTotal', '$comision', '$date', '$vendedor')";

                DB::insert($insertSQL222);

                DB::commit();

                \Helper::messageFlash('Cobros MP','Proceso aplicado correctamente.');

                return redirect('clientes/'.$colname_rsCliente);
            } catch (Exception $e) {
                DB::rollback();
                return redirect()->back()->withErrors(['Ha ocurrido un error en el proceso. Por favor vuelva a intentarlo']);
            }
        }

    }

    public function controlMPActualizarImportes(Request $request)
    {
        $ref_op = isset($request->ref_op) ? $request->ref_op : false;

        if ($ref_op) {
            $cobro = DB::table('mercadopago')->where('ref_op', $ref_op)->where('importe','>',0)->first();

            $comis = DB::table('mercadopago')->where('ref_op', $ref_op)->where('importe','<',0.00)->whereIn('concepto', ['Cargo Mercado Pago','Costo de Mercado Pago','Comisión por venta de Mercado Libre'])->first();

            $actual = DB::table('ventas_cobro')->where('ref_cobro', $ref_op)->first();

            $actual_cobro = $actual->precio;
            $actual_comision = $actual->comision;

            $cobrado = $cobro->importe;
            $comision = (-1.00 * $comis->importe);

            DB::update("UPDATE ventas_cobro SET precio=?, comision=? WHERE ref_cobro=?", [$cobrado, $comision, $ref_op]);

            $mensaje = '';
            if (($cobrado - $actual_cobro) != 0) {
                $mensaje .= 'Importe de ' . $actual_cobro . ' a ' . $cobrado;
            }
            if (($comision - $actual_comision) != 0) {
                if ($mensaje != '') {
                    $mensaje .= ' y comision de ' . $actual_comision . ' a ' . $comision;
                } else {
                    $mensaje .= 'Comision de ' . $actual_comision . ' a ' . $comision;
                }
            }

            \Helper::messageFlash('Cobros MP',$mensaje);

            return redirect()->back();
        }
    }

    public function controlMPBajaSlotLibre(Request $request)
    {
        if (isset($request->dif) && isset($request->ref_cobro)) {
            $insert = "INSERT INTO mercadopago_baja (concepto, ref_op, importe, saldo) SELECT 'Regala plata - No descargó', ref_op, (? * -1), saldo FROM mercadopago WHERE ref_op=? LIMIT 1";

            DB::insert($insert, [$request->dif, $request->ref_cobro]);

            \Helper::messageFlash('Cobros MP','Proceso aplicado correctamente.');

            return redirect('/');
        }
    }

    private function getConceptos()
    {
        $words = array("Percepción RG 4240 IVA Servicios Digitales Internacionales",
                                    "Anulación de percepción RG 4240 IVA Servicios Digitales Internacionales",
        "Anulación de comisión por venta de MercadoLibre",
        "Anulación de cargo MercadoPago",
                       "Anulación de cargo Mercado Pago",
        "Anulación de costo de envío por MercadoEnvíos",
                       "Anulación de cargo por envío",
        "Anulación de costo de MercadoPago",
                       "Anulación de costo de Mercado Pago",
                       "Anulación de comisión por venta de Mercado Libre",
        "Anulación de dinero retenido por contracargo",
        "Anulación de retiro de dinero a cuenta bancaria",
                                    "Anulación de devolución por Compra Protegida",
                                    "Anulación de retención de ingresos brutos de Entre Ríos",
                                    "Anulación de retención de ingresos brutos de Santa Fe",
                                    "Anulación parcial de retención de ingresos brutos de Santa Fe",
                                    "Anulación de retención de ingresos brutos de Santiago del Estero",
                                    "Anulación parcial de retención de ingresos brutos de Córdoba",
        "Anulación de retención de ingresos brutos de Córdoba",
                       "Anulación de retención de ingresos brutos de Catamarca",
        "Anulación parcial de costo de MercadoPago",
                       "Anulación parcial de costo de Mercado Pago",
        "Cargo MercadoPago",
                       "Cargo Mercado Pago",
                       "Cargo por envío",
        "Cobro",
        "Cobro Adicional",
        "Cobro por descuento a tu contraparte",
        "Comisión por venta de MercadoLibre",
                       "Comisión por venta de Mercado Libre",
        "Costo de envío por MercadoEnvíos",
        "Costo de MercadoPago",
                       "Costo de Mercado Pago",
        "Devolución de cobro",
        "Devolución de cobro Adicional",
        "Devolución de cobro por descuento a tu contraparte",
        "Devolución de dinero recibido",
        "Devolución parcial de cobro",
                       "Devolución parcial de ingreso de dinero",
                       "Devolución parcial de pago",
                       "Devolución de pago",
        "Devolución por Compra Protegida",
        "Dinero recibido",
        "Dinero retenido por contracargo",
                       "Dinero retenido",
                       "Ingreso de dinero",
                       "Movimiento General",
                                                "Anulación de movimiento General",
                                                "Descuento recibido",
                                                "Pago con descuento recibido",
        "Pago",
        "Pago adicional",
        "Percepción Ing. Brutos CAP. FED.",
        "Percepción Ing. Brutos Pcia. Bs. As.",
        "Recarga de celular",
                                    "Retención de ingresos brutos de Mendoza",
                                    "Retención de ingresos brutos de Neuquén",
                                    "Retención de ingresos brutos de Santiago del Estero",
                                    "Retención de ingresos brutos de Tucumán",
                                    "Retención de Ingresos brutos de Río Negro",
                                    "Retención de Ingresos brutos de Jujuy",
        "Retención de ingresos brutos de Catamarca",
        "Retención de ingresos brutos de Entre Ríos",
        "Retención de ingresos brutos de La Pampa",
        "Retención de ingresos brutos de Santa Fe",
        "Retención de ingresos brutos de Córdoba",
        "Retiro de dinero a cuenta bancaria" );

        $whereClause = "";
        foreach( $words as $word) {
           $whereClause .= " concepto != '" . $word . "' AND";
        }

        // Remove last 'and'
        $whereClause = substr($whereClause, 0, -3);
        /**** query para descubrir si hay nuevo concepto de operación en mercadopago cargado a la base de datos y por ende no tenido en cuenta en los querys al momento de incorporarlo */
        
        $query_rsNewConcept = "SELECT concepto FROM mercadopago WHERE $whereClause GROUP BY concepto";

        $conceptos = DB::select($query_rsNewConcept);

        return $conceptos;

    }

    private function getCobrosMPPareja($version2 = null)
    {
        /*** SI AGREGO CONCEPTO QUE NO ES VENTA O COBRO (serían percepciones, retiros, pagos de mis compras, etc) AGREGARLO TMB EN "WHERE NOT LIKE" variable $wherenotlike   */

        $condicion = '';

        if ($version2 == null) {
             $condicion .= "AND ref_op<=4554354344 # 2019-03-26: Filtro los cobros hasta el último que entró en mi primer cuenta de MP, luego de esto continuamos cobrando con la cuenta de MP de Mariana";
         } else {
            $condicion .= "AND ref_op>=4629477059";
         } 

        $wherenotlike = "concepto NOT LIKE '%Percepción Ing. Brutos%' 
        AND concepto NOT LIKE '%Retención de ingresos brutos de%' 
        AND concepto NOT LIKE '%Anulación de retención de ingresos brutos de%' 
        AND concepto NOT LIKE '%Anulación parcial de retención de ingresos brutos de%' 
                                    AND concepto != 'Devolución de pago'
                                    AND concepto != 'Descuento recibido'
        AND concepto != 'Recarga de celular' 
        AND concepto != 'Pago' 
        AND concepto != 'Pago adicional' 
        AND concepto != 'Retiro de dinero a cuenta bancaria' 
        AND concepto != 'Anulación de retiro de dinero a cuenta bancaria' 
        AND concepto != 'Percepción RG 4240 IVA Servicios Digitales Internacionales'
                                    AND concepto != 'Anulación de percepción RG 4240 IVA Servicios Digitales Internacionales'"
        ;

        $query_rsGRAL = "SELECT mp.*, cobro.*, (imp_mp - imp_db) as dif # SACO LA DIFERENCIA ENTRE MP Y LA DB
        FROM 
            (SELECT ref_op, GROUP_CONCAT(nro_mov SEPARATOR ', ') as nro_mov, # agrupo los mov de una misma operacion
                 GROUP_CONCAT(concepto SEPARATOR ', ') AS concepto, # y agrupo los conceptos de esos movimientos
                 SUM(importe) AS imp_mp # sumo el total final (saldo) de esa operacion
                 FROM (SELECT * FROM mercadopago UNION ALL SELECT ID,nro_mov,concepto,ref_op,importe,saldo FROM mercadopago_baja) as mercadopago
                     
                 WHERE " . $wherenotlike . " # quito los movimientos que no tienen que ver con ventas y cobros (serian pagos, retiros y reten o percep)
                 $condicion 
                 GROUP BY ref_op # los agrupo por operacion
              ) as mp
        LEFT JOIN
                (SELECT ref_cobro, 
                IFNULL(SUM(ventas_cobro.precio - ventas_cobro.comision), 0) as imp_db, # si no hay cobro con esa referencia le coloco valor 0
                GROUP_CONCAT(ventas_id SEPARATOR ',') AS ventas_id, # agrupo todos las ventas que tienen esa ref de cobro
                clientes_id
                FROM ventas_cobro
                    LEFT JOIN (SELECT ventas.ID as ID, clientes_id FROM ventas UNION ALL SELECT ventas_baja.ventas_id as ID, clientes_id FROM ventas_baja ) as vtas
                    ON ventas_cobro.ventas_id = vtas.ID
                    GROUP BY ref_cobro
                ) as cobro
        ON mp.ref_op = cobro.ref_cobro # No necesito mas esto -> COLLATE utf8_spanish_ci uno la tabla de mercadopago a la table de cobros";
        $query_rsCXP = $query_rsGRAL;
        $query_rsCXP .= "
        WHERE ((imp_mp >= (imp_db + 1.5)) OR (imp_mp<= (imp_db - 1.5))) # filtro las que tengan diferencia entre importes > a 50 centavos
        ORDER BY `dif` ASC";

        $cobros_mp_pareja = DB::select($query_rsCXP);

        return $cobros_mp_pareja;
    }

    private function getCobrosBDPareja($version2 = null)
    {

        $condicion = '';

        if ($version2 == null) {
            $condicion = "AND ref_cobro<=4554354344 # 2019-03-26: Filtro los cobros hasta el último que entró en mi primer cuenta de MP, luego de esto continuamos cobrando con la cuenta de MP de Mariana";
        } else {
            $condicion = "AND ref_cobro>=4629477059";
        }

        $query_rsGRAL2 = "SELECT db.*, mp.*, (imp_mp - imp_db) as dif
        FROM 
        (SELECT ventas_cobro.Day,
                ref_cobro, 
                IFNULL(SUM(ventas_cobro.precio - ventas_cobro.comision), 0) as imp_db, # si no hay cobro con esa referencia le coloco valor 0
                GROUP_CONCAT(ventas_id SEPARATOR ',') AS ventas_id, # agrupo todos los ID de ventas que tienen esa ref de cobro
                ventas_cobro.usuario,
                clientes_id
            FROM ventas_cobro
                LEFT JOIN 
                (SELECT ventas.ID as ID, clientes_id FROM ventas UNION ALL SELECT ventas_baja.ventas_id as ID, clientes_id FROM ventas_baja ) as vtas
            ON ventas_cobro.ventas_id = vtas.ID 
            WHERE ventas_cobro.Day > '2017-04-01' AND medio_cobro LIKE '%MP%' $condicion
            GROUP BY ref_cobro) as db
        LEFT JOIN
            (SELECT ref_op, GROUP_CONCAT(nro_mov SEPARATOR ', ') as nro_mov,
            GROUP_CONCAT(concepto SEPARATOR ', ') AS concepto,
            SUM(importe) AS imp_mp
            FROM (SELECT * FROM mercadopago UNION ALL SELECT ID,nro_mov,concepto,ref_op,importe,saldo FROM mercadopago_baja) as mercadopago
            WHERE concepto NOT LIKE '%Percepción Ing. Brutos%' AND concepto NOT LIKE '%Retención de ingresos brutos de%' AND concepto != 'Recarga de celular' AND concepto != 'Pago' AND concepto != 'Pago adicional' AND concepto != 'Anulación de retención de ingresos brutos de Córdoba' AND concepto != 'Retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retención de ingresos brutos de Catamarca' GROUP BY ref_op) as mp
        ON db.ref_cobro = mp.ref_op # No necesito mas esto -> COLLATE utf8_spanish_ci";
        $query_rsCobrosDB = $query_rsGRAL2;
        $query_rsCobrosDB .= "
        WHERE ((imp_mp >= (imp_db + 1.5)) OR (imp_mp<= (imp_db - 1.5))) # filtro las que tengan diferencia entre importes > a 50 centavos
        ORDER BY `dif` ASC";

        $cobros_bd_pareja = DB::select($query_rsCobrosDB);

        return $cobros_bd_pareja;
    }

    private function getCobrosBDSinPareja($version2 = null)
    {


        $condicion = '';

        if ($version2 == null) {
            $condicion = "AND ref_cobro<=4554354344 # 2019-03-26: Filtro los cobros hasta el último que entró en mi primer cuenta de MP, luego de esto continuamos cobrando con la cuenta de MP de Mariana";
        } else {
            $condicion = "AND ref_cobro>=4629477059";
        }

        $query_rsGRAL2 = "SELECT db.*, mp.*, (imp_mp - imp_db) as dif
        FROM 
        (SELECT ventas_cobro.Day,
                ref_cobro, 
                IFNULL(SUM(ventas_cobro.precio - ventas_cobro.comision), 0) as imp_db, # si no hay cobro con esa referencia le coloco valor 0
                GROUP_CONCAT(ventas_id SEPARATOR ',') AS ventas_id, # agrupo todos los ID de ventas que tienen esa ref de cobro
                ventas_cobro.usuario,
                clientes_id
            FROM ventas_cobro
                LEFT JOIN 
                (SELECT ventas.ID as ID, clientes_id FROM ventas UNION ALL SELECT ventas_baja.ventas_id as ID, clientes_id FROM ventas_baja ) as vtas
            ON ventas_cobro.ventas_id = vtas.ID 
            WHERE ventas_cobro.Day > '2017-04-01' AND medio_cobro LIKE '%MP%' AND ref_cobro<=4554354344 # 2019-03-26: Filtro los cobros hasta el último que entró en mi primer cuenta de MP, luego de esto continuamos cobrando con la cuenta de MP de Mariana
            GROUP BY ref_cobro) as db
        LEFT JOIN
            (SELECT ref_op, GROUP_CONCAT(nro_mov SEPARATOR ', ') as nro_mov,
            GROUP_CONCAT(concepto SEPARATOR ', ') AS concepto,
            SUM(importe) AS imp_mp
            FROM (SELECT * FROM mercadopago UNION ALL SELECT ID,nro_mov,concepto,ref_op,importe,saldo FROM mercadopago_baja) as mercadopago
            WHERE concepto NOT LIKE '%Percepción Ing. Brutos%' AND concepto NOT LIKE '%Retención de ingresos brutos de%' AND concepto != 'Recarga de celular' AND concepto != 'Pago' AND concepto != 'Pago adicional' AND concepto != 'Anulación de retención de ingresos brutos de Córdoba' AND concepto != 'Retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retiro de dinero a cuenta bancaria' AND concepto != 'Anulación de retención de ingresos brutos de Catamarca' GROUP BY ref_op) as mp
        ON db.ref_cobro = mp.ref_op # No necesito mas esto -> COLLATE utf8_spanish_ci";

        $query_rsCobrosDB2 = $query_rsGRAL2; /*** es la misma tabla QUE ARRIBA pero le hago distinto filtro */
        $query_rsCobrosDB2 .= "
        WHERE ref_op IS NULL and imp_db != '0.00' # filtro las op. de DB que no tienen pareja en MP y que tienen importe distinto a 0
        ORDER BY imp_db DESC";

        $cobros_bd_sin_pareja = DB::select($query_rsCobrosDB2);

        return $cobros_bd_sin_pareja;
    }

    private function getCobrosMPSinPareja($version2 = null)
    {

        /*** SI AGREGO CONCEPTO QUE NO ES VENTA O COBRO (serían percepciones, retiros, pagos de mis compras, etc) AGREGARLO TMB EN "WHERE NOT LIKE" variable $wherenotlike   */ 

        $condicion = '';

        if ($version2 == null) {
             $condicion .= "AND ref_op<=4554354344 # 2019-03-26: Filtro los cobros hasta el último que entró en mi primer cuenta de MP, luego de esto continuamos cobrando con la cuenta de MP de Mariana";
         } else {
            $condicion .= "AND ref_op>=4629477059";
         }

        $wherenotlike = "concepto NOT LIKE '%Percepción Ing. Brutos%' 
        AND concepto NOT LIKE '%Retención de ingresos brutos de%' 
        AND concepto NOT LIKE '%Anulación de retención de ingresos brutos de%' 
        AND concepto NOT LIKE '%Anulación parcial de retención de ingresos brutos de%' 
        AND concepto != 'Recarga de celular' 
        AND concepto != 'Pago' 
        AND concepto != 'Pago adicional' 
        AND concepto != 'Retiro de dinero a cuenta bancaria' 
        AND concepto != 'Anulación de retiro de dinero a cuenta bancaria' 
        AND concepto !='Percepción RG 4240 IVA Servicios Digitales Internacionales'";

        $query_rsGRAL = "SELECT mp.*, cobro.*, (imp_mp - imp_db) as dif # SACO LA DIFERENCIA ENTRE MP Y LA DB
        FROM 
            (SELECT ref_op, GROUP_CONCAT(nro_mov SEPARATOR ', ') as nro_mov, # agrupo los mov de una misma operacion
                 GROUP_CONCAT(concepto SEPARATOR ', ') AS concepto, # y agrupo los conceptos de esos movimientos
                 SUM(importe) AS imp_mp # sumo el total final (saldo) de esa operacion
                 FROM (SELECT * FROM mercadopago UNION ALL SELECT ID,nro_mov,concepto,ref_op,importe,saldo FROM mercadopago_baja) as mercadopago
                     
                 WHERE " . $wherenotlike . " # quito los movimientos que no tienen que ver con ventas y cobros (serian pagos, retiros y reten o percep)
                 $condicion 
                 GROUP BY ref_op # los agrupo por operacion
              ) as mp
        LEFT JOIN
                (SELECT ref_cobro, 
                IFNULL(SUM(ventas_cobro.precio - ventas_cobro.comision), 0) as imp_db, # si no hay cobro con esa referencia le coloco valor 0
                GROUP_CONCAT(ventas_id SEPARATOR ',') AS ventas_id, # agrupo todos las ventas que tienen esa ref de cobro
                clientes_id
                FROM ventas_cobro
                    LEFT JOIN (SELECT ventas.ID as ID, clientes_id FROM ventas UNION ALL SELECT ventas_baja.ventas_id as ID, clientes_id FROM ventas_baja ) as vtas
                    ON ventas_cobro.ventas_id = vtas.ID
                    GROUP BY ref_cobro
                ) as cobro
        ON mp.ref_op = cobro.ref_cobro # No necesito mas esto -> COLLATE utf8_spanish_ci uno la tabla de mercadopago a la table de cobros";

        $query_rsCXP2 = $query_rsGRAL; /*** es la misma tabla QUE ARRIBA pero le hago distinto filtro */
        $query_rsCXP2 .= "
        WHERE ref_cobro IS NULL and imp_mp != '0.00' # filtro las op. de mp que no tienen pareja en bd y que tienen importe distinto a 0
        ORDER BY imp_mp ASC";

        $cobros_mp_sin_pareja = DB::select($query_rsCXP2);

        return $cobros_mp_sin_pareja;
    }

    public function configGeneral()
    {
        $cantidadStock = DB::table('stock')->select(
            'titulo',
            'consola'
        )
        ->groupBy(DB::raw("titulo, consola"))
        ->orderBy(DB::raw("consola, titulo"))
        ->get();

        $dominios = DB::table('dominios')->get();

        $cantidadStock = count($cantidadStock);
        $configuraciones = DB::table('configuraciones')->where('ID',1)->first();
        $oferta_fortnite = $configuraciones->oferta_fortnite;
        $cuentas_excluidas = $configuraciones->cuentas_excluidas;
        $titles = $this->wp_p->lastGameStockTitles();
        $titulos = [];
        $titulos_pri = [];
        $titulos_secu = [];
        $dominios_exclu = [];

        $productos_excluidos = explode(",", $configuraciones->productos_excluidos);

        if ($productos_excluidos) {
            foreach ($productos_excluidos as $value) {
                $titulos[] = str_replace('"', '', $value);
            }
        }
        $productos_excluidos_pri = explode(",", $configuraciones->prod_excluidos_pri);

        if ($productos_excluidos_pri) {
            foreach ($productos_excluidos_pri as $value) {
                $titulos_pri[] = str_replace('"', '', $value);
            }
        }
        $productos_excluidos_secu = explode(",", $configuraciones->prod_excluidos_secu);

        if ($productos_excluidos_secu) {
            foreach ($productos_excluidos_secu as $value) {
                $titulos_secu[] = str_replace('"', '', $value);
            }
        }

        $dominios_excluidos = explode(",", $configuraciones->dominios_excluidos);

        if ($dominios_excluidos) {
            foreach ($dominios_excluidos as $value) {
                $dominios_exclu[] = str_replace('"', '', $value);
            }
        }

        $options = $this->optionsConfig();

        return view('config.general', compact('options','oferta_fortnite','cuentas_excluidas','cantidadStock','configuraciones','titles','titulos','dominios','titulos_pri','titulos_secu','dominios_exclu'));
    }

    private function optionsConfig()
    {
        $options = [
            "menu1" => "Mensaje extra email",
            "menu2" => "Cuentas Excluidas - PS3 Resetear",
            "menu3" => "Reporte de Ventas",
            "menu4" => "Procesos Automaticos",
            "menu5" => "Parametros",
            "menu6" => "Productos Excluidos",
            "menu7" => "Dominios para Ctas",
            "menu8" => "Productos Excluidos Recupero",
            "menu9" => "Dominios excluidos para GC"
        ];
        asort($options);

        return $options;
    }

    public function configGeneralStore(Request $request)
    {
        switch ($request->opt) {
            case 1: // Texto Fortnite
                $data = [];
                $data['oferta_fortnite'] = $request->oferta_fortnite;

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Texto para ofertas fortnite actualizado.');
                return redirect()->back();
                break;
            
            case 2: // Cuentas Excluidas
                $data = [];
                $cuentas_excluidas = explode(",",$request->cuentas_excluidas);

                $cuentas_excluidas = array_map(function($ele) {
                    return trim($ele);
                }, $cuentas_excluidas);

                $data['cuentas_excluidas'] = implode(",",$cuentas_excluidas);

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Cuentas excluidas en PS3 Resetear actualizada.');
                return redirect()->back();
                break;
            case 3: // Parametros
                $inputs = $request->all();

                $data = [];
                
                foreach ($inputs as $index => $value) {
                    if ($index != '_token' && $index != 'opt') {
                        $data[$index] = $value;
                    }
                }

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Parametros actualizados correctamente.');
                return redirect()->back();
                break;
            case 4:
                $titulos = $request->productos_excluidos;
                $titles = [];
                foreach ($titulos as $value) {
                    $titles[] = '"'.$value.'"';
                }

                $titulos = implode(",", $titles);

                $data['productos_excluidos'] = $titulos;

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Productos excluidos registrados.');
                return redirect()->back();
                break;
            case 5: // Cuentas Robadas Excluidas
                $data = [];
                $cuentas_excluidas = explode(",",$request->cuentas_excluidas);

                $cuentas_excluidas = array_map(function($ele) {
                    return trim($ele);
                }, $cuentas_excluidas);

                $data['cuentas_robadas_excluidas'] = implode(",",$cuentas_excluidas);

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Cuentas robadas excluidas satisfactoriamente.');
                return redirect()->back();
                break;
            case 6:

                if ($request->dominio != "" && $request->accion == 'create-edit') {
                    $data['dominio'] = $request->dominio;
                    $data['indicador_habilitado'] = $request->habilitado;
                    $data['usuario'] = session()->get('usuario')->Nombre;
                    $data['update_at'] = date('Y-m-d H:i:s');
    
                    $accion = '';
    
                    if ($request->id_dominio != 0) {
                        $accion = 'editado';
                        DB::table('dominios')->where('ID',$request->id_dominio)->update($data);
                    } else {
                        $data['create_at'] = date('Y-m-d H:i:s');
                        $accion = 'creado';
                        DB::table('dominios')->insert($data);
                    }
                    
                    \Helper::messageFlash('Configuraciones',"Dominio $accion satisfactoriamente.");
                    return redirect()->back();
                } elseif ($request->accion == 'delete') {
                    DB::table('dominios')->where('ID',$request->id_dominio)->delete();
                    \Helper::messageFlash('Configuraciones',"Dominio eliminado satisfactoriamente.");
                }

                return redirect()->back()->withErrors(["El dominio no puede ser vacío"]);
                
                break;
            case 7: // Ventas sin entregar Excluidas
                $data = [];
                $ventas_excluidas = explode(",",$request->ventas_excluidas);
                $clientes_excluidos = explode(",",$request->clientes_excluidos);

                $ventas_excluidas = array_map(function($ele) {
                    return trim($ele);
                }, $ventas_excluidas);
                
                $clientes_excluidos = array_map(function($ele) {
                    return trim($ele);
                }, $clientes_excluidos);

                $data['ventas_sinentregar'] = implode(",",$ventas_excluidas);
                $data['clientes_sinentregar'] = implode(",",$clientes_excluidos);

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Ventas sin entregar excluidas satisfactoriamente.');
                return redirect()->back();
                break;
            case 8:
                $titulos_pri = $request->productos_excluidos_pri;
                $titulos_secu = $request->productos_excluidos_secu;
                $titles = [];
                if ($titulos_pri) {
                    foreach ($titulos_pri as $value) {
                        $titles[] = '"'.$value.'"';
                    }
                }
                $titulos_pri = implode(",", $titles);
                $data['prod_excluidos_pri'] = $titulos_pri;

                $titles = [];
                if ($titulos_secu) {
                    foreach ($titulos_secu as $value) {
                        $titles[] = '"'.$value.'"';
                    }
                }
                $titulos_secu = implode(",", $titles);
    
                $data['prod_excluidos_secu'] = $titulos_secu;

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Productos excluidos recupero registrados.');
                return redirect()->back();
                break;
            case 9:
                $dominios = $request->dominios_excluidos;
                $domains = [];
                foreach ($dominios as $value) {
                    $domains[] = '"'.$value.'"';
                }

                $dominios = implode(",", $domains);

                $data['dominios_excluidos'] = $dominios;

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Dominios excluidos registrados.');
                return redirect()->back();
                break;
        }
    }

    public function indexEvolucion()
    {
        $titulos = $this->getTitulosEvolucion()->get();

        return view('control.evolucion',compact('titulos'));
    }

    public function dataEvolucion(Request $request)
    {
        $filters['titulo'] = $request->titulo;
        $filters['consola'] = $request->consola;
        $filters['slot'] = $request->slot;
        $filters['agrupar'] = $request->agrupar;
        $filters['fecha_inicio'] = $request->fecha_inicio;
        $filters['fecha_fin'] = $request->fecha_fin;

        $datos = $this->getDatosEvolucion($filters)->get();

        echo json_encode($datos);
    }

    private function getDatosEvolucion($filter)
    {
        $datos = DB::table('ventas')
        ->select(
            DB::raw("COUNT(*) AS Q"),
            DB::raw("DATE(ventas.Day) as Day"),
            DB::raw('(SUM(precio)/COUNT(*)) as precio'),
            'ventas.slot',
            'stock.titulo',
            'stock.consola'
        )
        ->leftjoin('stock','ventas.stock_id','=','stock.ID')
        ->leftjoin(DB::raw("(SELECT ventas_id, SUM(precio) AS precio FROM ventas_cobro GROUP BY ventas_id) AS ventas_cob"),'ventas_cob.ventas_id','=','ventas.ID');

        $groupBy = '';
        

        if ($filter['titulo'] != '') {
            $datos->where('stock.titulo',$filter['titulo']);
        }
        if ($filter['consola'] != '') {
            $datos->where('stock.consola',$filter['consola']);
        }
        if ($filter['slot'] != '') {
            $datos->where('ventas.slot',$filter['slot']);
        }
        if ($filter['fecha_inicio'] != "" && $filter['fecha_fin'] != "") {
            $datos->whereBetween(DB::raw("DATE(ventas.Day)"), [$filter['fecha_inicio'], $filter['fecha_fin']]);
        }

        if ($filter['agrupar'] == 'dia') {
            $groupBy = "DATE(ventas.Day)";
        } elseif ($filter['agrupar'] == 'semana') {
            $groupBy = "YEAR(ventas.Day),WEEK(ventas.Day)";
        } elseif ($filter['agrupar'] == 'mes') {
            $groupBy = "YEAR(ventas.Day),MONTH(ventas.Day)";
        }

        $datos->groupBy(DB::raw($groupBy))
        ->orderBy(DB::raw($groupBy));

        return $datos;
    }

    private function getTitulosEvolucion()
    {
        $titulos = DB::table('ventas_cobro')
        ->select(
            DB::raw("CONCAT(stock.titulo,' (',stock.consola,')') AS titulo")
        )
        ->leftjoin('ventas','ventas_cobro.ventas_id','=','ventas.ID')
        ->leftjoin('stock','ventas.stock_id','=','stock.ID')
        ->groupBy('stock.titulo')
        ->groupBy('stock.consola')
        ->orderBy('stock.titulo','ASC');

        return $titulos;
    }

    public function excel()
    {
        $path_files_excel = [];

        $stocks = DB::table('stock')->select(
            'titulo',
            'consola'
        )
        ->groupBy(DB::raw("titulo, consola"))
        ->orderBy(DB::raw("consola, titulo"))
        ->get();

        foreach ($stocks as $stock) {
            $data_excel = $this->getVentaStock($stock->titulo, $stock->consola);

            if (count($data_excel) > 0) { // Se arma el excel siempre y cuando haya datos.

                $cabecera = ["Vta_id","Cte_id","Titulo","Consola","Apellido","Nombre","Email"];

                array_unshift($data_excel, $cabecera); // Agregar elemento a la primera posicion del array.

                $excel_title = "DB-$stock->titulo-$stock->consola-" . date('Y-m');

                $file = Excel::create($excel_title,
                        function ($excel) use ($data_excel) {
                            $excel->sheet("Reporte", function ($sheet) use ($data_excel) {
                                $sheet->fromArray($data_excel, null, 'A1', false, false);
                            });
                        });

                $path_files_excel[] = $file->store("csv",false,true)['full'];
            }

        }

        $zipTitle = 'Reports-' . date('Y-m') . '.zip';
        $path_zip = storage_path('app/public/'.$zipTitle);

        Zipper::make($path_zip)->add($path_files_excel)->close();

        try {
            Mail::send('emails.excel', [], function($message) use ($path_zip)
            {
                $message->to("victor.ross.04@gmail.com", "Victor Ross")->subject("Reportes");
                // $message->to("victor.ross.04@gmail.com", "Victor Ross")->cc("ortizkendry95@gmail.com", "Kendry Ortiz")->subject("Reportes");
                $message->attach($path_zip);
            });

            \Helper::messageFlash('Configuraciones','Se ha generado el reporte y ha sido enviado a su correo satisfactoriamente.');

            return redirect()->back();
            
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Ha ocurrido un error al intentar enviar el correo']);
        }
    }
    private function getVentaStock($titulo, $consola)
    {
        $result = DB::table('ventas AS v')
        ->select(
            'v.ID AS vta_id',
            'c.ID AS cte_id',
            's.titulo',
            's.consola',
            'c.apellido',
            'c.nombre',
            'c.email'
        ) 
        ->leftjoin('stock AS s','v.stock_id','=','s.ID')
        ->leftjoin('clientes AS c','v.clientes_id','=','c.ID')
        ->where('titulo',$titulo)
        ->where('consola',$consola)
        ->get();

        $data_excel = [];

        foreach ($result as $i => $value) {
            $data_excel[$i] = [];
            foreach ($value as $key => $value) {
                 $data_excel[$i][] = $value;
            }
        }

        return $data_excel;
    }

    public function controlVentas()
    {
        $ventas = Sales::getDatosControlVentas()->paginate(100);
        $gasto = $this->expenses->gastosControlVentas()->first();
        $gto_x_ing = $gasto->gasto / $gasto->ingreso;

        return view('control.control_ventas', compact('ventas','gto_x_ing'));
    }

    public function balance()
    {
        $row_rsVentas = $this->sales->totalVentas()->first();
        $row_rsGastos = Expenses::totalGastos()->first();
        $row_rsStock = Stock::totalesStock()->first();
		$row_rsStock2019 = Stock::totalesStock2019()->first();
        $row_rsStockVendido = $this->sales->stockVendido();
        $balance_mensual = $this->balance->balanceMensual();
        $row_rsCicloVtaGRAL = $this->sales->datosVentasBalance();
        $row_rsCicloVta = $this->sales->datosVentasBalance('ciclo_vta');
        $row_rsCicloVtaPS4 = $this->sales->datosVentasBalance('vta_ps4');
        $row_rsCicloVtaPS3 = $this->sales->datosVentasBalance('vta_ps3');
        $row_rsCicloVtaPS = $this->sales->datosVentasBalance('vta_ps');

        return view('control.balance', compact('row_rsVentas','row_rsGastos','row_rsStock','row_rsStock2019','row_rsStockVendido','balance_mensual','row_rsCicloVtaGRAL','row_rsCicloVta','row_rsCicloVtaPS4','row_rsCicloVtaPS3','row_rsCicloVtaPS'));
    }

    public function balanceProductos()
    {
        $rsCXP = Stock::getDatosBalanceProductos()->get();

        return view('control.balance_productos', compact('rsCXP'));
    }

    public function balanceProductosDias(Request $request)
    {
        $dias = isset($request->dias) ? $request->dias : 7;

        $rsCXP = Stock::getDatosBalanceProductosDias($dias);

        $filtro_dias = [3,7,14,28,60,90,180,360]; // Array que se utiliza para filtrar por días.

        return view('control.balance_productos_dias', compact('dias','rsCXP','filtro_dias'));
    }

    public function balanceProductosDiasCondicionado($page)
    {
        $dias = 15;

        switch ($page) {
            case 'ventas_7_dias':
                $dias = 7;
                break;
            case 'ventas_15_dias':
                $dias = 15;
                break;
            
            case 'ventas_30_dias':
                $dias = 30;
                break;
            case 'ventas_45_dias':
                $dias = 45;
                break;
        }

        $rsCXP = Stock::getDatosBalanceProductosDias($dias);

        $acceso = 'usuario';

        return view('control.balance_productos_dias', compact('dias','rsCXP','acceso'));
    }

    public function procesosAutomaticos($tipo)
    {
        switch ($tipo) {
            case 'automatizar_clientes':
                
                DB::table('clientes as a')
                ->leftjoin(DB::raw("(SELECT 
                clientes_id,
                COUNT(*) as q_vtas,
                DATEDIFF(NOW(), min( Day )) as dias_de_primer_venta
                FROM ventas GROUP BY clientes_id) as b"),'a.ID','=','b.clientes_id')
                ->where('a.auto','no')
                ->where(DB::raw("(dias_de_primer_venta > 180) or ((dias_de_primer_venta > 120) and (q_vtas > 1))"))
                ->update(['auto' => 'si']);

                \Helper::messageFlash('Configuraciones','Clientes automatizados correctamente.');

                return redirect()->back();

                break;
            
            case 'actualizar_estado_wc':
                
                $datos = DB::select("SELECT db.*, web.*, cbgw_posts.ID, cbgw_posts.post_status
                FROM (SELECT COUNT(*) as q_db, order_id_web FROM (SELECT order_id_web FROM ventas GROUP BY order_item_id) as result GROUP BY order_id_web) as db
                #primero agrupo por oii para evitar que cuente duplicado dos ventas de la base de datos correspondientes a un solo producto del pedido web ej(2 GC de 50usd por un producto GC 100usd)
                LEFT JOIN (SELECT COUNT(*) as q_web, order_id FROM cbgw_woocommerce_order_items WHERE order_item_type='line_item' GROUP BY order_id) as web
                #solo cuento los productos dentro de un pedido, para evitar contar cupones o descuentos que sean un item dentro del pedido
                ON db.order_id_web = web.order_id
                LEFT JOIN cbgw_posts 
                on db.order_id_web = cbgw_posts.ID
                WHERE cbgw_posts.post_status = 'wc-processing' AND q_db >= q_web");

                DB::beginTransaction();

                try {
                    $pedidos = [];
                    foreach ($datos as $value) {
                       if ($value->order_id) {
                           DB::table('cbgw_posts')->where('ID', $value->order_id)->update(['post_status' => 'wc-completed']);

                           $post_id = $value->order_id;
                           $meta_key = "_completed_date";
                           $date = date('Y-m-d H:i:s');

                           $data['post_id'] = $post_id;
                           $data['meta_key'] = $meta_key;
                           $data['meta_value'] = $date;

                           DB::table('cbgw_postmeta')->insert($data);

                           $pedidos[] = $post_id;
                       }
                    }

                    DB::commit();

                    \Helper::messageFlash('Configuraciones',"Pedido(s) (".implode(",",$pedidos).") marcado(s) como entregado.");

                    return redirect()->back();
                } catch (Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors(['Ha ocurrido un error en el proceso.']);
                }

                break;
            case 'actualizar_ids_ventas':

				///// 2019-12-06 agrego limit de 4800 order item id para que no tire abajo el servidor
                DB::table('ventas AS a')
                ->leftjoin(DB::raw("(SELECT 
                order_item_id,
                order_id,
                max( CASE WHEN cbgw_postmeta.meta_key='order_id_ml' and cbgw_woocommerce_order_items.order_id=cbgw_postmeta.post_id THEN cbgw_postmeta.meta_value END ) as order_id_ml
                FROM cbgw_woocommerce_order_items
                LEFT JOIN cbgw_postmeta 
                ON order_id = cbgw_postmeta.post_id
                GROUP BY order_item_id
				ORDER BY order_item_id desc
                LIMIT 4800) as b"),'a.order_item_id','=','b.order_item_id')
                ->whereNotNull('a.order_item_id')
                ->whereNull('a.order_id_ml')
                ->whereNotNull('b.order_id_ml')
                ->update([
                    'a.order_id_web' => 'b.order_id',
                    'a.order_id_ml' => 'b.order_id_ml'
                ]);

                \Helper::messageFlash('Configuraciones',"IDS de ventas actualizadas correctamente.");

                return redirect()->back();

                break;
            case 'actualizar_costo_ps4':

                $datos = DB::table(DB::raw("(SELECT cuentas_id as sa_cta_id, SUM(costo_usd) as sa_costo_usd, SUM(costo) as sa_costo FROM saldo GROUP BY cuentas_id) as saldo"))
                ->select(
                    'sa_cta_id as cuentas_id',
                    DB::raw("(sa_costo_usd - COALESCE(st_costo_usd,0)) as libre_usd"),
                    DB::raw("(sa_costo - COALESCE(st_costo,0)) as libre_ars"),
                    'consola',
                    'stock_id'
                )
                ->leftjoin(DB::raw("(SELECT cuentas_id as st_cta_id, SUM(costo_usd) as st_costo_usd, SUM(costo) as st_costo, GROUP_CONCAT(consola) as consola, GROUP_CONCAT(ID) as stock_id FROM stock GROUP BY cuentas_id) as stock"),'saldo.sa_cta_id','=','stock.st_cta_id')
                ->where(DB::raw("(sa_costo_usd - COALESCE(st_costo_usd,0))"),'!=',0)
                ->where('consola','ps4')
                ->where('st_cta_id','>=',3786)
                ->orderBy('stock.consola','DESC')
                ->get();

                $mensajes = '';

                DB::beginTransaction();

                try {
                    foreach ($datos as $value) {
                        if ($value->cuentas_id) {
                            $stock_id = $value->stock_id;
                            $cuentas_id = $value->cuentas_id;
                            $libre_usd = $value->libre_usd;
                            $libre_ars = $value->libre_ars;

                            if(($libre_ars > 0.00) and ($libre_ars < 300.00)) {

                                DB::table('stock')->where('ID',$stock_id)
                                ->update([

                                    'costo' => "(costo + $libre_ars)"

                                ]);

                                DB::table('cuentas_notas')
                                ->insert([

                                    'cuentas_id' => $cuentas_id,
                                    'Notas' => 'nota atuomatica: actualizo costo en pesos',
                                    'usuario' => 'Sistema'

                                ]);

                                $mensajes .= "[" . $cuentas_id . "] " . $stock_id . " actualizado en " . $libre_ars . " ARS<br>";
                                $mensajes .= "[" . $cuentas_id . "] nota añadida<br>";

                            }

                            /****
							if($libre_usd < 0.22) {

                                DB::table('stock')->where('ID',$stock_id)
                                ->update([

                                    'costo_usd' => "(costo_usd + $libre_usd)"

                                ]);

                                DB::table('cuentas_notas')
                                ->insert([

                                    'cuentas_id' => $cuentas_id,
                                    'Notas' => 'nota atuomatica: actualizo costo en dolares',
                                    'usuario' => 'Sistema'

                                ]);

                                $mensajes .= "[" . $cuentas_id . "] " . $stock_id . " actualizado en " . $libre_usd . " USD<br>";
                                $mensajes .= "[" . $cuentas_id . "] nota añadida<br>";

                            }*/
                        }
                    }

                    DB::commit();

                    // \Helper::messageFlash('Configuraciones',"Costos de stocks PS4 actualizados correctamente.");

                    // return redirect()->back();
                    return $mensajes;
                } catch (Exception $e) {
                    DB::rollback();
                    // return redirect()->back()->withErrors(['Ha ocurrido un error inesperado en el proceso.']);
                    return 'Ha ocurrido un error inesperado en el proceso.';
                }

                break;
            case 'automatizar_stock_web':

                $configuraciones = $this->getConfiguraciones();

                $condicion_prod_excl = $configuraciones->productos_excluidos ? "WHERE producto NOT IN ($configuraciones->productos_excluidos)" : "";

                $datos = DB::select("SELECT web.*, vtas.*, IFNULL((Q_vta_pri - Q_vta_sec),0) as libre
                        FROM
                        (select
                            p.ID,
                            p.post_title,
                            REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p_p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') as producto,
                            max( CASE WHEN pm.meta_key = 'consola' and  p.post_parent = pm.post_id THEN pm.meta_value END ) as consola,
                            max( CASE WHEN pm2.meta_key = '_regular_price' and p.ID = pm2.post_id THEN pm2.meta_value END ) as _regular_price,
                            max( CASE WHEN pm2.meta_key = '_precio_base' and p.ID = pm2.post_id THEN pm2.meta_value END ) as _precio_base,
                            max( CASE WHEN pm2.meta_key = 'attribute_pa_slot' and p.ID = pm2.post_id THEN pm2.meta_value END ) as slot,
                            max( CASE WHEN pm2.meta_key = '_stock_status' and p.ID = pm2.post_id THEN pm2.meta_value END ) as stock_status,
                            max( CASE WHEN pm2.meta_key = '_stock' and p.ID = pm2.post_id THEN pm2.meta_value END ) as stock,
                            p_p.post_status
                        from
                         cbgw_posts as p
                         left join cbgw_posts as p_p ON p.post_parent = p_p.ID
                         left join cbgw_postmeta as pm ON p.post_parent = pm.post_id
                         left join cbgw_postmeta as pm2 ON p.ID = pm2.post_id 
                        where
                            p.post_type = 'product_variation' and
                            p_p.post_status = 'publish'
                        group by
                            p.ID
                            order by p.post_title) as web
                            
                        LEFT JOIN
                        (SELECT 
                            titulo, 
                            SUM(case when slot = 'Primario' then 1 else 0 end) AS Q_vta_pri, 
                            SUM(case when slot = 'Secundario' then 1 else 0 end) AS Q_vta_sec,
                            DATEDIFF(NOW(), MIN(stock.Day)) as antiguedad_juego
                        FROM 
                            ventas 
                        LEFT JOIN 
                            stock 
                        ON 
                            ventas.stock_id = stock.ID
                        WHERE 
                            (consola = 'ps4' or titulo = 'plus-12-meses-slot')
                        GROUP BY 
                            titulo  ) as vtas
                        ON
                        web.producto = vtas.titulo
                        $condicion_prod_excl
                        ");

                $mensajes = '';

                DB::beginTransaction();

                try {
                    foreach ($datos as $value) {
                        if ($value->producto) {
                            $ID = $value->ID;
                            $producto = $value->producto;
                            $slot = $value->slot;
                            $stock = $value->stock_status;
                            $precio_regular = $value->_regular_price;
                            $precio_base = $value->_precio_base;
                            $antiguedad_juego = $value->antiguedad_juego;

                            if($value->Q_vta_pri === "NULL"): $qvp = 0; else: $qvp = $value->Q_vta_pri; endif;
                            if($value->Q_vta_sec === "NULL"): $qvs = 0; else: $qvs = $value->Q_vta_sec; endif;
                            if($value->stock === "NULL"): $stock_Q = 0; else: $stock_Q = $value->stock; endif;
                            if(($value->libre === "NULL") or ($value->libre < 0)): $libre = 0; else: $libre = $value->libre; endif;

                            if(strpos($value->producto, 'fifa-18') !== false): $margen = 0;
                            elseif(strpos($value->producto, 'plus-12-meses-slot') !== false): $margen = 0;
                            else: $margen = 5;
                            endif;

                            if(($qvs * 0.1) > 10): $multi = 10; else: $multi = ($qvs * 0.1); endif;

                            if($slot == "primario"){
                                $factorA = 0; $factorB = 0; $factorC = 0;           
                                if ($libre > 0 && $qvp > 0){
                                $factorA = ($libre / 500);
                                    if ($antiguedad_juego > 30){ 
                                        $factorB = (($libre / $qvp)*($libre / $qvp));
                                    }   
                                }
                                
                                if($antiguedad_juego > 0){
                                    $factorC = ($antiguedad_juego / 2000);
                                    if($factorC > 0.08) {$factorC = 0.08;} 
                                }
                                
                                $multiplier = 1 + ($factorA + $factorB + $factorC); 
                                if($multiplier > 1.30) {$multiplier = 1.30;}
                                $new_price = ($precio_base * $multiplier); 
                                $new_price = round($new_price, 1);
                                //$new_price = (ceil($new_price)*25);
                                    
                                    
                                if((($new_price > ($precio_regular * 1.025)) or ($new_price < ($precio_regular * 0.975))) and $new_price > 1){  
                                    DB::table('cbgw_postmeta')
                                    ->where('post_id',$ID)
                                    ->whereRaw("(meta_key='_price' or meta_key='_regular_price')")
									//->where('meta_key','_price')
                                    ->update([
                                        'meta_value' => $new_price
                                    ]);
									

                                    $mensajes .= "ID:" . $ID . " -multi:". round($multiplier,2) . " -qvp:" . $qvp . " -qvs:" . $qvs . " -lib:". $libre ." -ant:" . $antiguedad_juego . " //// " . $producto.  " " . $slot . " -> de " . $precio_regular . " a " . $new_price. " (base " . $precio_base .  ")<br>";
                                }
                                
                                
                                if(($qvp <= ($qvs + $multi + $margen)) && ($stock == "outofstock")){
                                    DB::table('cbgw_postmeta')
                                    ->where('post_id',$ID)
                                    ->where('meta_key','_stock_status')
                                    ->update([
                                        'meta_value' => 'instock'
                                    ]);

                                    DB::table('cbgw_postmeta')
                                    ->where('post_id',$ID)
                                    ->where('meta_key','_stock')
                                    ->update([
                                        'meta_value' => '999'
                                    ]);

                                    $mensajes .= $producto.  " " .$slot. " agregado a stock<br>";
                                }
                            }

                            if($slot == "secundario"){
                                
                                if($qvs >= $qvp){
                                    if($stock == "instock"){
                                        
                                          DB::table('cbgw_postmeta')
                                          ->where('post_id',$ID)
                                          ->where('meta_key','_stock_status')
                                          ->update([
                                              'meta_value' => 'outofstock'
                                          ]);

                                          $mensajes .= $producto.  " " .$slot. " quitado de stock<br>";
                                    }
								}
                                if(($qvs < $qvp) && ($stock == "outofstock")){
                                    DB::table('cbgw_postmeta')
                                    ->where('post_id',$ID)
                                    ->where('meta_key','_stock_status')
                                    ->update([
                                        'meta_value' => 'instock'
                                    ]);

                                    $mensajes .= $producto.  " " .$slot. " agregado a stock<br>";
                                }
                                if( ($libre > $stock_Q) or ($libre < $stock_Q) ) {
                                    DB::table('cbgw_postmeta')
                                    ->where('post_id',$ID)
                                    ->where('meta_key','_stock')
                                    ->update([
                                        'meta_value' => $libre
                                    ]);

                                    $mensajes .= '[' . $ID . '] ' . $producto .  " " .$slot. " cambiado a " . $libre . " stock<br>";
                                      
                                }
                            }

                        }
                    }

                    DB::commit();

                    /*\Helper::messageFlash('Configuraciones',"Stock web automatizados correctamente.");

                    return redirect()->back();*/

                    return $mensajes;
                } catch (Exception $e) {
                    DB::rollback();
                    // return redirect()->back()->withErrors(['Ha ocurrido un error inesperado en el proceso.']);
                    return 'Ha ocurrido un error inesperado en el proceso.';
                }

                break;
            case 'automatizar_web_ps3':

                $datos = DB::select("SELECT ID, REPLACE(producto,'-',' ') as producto, rdo_web_2.consola, _regular_price, _precio_base, IFNULL(costoxU, 0) as costoxU, IFNULL(Q_Stock, 0) as Q_Stock, IFNULL(Q_Vta, 0) as Q_Vta FROM (SELECT ID, producto, consola, _regular_price, _precio_base FROM (select
                        p.ID,
                        p.post_title,
                        REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') as producto,
                        max( CASE WHEN pm.meta_key = 'consola' and  p.ID = pm.post_id THEN pm.meta_value END ) as consola,
                        max( CASE WHEN pm.meta_key = '_regular_price' and p.ID = pm.post_id THEN pm.meta_value END ) as _regular_price,
                        max( CASE WHEN pm.meta_key = '_precio_base' and p.ID = pm.post_id THEN pm.meta_value END ) as _precio_base
                    from
                     cbgw_posts as p
                     left join cbgw_postmeta as pm ON p.ID = pm.post_id
                    where
                        p.post_type = 'product' and
                        p.post_status = 'publish'
                    group by
                        p.ID  
                    ORDER BY `consola`  ASC) as rdo_web
                    Where consola = 'ps3') AS rdo_web_2


                    LEFT JOIN


                    (SELECT ID_stk, stk.titulo, stk.consola, (costo_usd / Q_Stock) as costoxU, Q_Stock, IFNULL(q_venta,0) as Q_Vta
                    FROM
                    (SELECT ID_stk, titulo, consola, stk_ctas_id, dayreset, Q_reset, days_from_reset, Q_vta, round(AVG(costo_usd),2) as costo_usd, SUM(Q_Stock) AS Q_Stock FROM (SELECT ID AS ID_stk, titulo, consola, round(AVG(costo_usd),2) as costo_usd, cuentas_id AS stk_ctas_id, ID_reseteo AS ID_reset, r_cuentas_id AS reset_ctas_id, dayreseteo AS dayreset, reset.Q_reseteado AS Q_reset, DATEDIFF(NOW(), dayreseteo) AS days_from_reset, ID_vta, Q_vta, dayvta, ((2 + (IFNULL(Q_reseteado, 0) * 2)) - IFNULL(Q_vta, 0)) AS Q_Stock
                    FROM stock 
                    LEFT JOIN
                    (SELECT ID AS ID_reseteo, cuentas_id AS r_cuentas_id, COUNT(*) AS Q_reseteado, MAX(Day) AS dayreseteo
                    FROM reseteo
                    GROUP BY cuentas_id
                    ORDER BY ID DESC) AS reset
                    ON cuentas_id = r_cuentas_id
                    LEFT JOIN
                    (SELECT ventas.ID as ID_vta, stock_id, COUNT(*) AS Q_vta, Day AS dayvta
                    FROM ventas
                    GROUP BY stock_id) AS vendido
                    ON ID = stock_id
                    WHERE consola = 'ps3' AND (((Q_vta IS NULL) OR (Q_vta < '2')) OR (((Q_vta >= '2') AND (Q_reseteado = FLOOR(Q_vta/2)))))
                    GROUP BY ID
                    ORDER BY Q_reset, consola, titulo, ID DESC) AS consulta
                    GROUP BY consola, titulo
                    ORDER BY consola, titulo, ID_stk) as stk

                    LEFT JOIN

                    (SELECT ID, titulo, consola, IFNULL(SUM(cantidadventa),0) AS q_venta
                    FROM stock
                    RIGHT JOIN
                    (SELECT stock_id, COUNT(*) AS cantidadventa
                    FROM ventas
                    WHERE ventas.Day >= DATE(NOW() - INTERVAL 45 DAY)
                    GROUP BY stock_id) AS vtas
                    ON stock.ID = vtas.stock_id
                    WHERE consola='ps3'
                    GROUP BY consola, titulo
                    ORDER BY q_venta DESC, consola ASC, titulo ASC) as vta

                    ON stk.titulo = vta.titulo) as rdo_db


                    ON rdo_web_2.producto = rdo_db.titulo
                    ORDER BY `rdo_web_2`.`producto` ASC");

                $mensajes = '';

                DB::beginTransaction();

                try {
                    foreach ($datos as $value) {
                        if ($value->producto) {
                            $ID = $value->ID;
                            $producto = $value->producto;
                            $precio_regular = $value->_regular_price;
                            $precio_base = $value->_precio_base;
                            $Q_Stock = $value->Q_Stock;
                            $Q_Vta = $value->Q_Vta;
                            $costoxU = $value->costoxU;
                            
							$costoxU = $costoxU * 1.25; //le recargo 25% por las dudas
							
                            $multiplier = 1;
                            if($Q_Vta > 0) { 
                                $multiplier = ($Q_Stock / ($Q_Vta * 0.66)); 
                                if($multiplier > 4){$multiplier = 4;}
                                if($multiplier < 0.6){$multiplier = 0.6;}
                            }
                            
                            $new_price = ($precio_base / $multiplier); 
                            
                            if($new_price < ($costoxU * 1.10)) { 
                               $new_price = $costoxU * 1.10;
                            }
                            // 2019-10-28 actualizado para pasar a usd
							//if($new_price < 100) { $new_price = 100;}
							if($new_price < 1.5) { $new_price = 1.5;}
                            
                            if($new_price > ($precio_base * 1.1)) { $new_price = ($precio_base * 1.1);} 
                            
							//redondeo resultado
                            //$new_price = (round($new_price, 0)/5); 
                            //$new_price = (ceil($new_price)*5);
                            // 2019-10-28 actualizado para usd
							// $new_price = round($new_price, 0);
							$si = "no";
                            if(($new_price > ($precio_regular * 1.025)) or ($new_price < ($precio_regular * 0.975))){
                                $si = "sii";
                                
                                $_sale_price = DB::table('cbgw_postmeta')
                                ->where('post_id',$ID)
                                ->where('meta_key','_sale_price')->first();
                                
                                if ($_sale_price) {
                                    DB::table('cbgw_postmeta')
                                    ->where('post_id',$ID)
                                    ->where('meta_key','_sale_price')
                                    //->where(DB::raw("(meta_key='_price' or meta_key='_regular_price')"))
                                    ->update([
                                        'meta_value' => $new_price
                                    ]);
                                } else {
                                    $data['post_id'] = $ID;
                                    $data['meta_key'] = '_sale_price';
                                    $data['meta_value'] = $new_price;

                                    DB::table('cbgw_postmeta')->insert($data);
                                }
                                
                                
                                
								
								DB::table('cbgw_postmeta')
                                ->where('post_id',$ID)
								->where('meta_key','_price')
                                //->where(DB::raw("(meta_key='_price' or meta_key='_regular_price')"))
                                ->update([
                                    'meta_value' => $new_price
                                ]);

                                $mensajes .= " -s:". $Q_Stock . " -v:". $Q_Vta . " -divis:". round($multiplier,2) . " -costoxU:" . $costoxU . " //// " . $producto.  " -> de " . $precio_regular . " a " . $new_price. " (base " . $precio_base .  ") atacó db: " . $si . "<br>";
                            }
                            
                        }
                    }

                    DB::commit();

                    /*\Helper::messageFlash('Configuraciones',"Stock web PS3 automatizados correctamente.");

                    return redirect()->back();*/

                    return $mensajes;
                } catch (Exception $e) {
                    DB::rollback();
                    // return redirect()->back()->withErrors(['Ha ocurrido un error inesperado en el proceso.']);
                    return 'Ha ocurrido un error inesperado en el proceso.';
                }

                break;
            case 'automatizar_web_ps4':

                $configuraciones = $this->getConfiguraciones();

                $condicion_prod_excl = $configuraciones->productos_excluidos ? "AND producto NOT IN ($configuraciones->productos_excluidos)" : "";

                $datos = DB::select("SELECT web.*, IFNULL(Qvp,0) as Qvp, IFNULL(Qvs,0) as Qvs, IFNULL((Qvp - Qvs),0) as libre,  IFNULL(antiguedad,0) as ant_stk, IFNULL(Qvp_45d,0) as Qvp_45d, IFNULL(Qvs_45d,0) as Qvs_45d, IFNULL(costo_usd,0) as costo_usd, IFNULL(Q_stk,0) as Q_stk
                    FROM
                    (select
                        p.ID,
                        REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(p_p.post_title)), ' ', '-'), '''', ''), '’', ''), '.', '') as producto,
                        max( CASE WHEN pm.meta_key = 'consola' and  p.post_parent = pm.post_id THEN pm.meta_value END ) as consola,
                        max( CASE WHEN pm2.meta_key = 'attribute_pa_slot' and p.ID = pm2.post_id THEN pm2.meta_value END ) as slot,
                        max( CASE WHEN pm2.meta_key = '_regular_price' and p.ID = pm2.post_id THEN pm2.meta_value END ) as _regular_price,
                        max( CASE WHEN pm2.meta_key = '_precio_base' and p.ID = pm2.post_id THEN pm2.meta_value END ) as _precio_base,
                        max( CASE WHEN pm2.meta_key = '_sale_price' and p.ID = pm2.post_id THEN pm2.meta_value END ) as _sale_price
                    from
                     cbgw_posts as p
                     left join cbgw_posts as p_p ON p.post_parent = p_p.ID
                     left join cbgw_postmeta as pm ON p.post_parent = pm.post_id
                     left join cbgw_postmeta as pm2 ON p.ID = pm2.post_id 
                    where
                        p.post_type = 'product_variation' and
                        p_p.post_status = 'publish'
                    group by
                        p.ID
                        order by p.post_title ASC) as web
                        
                    LEFT JOIN
                    (SELECT 
                        titulo, 
                        SUM(case when slot = 'Primario' then 1 else 0 end) AS Qvp, 
                        SUM(case when slot = 'Secundario' then 1 else 0 end) AS Qvs
                    FROM ventas LEFT JOIN stock 
                    ON ventas.stock_id = stock.ID
                    WHERE consola = 'ps4'
                    GROUP BY titulo) as vtas
                    ON web.producto = vtas.titulo

                    LEFT JOIN
                    (SELECT 
                        titulo, 
                        SUM(case when slot = 'Primario' then 1 else 0 end) AS Qvp_45d, 
                        SUM(case when slot = 'Secundario' then 1 else 0 end) AS Qvs_45d
                    FROM ventas LEFT JOIN stock 
                    ON ventas.stock_id = stock.ID
                    WHERE consola = 'ps4' AND (DATEDIFF(NOW(), ventas.Day) < 45)
                    GROUP BY titulo) as vtas_45d
                    ON web.producto = vtas_45d.titulo

                    LEFT JOIN
					## 2019-12-04 aumento el costo de pri a 63% y bajo el de secu porque siempre se vende el pri facil
                    (SELECT ID AS ID_stk, titulo, consola, (round(AVG(costo_usd_modif),0)*0.63) as costo_usd, 'primario' as Stk_slot, Count(*) as Q_stk, DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day)))) as antiguedad
                    FROM stock
                    LEFT JOIN
                    (SELECT stock_id, SUM(case when slot = 'Primario' then 1 else null end) AS Q_vta_pri_1
                    FROM ventas
                    GROUP BY stock_id) AS vendido
                    ON ID = stock_id
                    WHERE consola = 'ps4' AND Q_vta_pri_1 IS NULL
                    GROUP BY consola, titulo

                    UNION ALL

                    SELECT ID AS ID_stk, titulo, consola, (round(AVG(costo_usd_modif),0)*0.37) as costo_usd, 'secundario' as Stk_slot, Count(*) as Q_stk, DATEDIFF(NOW(), FROM_UNIXTIME(AVG(UNIX_TIMESTAMP(stock.Day)))) as antiguedad
                    FROM stock
                    LEFT JOIN
                    (SELECT stock_id, SUM(case when slot = 'Secundario' then 1 else null end) AS Q_vta_sec_1
                    FROM ventas
                    GROUP BY stock_id) AS vendido
                    ON ID = stock_id
                    WHERE consola = 'ps4' AND Q_vta_sec_1 IS NULL
                    GROUP BY consola, titulo
                    ) as stk

                    ON
                    web.producto = stk.titulo and web.slot = stk.Stk_slot
                    WHERE producto!='plus-12-meses-slot' $condicion_prod_excl
                    ORDER BY producto ASC");
				
				$control_individual = '';
                $mensajes = '';

                DB::beginTransaction();

                try {
                    $mensajes .= "<table>";
                    foreach ($datos as $value) {
                        if ($value->producto) {
                            $ID = $value->ID;
                            $producto = $value->producto;
                            $slot = $value->slot;
                            $precio_regular = $value->_regular_price;
                            $precio_base = $value->_precio_base;
                            $sale_price = $value->_sale_price;
                            $qvp = $value->Qvp;
                            $qvs = $value->Qvs;
                            $libre = $value->libre;
                            $qvp_45d = $value->Qvp_45d;
                            $qvs_45d = $value->Qvs_45d;
							$costo_usd_original = $value->costo_usd;
                            $costo_usd = $value->costo_usd * $configuraciones->costo_automatizar_web_ps4;
                            $antiguedad = $value->ant_stk;
                            $Q_stk = $value->Q_stk;
                            $qv = null;
                            $qv_45d = null;
                            $divi = null;
                            $valor_oferta_sugerida = $configuraciones->valor_oferta_sugerida;
							
							/***
							if($costo_usd < 20) { // si es menor a 20 redondeo de a 10 usd
								$x=10;
							} else { // si es superior a 20 redondeo de a 5 usd
								$x=5;
							}*/
							
							/*** 2020-05-05 quito el redonde
							$x=3; // redondeo de 3 en 3 el costo usd porque es un AVG y no siempre tiene sentido
							$n = $costo_usd;
							$costo_usd_redondo = (ceil($n)%$x === 0) ? ceil($n) : round(($n+$x/2)/$x)*$x;
							*/
                            
							$costo_usd_redondo = $costo_usd;
							
					$control_individual .= " <strong>ID: " . $ID . " - ". str_replace('-', ' ', $producto) . " " . $slot ."</strong>";
					$control_individual .= "<br />Reg: " . $precio_regular . " // Base: " . $precio_base . " // Sale: " . $sale_price . " // C_orig: " . $costo_usd_original . " // C_redondeo: " . $costo_usd_redondo . " // Q_Stk: " . $Q_stk . " // Ant: " . $antiguedad ;
							
							$costo_usd = $costo_usd_redondo; // cambio el costo usd al redondeado
															
                            // arreglo la variable qv y qv_45d
                            if($slot == "primario"){
                                $qv = $qvp; $qv_45d=$qvp_45d;
                            }
                            if($slot == "secundario"){
                                $qv = $qvs; $qv_45d=$qvs_45d;
                            }
                            
                            //Si la cantidad de venta es 0 las paso a 1
                            if($qv<=1) {$qv=1;}
							if($qv_45d<=1) {$qv_45d=1;} 
							
					$control_individual .= "<br />qvp_45d: " . $qvp_45d . " // qvs_45d: " . $qvs_45d . " // qvp: " . $qvp . " // qvs: " . $qvs . " // Libre: " . $libre . " // Q_Stk: " . $Q_stk;
                                
                            /****
							////// 2020/05/03 aplico nueva columna de costo_usd_modif para evitarme este calculo de abajo... 
			
							// nueva formula para aplicar baja de precio a mayor antiguedad del stock
							// 0.98 elevado a la 36 da 0.48, es decir que reduciría el costo a 48% del original
							if ($antiguedad >= 720) { 
								$elevado=36;
							} 
                            elseif ($antiguedad >= 360) { 
								$elevado=($antiguedad/20);
							} 
							elseif ($antiguedad >= 45) { // si es < 45 va a ir reduciendo el costo original de a poco cuanto mas días pasan
								$elevado=($antiguedad/30);
							}
							else {
								$elevado=0.1;
							}
							
							if ($antiguedad >= 180) { // 2019-12-03 : si ant prom es > 180d le bajo 10% mas
								$extra_x_anti=0.9;
							} 
							else {
								$extra_x_anti=1;
							}
                            //pow es la formula de exponente para PHP, no existe el ^
							////// 2020-02-26 actualizo el elavo a 0.98 para aumentar precios debido a que muchos reciclados son reclamados por los clientes
							$fn_exp = (pow(0.97, $elevado));
                            $costo_usd = $costo_usd * $fn_exp * $extra_x_anti;
							
							***/
                            
                            // el precio de oferta sugerido tiene que ser X % mayor al costo para asegurar ganancia, configuro desde sistema en CONFIG > GENERAL
                            $oferta_sugerida = $costo_usd * $valor_oferta_sugerida;
							
					/**** 2020/05/03 modifico el texto que imprimo 
					$control_individual .= "<br /><br /> costo x (0.97 ^" . round($elevado,2) . ") = C_modif: " . round($costo_usd,2) . " x (multi con gcia): " . $valor_oferta_sugerida . " * coef ant 180: " . $extra_x_anti . " queda en: " . round($oferta_sugerida,2);
                    */
                      
					$control_individual .= "<br /><br /> C_modif: " . round($costo_usd,2) . " x (multi con gcia): " . $valor_oferta_sugerida . " queda en: " . round($oferta_sugerida,2);		
							
                            // si hay muchas ventas y poco stock voy subiendo el precio de oferta
                            if($Q_stk > 3){
                                $divi = ($qv_45d/$Q_stk);
                                $divi = round(pow($divi,0.8),2); // elevo a la 0,8 para suavizar el resultado a menos
                                if($divi >= 3) {$divi = 3;}
                                elseif($divi <= 0.85) {$divi = 0.85;}
								else {$divi = $divi;}
                                $oferta_sugerida = $oferta_sugerida * $divi;
					$control_individual .= "<br /> >3 Stk subo precio si hay mucha Vta_45 y poco Stk // el es multi es: " . round($divi,2) . " queda en " . round($oferta_sugerida,2);
                            }
					
                            // si se vendió 2 o mas limito la oferta cuando queda poco stock
							if($qv_45d >= 2) {
								if($Q_stk == 5){$limite_Stk = $precio_base * 0.750;} 
								elseif($Q_stk == 4){$limite_Stk = $precio_base * 0.800;}
								elseif($Q_stk == 3){$limite_Stk = $precio_base * 0.850;}
								elseif($Q_stk == 2){$limite_Stk = $precio_base * 0.900;} 
								elseif($Q_stk == 1){$limite_Stk = $precio_base * 0.920;} 
								else{$limite_Stk = $oferta_sugerida;} 
								
								if($oferta_sugerida < $limite_Stk) {
								$oferta_sugerida = $limite_Stk;
					$control_individual .= "<br /> > 1 Vta y < 6 stk -> precio límite inferior: " . $limite_Stk . ", queda en: " . round($oferta_sugerida,2);
							} 
							}					             
					
                            
                            if($qv_45d == 3){$estimulo_Vta45 = 0.94;} 
                            elseif($qv_45d == 2){$estimulo_Vta45 = 0.90;} 
                            elseif($qv_45d == 1){$estimulo_Vta45 = 0.86;}
                            elseif($qv_45d == 0){$estimulo_Vta45 = 0.82;}
                            else{$estimulo_Vta45 = 1;}
							
                    /**** 2019-11-27 quito esto porque no tiene tanta relación, mejoro oferta cuanto mas stock tengo en relación a mis ventas... mayor stk disponible mejor oferta traslado al público */
                            //if(($Q_stk/$qv) > 0.30){$estimulo_Relacion = 0.925;} 
                            //elseif(($Q_stk/$qv) > 0.20){$estimulo_Relacion = 0.950;} 
                            //elseif(($Q_stk/$qv) > 0.10){$estimulo_Relacion = 0.975;} 
                            //else{$estimulo_Relacion = 1;} 
							$estimulo_Relacion = 1; //COMENTAR ESTA LINEA SI HABILITO LA DE ARRIBA
                            
                            $oferta_sugerida = $oferta_sugerida * $estimulo_Vta45 * $estimulo_Relacion;
					$control_individual .= "<br /> < 3 vtas en 45d -> bajo precio // multi por: " . $estimulo_Vta45 . " queda en: " . round($oferta_sugerida, 2) ;

                            
                            // límite inferior máximo: el precio de oferta no puede ser menor al 30% del "precio base"
                            if($oferta_sugerida < ($precio_base * 0.30))  {
								$oferta_sugerida = ($precio_base * 0.30);
					$control_individual .= "<br /> oferta no puede ser < 35% de precio base // queda en : " . round($oferta_sugerida,2);
							}
                       
                            // si no queda stock secundario quito oferta
                            if(($slot == "secundario") and ($libre <= 1)) {
								$oferta_sugerida = 0;
					$control_individual .= "<br /> no queda STK secu quito oferta secu";
							}
                            
                            // si la cantidad de venta histórica es menor o igual a 3 y además es igual a lo vendido en 45 días posiblemente es juego nuevo y no quiero bajar precio
							/*** 2019-11-27 QUITO ESTO QUE NO ME PERMITIÓ VENDER EL NINJA GO DE LEGO CUANDO COMPRAMOS BARATO POR PRIMERA VEZ 
                            if(($qv <= 3) and ($qv = $qv_45d)) {
								$oferta_sugerida = 0;
							}
                            */
							
							// si hay mucho (secundario) -> libre en relación a las ventas de primario voy aumentando el precio
							if($antiguedad <= 365) {
								if(($slot == "primario") and ($libre >= 4) and ($libre <= 8)) {
									$oferta_sugerida = $oferta_sugerida * (1+($libre/$qv));
				$control_individual .= "<br /> ant < 365d, controlo libres: <br/> es pri y 4-8 libres, precio * " . round(1+(($libre/$qv)),2) . " queda en: " . round($oferta_sugerida,2);
								}
							/**** 2019-11-27 quito esto porque no le encuentro sentido, en la de arriba ya defino un aumento de precio al pri cuando hay mucho libre 
								if($libre <= 10) {
									$elev2=1;} 
								else {
									$elev2=($libre/10);
								}*/
								if(($slot == "primario") and ($libre > 8)) {
									$oferta_sugerida = $oferta_sugerida * (pow(1.04,($libre/8)));
				$control_individual .= "<br /> ant < 365d, controlo libres: <br/> es pri y +8 libres, precio * " . round((pow(1.04,($libre/10))),2) . " queda en: " . round($oferta_sugerida,2);
									
								}	
								
							}

							// si hay mas de 10 secundarios libres voy aumentando el precio al primario exponencialmente                                         
                            
                            // redondeo la oferta
                            // $oferta_sugerida = (round($oferta_sugerida, 0)/25);
                            // $oferta_sugerida = (ceil($oferta_sugerida)*25);
							// actualizado para pasar a US
							$oferta_sugerida = round($oferta_sugerida, 2);
					$control_individual .= "<br /> Sug redondeado: " . $oferta_sugerida;

                            $mensajes .= "<tr><td>[" . $ID . "]</td><td>" . str_replace('-', ' ', $producto).  " " . $slot . "</td><td> Reg: " . $precio_regular . "</td><td>Bas: " . $precio_base . "</td><td>Sal: " . $sale_price . "</td><td>Sug: " . $oferta_sugerida . "</td><td>Lib:" . $libre . "</td><td> // </td><td>qvP:" . $qvp . "</td><td>qvS:" . $qvs ."</td><td>qvP_45d:" . $qvp_45d . "</td><td>qvS_45d:" . $qvs_45d . "</td><td>Stk:" . $Q_stk . "</td><td>qv_45d/Stk: " . $divi . "</td><td>C_mod:". round($costo_usd,2)  . " de " . round($costo_usd_original) ."</td><td>Ant:" . $antiguedad . "</td></tr>"; 
                            
                            
                            if(($Q_stk <= 1) or (($Q_stk/$qv_45d) < 0.10)) {
					$control_individual .= "<br /> stk <= 1 o < al 10% de lo vendido en 45d, quito oferta";
								$oferta_sugerida = 0;
							}
					
                            

                            if($oferta_sugerida >= ($precio_base * 0.963)) {
					$control_individual .= "<br /> Sug es casi lo mismo que precio base, quito oferta";
								$oferta_sugerida = 0;
							}
							
					
                            if($oferta_sugerida == 0) { // si no hay precio de oferta 
                                if($sale_price !== ""){  // y el producto tiene actualmente un precio de oferta, le quito
                                    $_sale_price = DB::table('cbgw_postmeta')
                                    ->where('post_id',$ID)
                                    ->where('meta_key','_sale_price')->first(); //encuentro si existe el meta key sale_price
                                    
                                    if ($_sale_price) {
                                        DB::table('cbgw_postmeta')
                                        ->where('post_id',$ID)
                                        ->where('meta_key','_sale_price')
                                        //->where(DB::raw("(meta_key='_price' or meta_key='_regular_price')"))
                                        ->update([
                                            'meta_value' => '' // quito el precio de oferta actual
                                        ]);
                                    } else {
                                        $data['post_id'] = $ID;
                                        $data['meta_key'] = '_sale_price';
                                        $data['meta_value'] = '';
    
                                        DB::table('cbgw_postmeta')->insert($data); // si no existe el meta key sale_price lo creo
                                    }

                                        $mensajes .= "[" . $ID . "] " . str_replace('-', ' ', $producto).  " " . $slot . " REMOVIDA<br><br>";
                                    }
                                
                                } else {

                                if(($oferta_sugerida <= ($precio_base * 0.963)) and ($Q_stk >= 1)) {
                                    if($sale_price == "") { $sale_price = 1; }
                                    if((($oferta_sugerida >= ($sale_price * 1.05)) or ($oferta_sugerida <= ($sale_price * 0.95))) and $oferta_sugerida > 1){
                                        $_sale_price = DB::table('cbgw_postmeta')
                                        ->where('post_id',$ID)
                                        ->where('meta_key','_sale_price')->first();
                                        
                                        if ($_sale_price) { // si existe el sale_price lo actualizo
                                            DB::table('cbgw_postmeta')
                                            ->where('post_id',$ID)
                                            ->where('meta_key','_sale_price')
                                            //->where(DB::raw("(meta_key='_price' or meta_key='_regular_price')"))
                                            ->update([
                                                'meta_value' => $oferta_sugerida
                                            ]);
                                        } else { // si no existe lo creo
                                            $data['post_id'] = $ID;
                                            $data['meta_key'] = '_sale_price';
                                            $data['meta_value'] = $oferta_sugerida;
        
                                            DB::table('cbgw_postmeta')->insert($data);
                                        }
										
										$variacion = round((($oferta_sugerida / $sale_price) - 1) * 100,2);
										
										if ($variacion >= 0) {
											$color= "green";
										} else {
											$color= "red";
										}
										
										$mensajes .= "[" . $ID . "] " . str_replace('-', ' ', $producto).  " " . $slot . " APLICADO de " . $sale_price . " a " . $oferta_sugerida . " <span id='" . $ID . "' style='color:" . $color . ";'> (" . $variacion . " %)</span> " . " <br><br>";

                                        }
                                    }
                                }
                            }
						$control_individual .= "<p> .... </p>";
                    }
					
					$control_individual .= "<p>..............fin che</p>";
					
                    $mensajes .= "</table>" . $control_individual;
					

                    DB::commit();

                    /*\Helper::messageFlash('Configuraciones',"Stock web PS4 automatizados correctamente.");

                    return redirect()->back();*/
                    return $mensajes ;
                } catch (Exception $e) {
                    DB::rollback();
                    // return redirect()->back()->withErrors(['Ha ocurrido un error inesperado en el proceso.']);
                    return 'Ha ocurrido un error inesperado en el proceso.';
                }

                break;
        }
    }

    private function getConfiguraciones() {
        return DB::table('configuraciones')->where('ID',1)->first();
    }
}
