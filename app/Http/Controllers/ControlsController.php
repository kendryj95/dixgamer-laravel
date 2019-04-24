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
        $oferta_fortnite = DB::table('configuraciones')->where('ID',1)->value('oferta_fortnite');
        $cuentas_excluidas = DB::table('configuraciones')->where('ID',1)->value('cuentas_excluidas');
        return view('config.general', compact('oferta_fortnite','cuentas_excluidas'));
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
                $data['cuentas_excluidas'] = $request->cuentas_excluidas;

                DB::table('configuraciones')->where('ID', 1)->update($data);
                \Helper::messageFlash('Configuraciones','Cuentas excluidas en PS3 Resetear actualizada.');
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

        if ($filter['agrupar'] == 'dia') {
            $groupBy = "DATE(ventas.Day)";
        } elseif ($filter['agrupar'] == 'semana') {
            $groupBy = "WEEK(ventas.Day)";
        } elseif ($filter['agrupar'] == 'mes') {
            $groupBy = "MONTH(ventas.Day)";
        }

        $datos->groupBy(DB::raw($groupBy))
        ->orderBy(DB::raw($groupBy),'ASC');

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
}
