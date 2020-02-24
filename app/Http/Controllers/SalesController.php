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
use Schema;

class SalesController extends Controller
{
    //
    public function index(Request $request){

        // Ventas con filtro
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $datos = Sales::getData()->salesByCustomColumn($obj)->orderBy('ventas.ID','DESC')->paginate(50);
        // $datos = $this->QueryIndex();

        $columns = Schema::getColumnListing('ventas');
        array_push($columns, "titulo");

        return view('sales.sales_list')->with(['datos' => $datos, 'columns' => $columns]);
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
            'Day'           => date('Y-m-d H:i:s'),
            'Notas'         => $request->Notas,
            'usuario'       => session()->get('usuario')->Nombre
        ]);

        /*if ($request->medio_cobro == 'Banco') { // COMENTADO EL DÍA 12/03/2019 POR ORDEN DE VICTOR
            $data['cobros_id'] = $insert2;
            DB::table('ventas_cobro_bancos')->insert($data);
        }*/

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

    public function salesInsertWeb(Request $request,$oii, $titulo, $consola, $slot = '')
    {
        $link_PS = '';
        $giftConStock = false;
        $data_gifts = [];

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
                                meta_value 
                                FROM cbgw_postmeta 
                                WHERE meta_key='_payment_method' AND post_id=p.ID) AS _payment_method"),
                              DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_Mercado_Pago_Payment_IDs' AND post_id=p.ID LIMIT 1) AS ref_cobro"),
                              DB::raw("(SELECT SUBSTRING_INDEX(SUBSTRING_INDEX(meta_value, 'payment_id=', -1),'&',1) FROM cbgw_postmeta WHERE meta_key='_transaction_details_ticket' AND post_id=p.ID LIMIT 1) AS ref_cobro_2"),
                              DB::raw("(SELECT meta_value FROM cbgw_postmeta WHERE meta_key='_transaction_id' AND post_id=p.ID LIMIT 1) AS ref_cobro_3"),
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

        if ($venta) { // Si obtiene el registro de las tablas de woocommerce, de lo contrario se mostrará una alerta de error.
           if (!is_array($row_rsSTK)) {

               if (isset($request->gift) && $request->gift == 'si') { // Validaré si el producto es una gift
                   $valor_gift = explode("-", $titulo)[2];

                   $data_gifts = $this->giftCardOrder($valor_gift);
                   
               }

               if (count($data_gifts) > 0) {
                   $giftConStock = true;
               } else {
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


           if (!is_array($row_rsSTK) && !$giftConStock) {
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
               
               $clientes_id = $existEmailCliente->ID;
               $medio_cobro = '';
               $ref_cobro = '';
               $multiplo = '';

               if ($venta) {

                   // Defino el multiplo de la comisión por defecto de MP que es 5,38 %
                   $multiplo = "0.0538";
                   
                   // DEFINO EL MEDIO DE COBRO CON LAS OPCIONES PREDEFINIDAS 14/12/2018
                   if (strpos($venta->_payment_method, 'card') !== false): $medio_cobro = "MP - Tarjeta";
                   elseif (strpos($venta->_payment_method, 'account') !== false): $medio_cobro = "MP - Saldo";
                   elseif (strpos($venta->_payment_method, 'basic') !== false): $medio_cobro = "MP";
                   elseif (strpos($venta->_payment_method, 'ticket') !== false): $medio_cobro = "MP - Ticket";
                   elseif (strpos($venta->_payment_method, 'atm') !== false): $medio_cobro = "MP - Ticket";
                   elseif (strpos($venta->_payment_method, 'digital') !== false): $medio_cobro = "MP"; // mercado credito
                       
                   // si es por banco cambio multiplo de comisión a 0%
                   elseif (strpos($venta->_payment_method, 'bacs') !== false): $medio_cobro = "Banco"; $multiplo = "0.00";
                   elseif (strpos($venta->_payment_method, 'yith') !== false): $medio_cobro = "Fondos"; $multiplo = "0.00";
                   // si es paypal va 7%
                   elseif (strpos($venta->_payment_method, 'paypal') !== false): $medio_cobro = "PayPal"; $multiplo = "0.07";
                   else: $medio_cobro = "No encontrado";
                   endif;
                   

                   // SI ES VENTA DE ML DEFINO LOS VALORS CORRECTOS
                   if (($venta->user_id_ml) && ($venta->user_id_ml != "")){ 
                   $ref_cobro = $venta->ref_cobro_3;
                   } else { // SI ES VENTA WEB DEFINO LOS VALORES CORRECTOS
                   //2017-08 Paso el ref_cobro_2 como primer alternativa para ver si se reducen los errores de REF DE COBRO WEB
                       if (($venta->ref_cobro_2) && ($venta->ref_cobro_2 != "")): $ref_cobro = $venta->ref_cobro_2;
                       elseif (($venta->ref_cobro) && ($venta->ref_cobro != "")): $ref_cobro = $venta->ref_cobro;
                       elseif (($venta->ref_cobro_3) && ($venta->ref_cobro_3 != "")): $ref_cobro = $venta->ref_cobro_3;
                       endif;
                       
                   }
                   
                   $precio = $venta->precio;
                   $comision = ($multiplo * $venta->precio);
                   $precio_original = $precio;
                   $comision_original = $comision;

                   DB::beginTransaction();

                   try {

                       if (!$giftConStock) { // Si el producto no es una gift quiere decir esa variable.
                           $data = $this->dataSale($venta, $row_rsSTK, $existEmailCliente, $slot);
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
                           $data['Day'] = date('Y-m-d H:i:s');
                           $data['usuario'] = session()->get('usuario')->Nombre;

                           DB::table('ventas_cobro')->insert($data);
                       } else {
                           foreach ($data_gifts as $value) { // Varias gifts se van a registrar

                               $row_rsSTK = Stock::StockDisponible($value->consola,$value->titulo, '');
                               $partes = count($data_gifts);
                               $precio = $precio_original / $partes;
                               $comision = $comision_original / $partes;

                               $data = $this->dataSale($venta, $row_rsSTK, $existEmailCliente);
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
                               $data['Day'] = date('Y-m-d H:i:s');
                               $data['usuario'] = session()->get('usuario')->Nombre;

                               DB::table('ventas_cobro')->insert($data);
                           }
                       }

                       DB::commit();

                       echo "<script>window.top.location.href='".url('clientes',$clientes_id)."'</script>";


                   } catch (Exception $e) {
                       DB::rollback();
                       return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor vuelva a intentarlo.']);
                   }

               }
           } 
        } else {
            return redirect()->back()->withErrors(['Ha ocurrido un error al intentar obtener los datos de la compra.']);
        }

    }

    private function dataSale($venta, $row_rsSTK, $existEmailCliente, $slot = '')
    {
        $date = date('Y-m-d H:i:s');
        $clientes_id = $existEmailCliente->ID;
        $stock_id = $row_rsSTK[0]->ID_stk;
        $order_item_id = $venta->order_item_id;
        $order_id_ml = '';
        $slot_def = '';
        $medio_venta = '';

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
        } else { // SI ES VENTA WEB DEFINO LOS VALORES CORRECTOS
        $medio_venta = "Web";
        }

        $order_id_web = $venta->order_id;

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

        return $data;
    }

    private function giftCardOrder($gift)
    {
        $data_gifts = [];
        switch ($gift) {
            case '20':
                $gifts = [
                    "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', ''),
                    "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', '')
                ];
                if (is_array($gifts["g_20"])) { // Si hay gift de 20 hay cantidad en stock.
                    if ($gifts["g_20"][0]->Q_Stock > 0) { // Si hay gift de 20 hay cantidad en stock.
                        $data_gifts = [
                            $gifts["g_20"][0]
                        ];  
                    }  
                } elseif (is_array($gifts["g_10"])) { // Si hay más de 1 gift de 10 usd
                    if ($gifts["g_10"][0]->Q_Stock > 1) { // Si hay más de 1 gift de 10 usd
                        ## Se registrará 2 veces para 20 usd.
                        for ($i=0; $i <= 1 ; $i++) { 
                            $data_gifts[] = $gifts["g_10"][0];
                        }
                    }
                }
                break;
              case '30':
                $gifts = [
                    "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', ''),
                    "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', '')
                ];
                if (is_array($gifts["g_10"]) && is_array($gifts["g_20"])) { // Si hay gift de 10 y de 20 hay cantidad en stock.
                    if ($gifts["g_10"][0]->Q_Stock > 0 && $gifts["g_20"][0]->Q_Stock > 0) { // Si hay gift de 10 y de 20 hay cantidad en stock.
                        $data_gifts = [
                            $gifts["g_10"][0],
                            $gifts["g_20"][0]
                        ];  
                    } 
                } elseif (is_array($gifts["g_10"])) { // Si hay más de 2 gift de 10 usd
                    if ($gifts["g_10"][0]->Q_Stock > 2) { // Si hay más de 2 gift de 10 usd
                         ## Se registrará 3 veces para 30 usd.
                         for ($i=0; $i <= 2 ; $i++) { 
                             $data_gifts[] = $gifts["g_10"][0];
                         }
                     }
                }
                break;
            
            case '35':
                $gifts = [
                    "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', ''),
                    "g_25" => Stock::StockDisponible('ps','gift-card-25-usd', '')
                ];
                if (is_array($gifts["g_10"]) && is_array($gifts["g_25"])) { // Si hay gift de 10 y de 25 hay cantidad en stock.
                    if ($gifts["g_10"][0]->Q_Stock > 0 && $gifts["g_25"][0]->Q_Stock > 0) { // Si hay gift de 10 y de 25 hay cantidad en stock.
                        $data_gifts = [
                            $gifts["g_10"][0],
                            $gifts["g_25"][0]
                        ];
                    }
                } 
                break;

            case '40':
                $gifts = [
                    "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', ''),
                    "g_40" => Stock::StockDisponible('ps','gift-card-40-usd', ''),
                    "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', '')
                ];
                if (is_array($gifts["g_40"])) { // Si hay más de 1 gift de 40 en stock.
                    if ($gifts["g_40"][0]->Q_Stock > 0) { // Si hay más de 1 gift de 40 en stock.
                        $data_gifts = [
                            $gifts["g_40"][0]
                        ];
                    }
                }elseif (is_array($gifts["g_20"])) { // Si hay más de 1 gift de 20 en stock.
                    if ($gifts["g_20"][0]->Q_Stock > 1) { // Si hay más de 1 gift de 20 en stock.
                      for ($i=0; $i <= 1 ; $i++) { 
                          $data_gifts[] = $gifts["g_20"][0];
                      }
                    } elseif (is_array($gifts["g_10"])) { // Si hay más de 3 gift de 10 en stock.
                        if ($gifts["g_10"][0]->Q_Stock > 3) { // Si hay más de 3 gift de 10 en stock.
                            for ($i=0; $i <= 3 ; $i++) { 
                                $data_gifts[] = $gifts["g_10"][0];
                            }
                        }
                    }
                } elseif (is_array($gifts["g_10"])) { // Si hay más de 3 gift de 10 en stock.
                    if ($gifts["g_10"][0]->Q_Stock > 3) { // Si hay más de 3 gift de 10 en stock.
                        for ($i=0; $i <= 3 ; $i++) { 
                            $data_gifts[] = $gifts["g_10"][0];
                        }
                    }
                } 
                break;
            case '45':
                $gifts = [
                    "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', ''),
                    "g_25" => Stock::StockDisponible('ps','gift-card-25-usd', '')
                ];
                if ( is_array($gifts["g_20"]) && is_array($gifts["g_25"])) { // Si hay gift de 20 y de 25 hay cantidad en stock.
                    if ($gifts["g_20"][0]->Q_Stock > 0 && $gifts["g_25"][0]->Q_Stock > 0) { // Si hay gift de 20 y de 25 hay cantidad en stock.
                        $data_gifts = [
                            $gifts["g_20"][0],
                            $gifts["g_25"][0]
                        ];
                    } 
                } 
                break;
            case '50':
                $gifts = [
                    "g_50" => Stock::StockDisponible('ps','gift-card-50-usd', ''),
                    "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', ''),
                    "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', '')
                ];
                if ( is_array($gifts["g_50"])) { 
                    if ($gifts["g_50"][0]->Q_Stock > 0) { 
                        $data_gifts = [
                            $gifts["g_50"][0]
                        ];
                    } 
                } elseif ( is_array($gifts["g_20"]) && is_array($gifts["g_10"])) { 
                    if ($gifts["g_20"][0]->Q_Stock > 1 && $gifts["g_10"][0]->Q_Stock > 0) { 
                        $data_gifts = [
                            $gifts["g_20"][0],
                            $gifts["g_20"][0],
                            $gifts["g_10"][0]
                        ];
                    } elseif (is_array($gifts["g_10"])) { 
                        if ($gifts["g_10"][0]->Q_Stock > 0) { 
                            for ($i=0; $i <= 4 ; $i++) { 
                              $data_gifts[] = $gifts["g_10"][0];
                            }
                        } 
                    }
                } elseif (is_array($gifts["g_10"])) { 
                    if ($gifts["g_10"][0]->Q_Stock > 0) { 
                        for ($i=0; $i <= 4 ; $i++) { 
                          $data_gifts[] = $gifts["g_10"][0];
                        }
                    } 
                }
                break;

            case '55':
                $gifts = [
                    "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', ''),
                    "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', ''),
                    "g_25" => Stock::StockDisponible('ps','gift-card-25-usd', '')
                ];
                if (is_array($gifts["g_10"]) && is_array($gifts["g_20"]) && is_array($gifts["g_25"])) { // Si gift de 10 y de 20 y de 25 hay cantidad en stock.
                    if ($gifts["g_10"][0]->Q_Stock > 0 && $gifts["g_20"][0]->Q_Stock > 0 && $gifts["g_25"][0]->Q_Stock > 0) { // Si gift de 10 y de 20 y de 25 hay cantidad en stock.
                        $data_gifts = [
                            $gifts["g_10"][0],
                            $gifts["g_20"][0],
                            $gifts["g_25"][0]
                        ];
                    }
                } 
                break;
            case '60':

              $gifts = [
                  "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', ''),
                  "g_50" => Stock::StockDisponible('ps','gift-card-50-usd', ''),
                  "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', '')
              ];
              if (is_array($gifts["g_50"]) && is_array($gifts["g_10"])) { // Si hay más de 1 gift de 50 y de 10 en stock.
                  if ($gifts["g_50"][0]->Q_Stock > 0 && $gifts["g_10"][0]->Q_Stock > 0) { // Si hay más de 1 gift de 50 y de 10 en stock.
                      $data_gifts = [
                          $gifts["g_10"][0],
                          $gifts["g_50"][0]
                      ];
                  }
              } elseif (is_array($gifts["g_20"])) { // Si hay más de 2 gift de 20 en stock.
                  if ($gifts["g_20"][0]->Q_Stock > 2) { // Si hay más de 2 gift de 20 en stock.
                      for ($i=0; $i <= 2 ; $i++) { 
                          $data_gifts[] = $gifts["g_20"][0];
                      }
                  }
              }
              break;
            case '70':

              $gifts = [
                  "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', ''),
                  "g_20" => Stock::StockDisponible('ps','gift-card-20-usd', ''),
                  "g_50" => Stock::StockDisponible('ps','gift-card-50-usd', '')
              ];
              if (is_array($gifts["g_50"]) && is_array($gifts["g_20"])) { // Si hay más de 1 gift de 50 y de 10 en stock.
                  if ($gifts["g_50"][0]->Q_Stock > 0 && $gifts["g_20"][0]->Q_Stock > 0) { // Si hay más de 1 gift de 50 y de 10 en stock.
                      $data_gifts = [
                          $gifts["g_20"][0],
                          $gifts["g_50"][0]
                      ];
                  }
              } elseif (is_array($gifts["g_50"]) && is_array($gifts["g_10"])) { // Si hay más de 1 gift de 50 y de 10 en stock.
                  if ($gifts["g_50"][0]->Q_Stock > 0 && $gifts["g_10"][0]->Q_Stock > 0) { // Si hay más de 1 gift de 50 y de 10 en stock.
                      $data_gifts = [
                          $gifts["g_10"][0],
                          $gifts["g_10"][0],
                          $gifts["g_50"][0]
                      ];
                  }
              }  

              break;
            case '75':

              $gifts = [
                  "g_50" => Stock::StockDisponible('ps','gift-card-50-usd', ''),
                  "g_25" => Stock::StockDisponible('ps','gift-card-25-usd', '')
              ];
              if (is_array($gifts["g_50"]) && is_array($gifts["g_25"])) { // Si hay más de 1 gift de 50 y de 25 en stock.
                  if ($gifts["g_50"][0]->Q_Stock > 0 && $gifts["g_25"][0]->Q_Stock > 0) { // Si hay más de 1 gift de 50 y de 25 en stock.
                      $data_gifts = [
                          $gifts["g_50"][0],
                          $gifts["g_25"][0]
                      ];
                  }
              } 

              break;
            case '85':

              $gifts = [
                  "g_50" => Stock::StockDisponible('ps','gift-card-50-usd', ''),
                  "g_25" => Stock::StockDisponible('ps','gift-card-25-usd', ''),
                  "g_10" => Stock::StockDisponible('ps','gift-card-10-usd', '')
              ];
              if (is_array($gifts["g_50"]) && is_array($gifts["g_25"]) && is_array($gifts["g_10"])) { // Si hay más de 1 gift de 50, de 25 y de 10 en stock.
                  if ($gifts["g_50"][0]->Q_Stock > 0 && $gifts["g_25"][0]->Q_Stock > 0 && $gifts["g_10"][0]->Q_Stock > 0) { // Si hay más de 1 gift de 50, de 25 y de 10 en stock.
                      $data_gifts = [
                          $gifts["g_50"][0],
                          $gifts["g_25"][0],
                          $gifts["g_10"][0]
                      ];
                  } 
              } 

              break;
        }

        return $data_gifts;
    }

    private function giftStockAvalaible($costo)
    {
        return DB::table('stock')->select(
            'titulo',
            'costo_usd',
            'consola',
            DB::raw("COUNT(titulo) AS Q")
        )
        ->where('titulo','LIKE','gift%')
        ->where('costo_usd',$costo)
        ->groupBy('titulo')->first();
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

    public function listaCobros(Request $request)
    {
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $datos = Sales::getDatosCobros()->salesCobrosByCustomColumn($obj)->paginate(50);

        $columns = Schema::getColumnListing('ventas_cobro');

        return view('sales.lista_cobros')->with(['datos' => $datos, 'columns' => $columns]);

    }

    public function sinEntregar(Request $request) {
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $ventas_excluidas = DB::table('configuraciones')->where('ID', 1)->value('ventas_sinentregar');

        $sales = Sales::getSalesSinEntregar($obj, $ventas_excluidas)->paginate(50);


        $columns = Schema::getColumnListing('ventas');

        return view('sales.lista_sin_entregar')->with(['datos' => $sales, 'columns' => $columns, 'ventas_excluidas' => $ventas_excluidas]);
    }

    public function salesClient($id_sale) {
        $venta = DB::table('ventas')->where('ID',$id_sale)->first();
        $id_cliente = 0;

        if ($venta) {
            $id_cliente = $venta->clientes_id;
        }

        echo json_encode(['id_cliente' => $id_cliente]);
    }


}
