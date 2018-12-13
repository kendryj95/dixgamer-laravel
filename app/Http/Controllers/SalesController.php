<?php

namespace App\Http\Controllers;

use App\Sales;
use Illuminate\Http\Request;
use App\User;
use App\Customer;
use App\Stock;
use App\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    //
    public function index(Request $request){

        $datos = $this->QueryIndex();

        return view('sales.sales_list')->with(['datos' => $datos]);
    }

    public function addManualSale(Request $request, $consola, $titulo, $slot){

        $colname_rsCON = $consola;
        $colname_rsTIT = $titulo;
        $colname_rsSlot = $slot;


        $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);

        /*if(! count($row_rsSTK) > 0) {
            exit('No hay stock del Juego:'. $titulo ($consola));
        }*/

        if(!is_array($row_rsSTK)) {
            return redirect()->back()->withErrors(["No hay stock del Juego: $titulo ($consola)"]);
        } else {
          $stk_ID = $row_rsSTK[0]->ID_stk;
        }

        $sqlAA = DB::select("SELECT CONCAT('[ ',ID,' ] ',nombre,' ',apellido,' - ',email) as nombre FROM clientes ORDER BY ID DESC");

        $rowsAA = [];
        for ($i=0;$i < count($sqlAA); $i++){
            $rowsAA[] = $sqlAA[$i]->nombre;
        }



        $row_rsClientes = DB::select("SELECT clientes.ID, apellido, nombre, vta.Q_Vta, email
                FROM clientes
                LEFT JOIN
                (SELECT clientes_id, COUNT(*) AS Q_Vta FROM ventas GROUP BY clientes_id) AS vta
                ON clientes.ID = vta.clientes_id
                ORDER BY ID DESC");

        $row_rsStock2 = DB::select(DB::raw("SELECT SUM(precio) as precio, ventas.ID as ventas_ID, stock.ID as stock_ID, titulo, consola, slot FROM ventas_cobro 
                    LEFT JOIN ventas ON ventas_cobro.ventas_id=ventas.ID
                    LEFT JOIN stock ON ventas.stock_id=stock.ID
                    WHERE consola='".$consola."' and titulo='".$titulo."' and slot='".$slot."'
                    GROUP BY ventas_id
                    ORDER BY ventas_ID DESC"));

        $stock_id = $row_rsSTK[0]->ID_stk;


        return view('sales.add_sale')->with([
            'colname_rsCON'     => $consola,
            'colname_rsTIT'     => $titulo,
            'colname_rsSlot'    => $slot,
            'row_rsClientes'    => $row_rsClientes,
            'rowsAA'            => $rowsAA,
            'row_rsStock2'      => $row_rsStock2,
            'stk_ID'            => $stk_ID
        ]);
    }

    public function saveManualSale(Request $request){

        $row_rsSTK = Stock::StockDisponible($request->consola,$request->titulo, $request->slot);

        if(!is_array($row_rsSTK)) {
            return redirect()->back()->withErrors(["No hay stock del Juego: $titulo ($consola)"]);
        } else {
          $stk_ID = $row_rsSTK[0]->ID_stk;
        }

        // dd($row_rsSTK);

        $insert = DB::table('ventas')->insertGetId([
            'clientes_id'   => $request->clientes_id,
            'stock_id'      => $stk_ID,
            'order_item_id' => $request->order_item_id,
            'cons'          => $request->consola,
            'slot'          => $request->slot,
            'medio_venta'   => $request->medio_venta,
            'order_id_ml'   => $request->order_id_ml,
            'order_id_web'   => $request->order_id_web,
            'estado'        => $request->estado,
            'Day'           => \Carbon\Carbon::now('America/New_York'),
            'Notas'         => $request->Notas,
            'usuario'       => session()->get('usuario')->Nombre

        ]);

        $venta_ID = $insert;

        $insert2 = DB::table('ventas_cobro')->insertGetId([
            'ventas_id'     => $venta_ID,
            'medio_cobro'   => $request->medio_cobro,
            'ref_cobro'     => $request->ref_cobro,
            'precio'        => $request->precio,
            'comision'      => $request->comision,
            'Day'           => \Carbon\Carbon::now('America/New_York'),
            'Notas'         => $request->Notas,
            'usuario'       => session()->get('usuario')->Nombre
        ]);

        if ($request->medio_cobro == 'Banco') {
            $data['cobros_id'] = $insert2;
            DB::table('ventas_cobro_bancos')->insert($data);
        }

        return redirect(url('clientes',$request->clientes_id));
    }



    public function QueryIndex(){
        $data = DB::select('SELECT ventas.ID AS ID_ventas, 
                                  clientes_id, 
                                  stock_id, 
                                  slot, 
                                  medio_venta, 
                                  medio_cobro, 
                                  precio, 
                                  comision, 
                                  ventas.Notas AS ventas_Notas, 
                                  ventas.Day as ventas_Day, 
                                  apellido, 
                                  nombre, 
                                  titulo, 
                                  consola 
                                  FROM ventas
                            LEFT JOIN (select ventas_id, medio_cobro, ref_cobro, sum(precio) as precio, sum(comision) as comision FROM ventas_cobro GROUP BY ventas_id) as ventas_cobro ON ventas.ID = ventas_cobro.ventas_id
                            LEFT JOIN clientes ON ventas.clientes_id = clientes.ID
                            LEFT JOIN stock ON ventas.stock_id = stock.ID ORDER BY ventas.ID DESC');
        return $data;
    }

    public function salesInsertWeb($oii, $titulo, $consola, $slot = '')
    {
        $link_PS = '';

        $existe_OII = DB::table('ventas')
                        ->select(
                            'order_item_id',
                            'clientes_id',
                            'usuario'
                        )
                        ->where('order_item_id',$oii)->first();

        if ($existe_OII) {
            $asignador = $existe_OII->usuario;
            $cliente = $existe_OII->clientes_id;

            return redirect("web/sales/list?r=_exist&c=$cliente&u=$asignador");
        }

        $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);

        $venta = DB::table('cbgw_woocommerce_order_items AS wco')
                            ->select(
                              'wco.order_item_id',
                              'wco.order_id',
                              'p.ID as post_id',
                              'p.post_status as estado',
                              DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_billing_email' AND post_id=p.ID) AS email"),
                              DB::raw("
                                (SELECT 
                                CASE
                                WHEN meta_value LIKE '%_card%' OR meta_value LIKE '%pago-basic' THEN 'MP - Tarjeta'  
                                WHEN meta_value LIKE 'account_money%' THEN 'MP'  
                                WHEN meta_value LIKE 'ticket_%' OR meta_value LIKE '%pago-ticket' THEN 'MP - Ticket'  
                                WHEN meta_value = 'bacs' THEN 'Banco'
                                WHEN meta_value = 'fondos' THEN 'Fondos'
                                ELSE 'No se encontró medio_pago'
                                END  
                                FROM cbgw_postmeta 
                                WHERE meta_key='_payment_method' AND post_id=p.ID) AS medio_pago"),
                              DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_Mercado_Pago_Payment_IDs' AND post_id=p.ID) AS ref_cobro"),
                              DB::raw("(SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value, 'payment_id=', -1),'&',1) FROM cbgw_postmeta WHERE meta_key='_transaction_details_ticket' AND post_id=p.ID) AS ref_cobro_2"),
                              DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_transaction_id' AND post_id=p.ID) AS ref_cobro_3"),
                              DB::raw("(SELECT meta_value FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_qty' AND order_item_id=wco.order_item_id) AS _qty"),
                              DB::raw("(SELECT meta_value FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_line_total' AND order_item_id=wco.order_item_id) AS precio"),
                              DB::raw("(SELECT meta_value FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='pa_slot' AND order_item_id=wco.order_item_id) AS slot"),
                              DB::raw("(SELECT meta_value FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_product_id' AND order_item_id=wco.order_item_id) AS _product_id"),
                              DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='user_id_ml' AND post_id=p.ID) AS user_id_ml"),
                              DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='order_id_ml' AND post_id=p.ID) AS order_id_ml")
                            )
                            ->leftjoin('cbgw_posts AS p','wco.order_id','=','p.ID')
                            ->leftjoin('cbgw_postmeta AS pm','wco.order_id','=','pm.post_id')
                            ->leftjoin('cbgw_woocommerce_order_itemmeta AS wcom','wco.order_item_id','=','wcom.order_item_id')
                            ->where('wco.order_item_id',$oii)
                            ->where(DB::raw("p.post_status = 'wc-processing' or p.post_status = 'wc-completed'"))
                            ->groupBy('wco.order_item_id')
                            ->orderBy('wco.order_item_id','DESC')
                            ->first();

        if (!is_array($row_rsSTK)) {
            $producto_catalogo = $venta->_product_id;

            $rsLink_PS = DB::table('cbgw_postmeta')
                            ->select(
                                DB::raw("GROUP_CONCAT(meta_value) as meta_value")
                            )
                            ->where('post_id', $producto_catalogo)
                            ->where('meta_key', 'link_ps')
                            ->groupBy('post_id')
                            ->first();

            $link_PS = '';
            
            if ($rsLink_PS) {
                
                $link_PS = $rsLink_PS->meta_value;
            }


        }

        $email_pedido = $venta->email;

        $existEmailCliente = DB::table('clientes')
                                ->select(
                                    'ID',
                                    'nombre',
                                    'apellido',
                                    'email',
                                    'auto'
                                )
                                ->where('email', $email_pedido)
                                ->first();

        if (!$existEmailCliente) {
            return redirect()->back()->withErrors(["Este email no corresponde a ningún cliente de la base de datos: $email_pedido"]);
        }


        if (!is_array($row_rsSTK)) {
            return view('sales.salesInsertWeb', [
                "row_rsSTK" => $row_rsSTK,
                "venta" => $venta,
                "consola" => $consola,
                "titulo" => $titulo,
                "slot" => $slot,
                "clientes" => $existEmailCliente,
                "linkPS" => $link_PS
            ]);
        } else {
            $date = date('Y-m-d H:i:s');
            $order_id_ml = '';
            $slot_def = '';
            $medio_cobro = '';
            $ref_cobro = '';
            $medio_venta = '';
            $multiplo = '';

            if ($venta) {
                $clientes_id = $existEmailCliente->ID;
                $stock_id = $row_rsSTK[0]->ID_stk;
                $order_item_id = $venta->order_item_id;

                $cons = $row_rsSTK[0]->consola;

                //Si es una vta de ps4 o plus slot por ML asigno el slot desde el parametro GET
                //if ((($row_rsClient['user_id_ml']) && ($row_rsClient['user_id_ml'] != "")) && (($cons === "ps4") or ($row_rsClient['producto'] === "plus-12-meses-slot"))): $slot = ucwords($colname_rsSlot);
                //Si es una vta de ps4 o plus slot que NO ES de ML asigno el slot desde la consulta SQL
                //elseif --> antes de escapar el IF anterior que iba primero

                if (($cons === "ps4") or ($row_rsSTK[0]->titulo === "plus-12-meses-slot")): $slot_def = ucwords($slot); $estado = "pendiente";
                elseif ($cons === "ps3"): $slot_def = "Primario"; $estado = "pendiente";

                //Si no cumple con ninguno de los parametros anteriores seguramente se trata de una venta de Gift Card y el slot se define en "No"
                else: $slot_def = "No"; $estado = "listo";
                endif;

                // SI ES VENTA DE ML DEFINO LOS VALORS CORRECTOS
                if (($venta->user_id_ml) && ($venta->user_id_ml != "")){ 
                $medio_venta = "MercadoLibre";
                $order_id_ml = $venta->order_id_ml;
                $medio_cobro = $venta->medio_pago;
                    /*if (strpos($venta->medio_pago_2, '_card') !== false): $medio_cobro = "Mercado Pago - Tarjeta";
                    // 2018-05-19 --> no estoy seguro si el medio de pago sale con la palabra ticket cuando es ticket
                    elseif (strpos($venta->medio_pago_2, 'ticket') !== false): $medio_cobro = "Mercado Pago - Ticket";
                    else: $medio_cobro = "Mercado Pago"; endif;*/
                $ref_cobro = $venta->ref_cobro_3;
                $multiplo = "0.12";
                } else { // SI ES VENTA WEB DEFINO LOS VALORES CORRECTOS
                //2017-08 Paso el ref_cobro_2 como primer alternativa para ver si se reducen los errores de REF DE COBRO WEB
                $medio_venta = "Web";
                $medio_cobro = ucwords(strtolower($venta->medio_pago));
                    if (($venta->ref_cobro_2) && ($venta->ref_cobro_2 != "")): $ref_cobro = $venta->ref_cobro_2;
                    elseif (($venta->ref_cobro) && ($venta->ref_cobro != "")): $ref_cobro = $venta->ref_cobro;
                    endif;
                    if (strpos($venta->medio_pago, 'Banco') !== false): $multiplo = "0.00";
                    elseif (strpos($venta->medio_pago, 'MP') !== false): $multiplo = "0.0538";
                    elseif (strpos($venta->medio_pago, 'Tarjeta') !== false): $multiplo = "0.0538";
                    elseif (strpos($venta->medio_pago, 'Ticket') !== false): $multiplo = "0.0538";
                    elseif (strpos($venta->medio_pago, 'PayPal') !== false): $multiplo = "0.99"; // TODAVIA NO SE LA TASA DE PAYPAL AVERIGUAR
                    else: $comision = "0.99"; endif; // HAGO ESTO PARA DETECTAR SI HAY UN ERROR FACILMENTE
                }

                $order_id_web = $venta->order_id;
                $precio = $venta->precio;
                $comision = ($multiplo * $venta->precio);

                $data = [];
                $data['clientes_id'] = $clientes_id;
                $data['stock_id'] = $stock_id;
                $data['order_item_id'] = $order_item_id;
                $data['cons'] = $cons;
                $data['slot'] = $slot_def;
                $data['medio_venta'] = $medio_venta;
                
                if(($order_id_ml) && ($order_id_ml != "")){

                    $data['order_id_ml'] = $order_id_ml;
                }
                $data['order_id_web'] = $order_id_web;
                $data['estado'] = $estado;
                $data['Day'] = $date;
                $data['usuario'] = session()->get('usuario')->Nombre;

                DB::beginTransaction();

                try {
                    DB::table('ventas')->insert($data);
                    $ventaid = DB::getPdo()->lastInsertId();

                    $data = [];
                    $data['ventas_id'] = $ventaid;
                    $data['medio_cobro'] = $medio_cobro;
                    if ("" !== trim($ref_cobro)) {
                        $data['ref_cobro'] = $ref_cobro;
                    }
                    $data['precio'] = $precio;
                    $data['comision'] = $comision;
                    $data['Day'] = $date;
                    $data['usuario'] = session()->get('usuario')->Nombre;

                    DB::table('ventas_cobro')->insert($data);

                    DB::commit();

                    echo "<script>window.top.location.href='".url('clientes',$clientes_id)."'</script>";


                } catch (Exception $e) {
                    DB::rollback();
                    return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor vuelva a intentarlo.']);
                }

            }
        }

    }

    public function verificarOrderItemId($oii, $clientes_id)
    {
        $status = 0; // Status por default por si no existe el oii en la tabla "cbgw_woocommerce_order_items"
        $datosOii = DB::table('cbgw_woocommerce_order_items')
                        ->where('order_item_id', $oii)->first();
        $data = [];

        if ($datosOii) { // Si existe el Oii en la tabla
            $existInVentas = DB::table('ventas')
                                ->where('order_item_id', $oii)->first();

            if ($existInVentas) { // ¿Existe en la tabla Ventas?
                if ($existInVentas->clientes_id == $clientes_id) { // Verificar si el oii pertenece al cliente que se le va a asignar.
                    $status = 2; // Status para devolver los datos
                    $data['datosOii'] = $datosOii;
                    $data['status'] = $status;
                } else {
                    $status = 1; // Status para avisar que ese oii pertenece a otro cliente.
                    $data['existInVentas'] = $existInVentas;
                    $data['status'] = $status;
                }
            } else {
                $status = 2; // Status para devolver los datos
                $data['datosOii'] = $datosOii;
                $data['status'] = $status;
            }
        }

        echo json_encode($data);
    }


}
