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
use Mail;

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
    
    public function indexNotes(Request $request){

        // Ventas con filtro
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $datos = Sales::getDataNotes($obj)->paginate(50);
        // $datos = $this->QueryIndex();

        $columns = Schema::getColumnListing('ventas_notas');

        return view('sales.sales_list_notes')->with(['datos' => $datos, 'columns' => $columns]);
    }

    public function indexMati(Request $request)
    {
        // Ventas con filtro
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $datos = Sales::getDataMati($obj)->orderBy('ID','DESC')->paginate(50);

        $columns = Schema::getColumnListing('mati');

        return view('sales.mati_list')->with(['datos' => $datos, 'columns' => $columns]);
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

        $medios_cobros = DB::table('medios_cobros')->where('habilitado',1)->groupBy('name')->get();

        $stock_id = $row_rsSTK[0]->ID_stk;


        return view('sales.add_sale')->with([
            'colname_rsCON'     => $consola,
            'colname_rsTIT'     => $titulo,
            'colname_rsSlot'    => $slot,
            'row_rsClientes'    => $row_rsClientes,
            'rowsAA'            => $rowsAA,
            'row_rsStock2'      => $row_rsStock2,
            'stk_ID'            => $stk_ID,
            'medios_cobros'     => $medios_cobros
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
            'Day'           => date('Y-m-d H:i:s'),
            'Day_modif'     => date('Y-m-d H:i:s'),
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
        $deuda = false;
        $postmeta = null;
        $venta_data = null;

        $existe_OII = DB::table('ventas')
                        ->select(
                            'order_item_id',
                            'clientes_id',
                            'usuario',
                            'ID as venta_id'
                        )
                        ->where('order_item_id',$oii)->first();

        if ($existe_OII) {
            $asignador = $existe_OII->usuario;
            $cliente = $existe_OII->clientes_id;

            if (!$request->action) return redirect("web/sales/list?r=_exist&c=$cliente&u=$asignador");
            elseif ($request->action && $request->action == "wooc") return response()->json(["status" => "duplicated", "ventas_id" => $existe_OII->venta_id]);

        }

        $row_rsSTK = Stock::StockDisponible($consola,$titulo, $slot);

        $venta = DB::table("cbgw_woocommerce_order_items as wco")
            ->select("wco.order_item_id","wco.order_id","p.ID as post_id","p.post_status as estado")
            ->leftjoin("cbgw_posts as p","wco.order_id","p.ID")
            ->where("wco.order_item_id",$oii)
            ->where(function ($query) {
                $query->where('p.post_status', 'wc-processing')
                    ->orWhere('p.post_status', 'wc-completed');
            })->first();


        if ($venta) { // Si obtiene el registro de las tablas de woocommerce, de lo contrario se mostrará una alerta de error.

            $postmeta = DB::table(DB::raw("(SELECT (SELECT meta_value AS email FROM cbgw_postmeta WHERE meta_key='_billing_email' AND post_id={$venta->order_id}) as email, (SELECT meta_value AS _payment_method FROM cbgw_postmeta WHERE meta_key='_payment_method_title' AND post_id={$venta->order_id}) as _payment_method, (SELECT meta_value AS ref_cobro_3 FROM cbgw_postmeta WHERE meta_key='_transaction_id' AND post_id={$venta->order_id} LIMIT 1) as ref_cobro_3) as postmeta"))->first();

            if (!$postmeta) return redirect()->back()->withErrors(["No se encontraron datos en el postmeta"]);

            $venta_data = DB::table(DB::raw("(SELECT (SELECT meta_value AS precio FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_line_total' and order_item_id=$oii) as precio, (SELECT meta_value AS slot FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='pa_slot' AND order_item_id=$oii) as slot, (SELECT meta_value AS _product_id FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_product_id' AND order_item_id=$oii) as _product_id) as sale"))->first();

           if (!is_array($row_rsSTK)) {

               if (isset($request->gift) && $request->gift == 'si') { // Validaré si el producto es una gift
                   $valor_gift = explode("-", $titulo)[2];

                   $data_gifts = $this->giftCardOrder($valor_gift);

               }

               if (count($data_gifts) > 0) {
                   $giftConStock = true;
               } else {
                   $producto_catalogo = $venta_data->_product_id;

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

           $email_pedido = $postmeta->email;

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

            if (strpos($titulo,"deuda") !== false && $consola == "deuda") {
                $deuda = true;
            }

            if (!is_array($row_rsSTK) && !$giftConStock && !$deuda) {
               if (!$request->previousUrl) {
                    $datos['consola'] = $consola;
                    $datos['titulo'] = $titulo;
                    $datos['slot'] = $slot;
                    $datos['order'] = $oii;
                    $datos['tipo'] = "Sales";
                    $datos['cliente'] = $existEmailCliente;
                    Mail::send('emails.sin_stock', $datos, function($message) use ($venta,$consola,$titulo,$slot)
                    {
                        $vendedor = session()->get('usuario') != null ? session()->get('usuario')->Nombre : "Xavi";
                        $message->to("contacto@dixgamer.com", "Contacto")->subject($vendedor.", falta stock (sales)- $titulo ($consola) $slot - Pedido {$venta->order_id}");
                    });

                   if ($request->action && $request->action == "wooc")
                       return response()->json(["status" => false]);
               }

               $venta->email = $email_pedido;
               $venta->user_id_ml = null;
               $venta->order_id_ml = null;

               if (!is_array($row_rsSTK) && !$giftConStock && !$deuda) {
                   return view('sales.salesInsertWeb', [
                       "row_rsSTK" => $row_rsSTK,
                       "venta" => $venta,
                       "consola" => $consola,
                       "titulo" => $titulo,
                       "slot" => $slot,
                       "clientes" => $existEmailCliente,
                       "linkPS" => $link_PS
                   ]);
               }
           } else {

               $clientes_id = $existEmailCliente->ID;
               $medio_cobro = '';
               $ref_cobro = '';
               $multiplo = '';

               if ($venta_data) {

                   // Defino el multiplo de la comisión por defecto de MP que es 5,38 %
                   $multiplo = "0.0538";


                   $payment_method = DB::table('medios_cobros')->where('payment_method','LIKE',"%".substr($postmeta->_payment_method,0,20)."%")->where('habilitado',1)->first();

                   if ($payment_method) {
                       $medio_cobro = $payment_method->name;
                       $multiplo = $payment_method->commission;
                   } else {
//                       $medio_cobro = "No encontrado";
                       $medio_cobro = $postmeta->_payment_method;
                   }

                   $ref_cobro = $postmeta->ref_cobro_3;

                   $precio = $venta_data->precio;
                   $comision = ($multiplo * $venta_data->precio);
                   $precio_original = $precio;
                   $comision_original = $comision;

                   $sale = new \stdClass();
                   $sale->order_item_id = $oii;
                   $sale->order_id = $venta->order_id;

                   DB::beginTransaction();

                   try {

                       if (!$giftConStock) { // Si el producto no es una gift quiere decir esa variable.
                           $data = $this->dataSale($sale, $row_rsSTK, $existEmailCliente, $slot, $deuda);

                           if ($request->action && $request->action == "wooc") {
                               $existe_OII = DB::table('ventas')
                                   ->select(
                                       'order_item_id',
                                       'clientes_id',
                                       'usuario',
                                       'ID as venta_id'
                                   )
                                   ->where('order_item_id',$oii)->first();

                               if ($existe_OII)
                                   return response()->json(["status" => "duplicated", "ventas_id" => $existe_OII->venta_id]);
                           }

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
                           $data['usuario'] = session()->get('usuario') != null ? session()->get('usuario')->Nombre : "Xavi";

                           DB::table('ventas_cobro')->insert($data);

                       } else {
                           foreach ($data_gifts as $value) { // Varias gifts se van a registrar

                               $row_rsSTK = Stock::StockDisponible($value->consola,$value->titulo, '');
                               $partes = count($data_gifts);
                               $precio = $precio_original / $partes;
                               $comision = $comision_original / $partes;

                               if ($request->action && $request->action == "wooc") {
                                   $existe_OII = DB::table('ventas')
                                       ->select(
                                           'order_item_id',
                                           'clientes_id',
                                           'usuario',
                                           'ID as venta_id'
                                       )
                                       ->where('order_item_id',$oii)->first();

                                   if ($existe_OII)
                                       return response()->json(["status" => "duplicated", "ventas_id" => $existe_OII->venta_id]);
                               }

                               $data = $this->dataSale($sale, $row_rsSTK, $existEmailCliente);
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

                       if ($request->session()->get('usuario') != null || ($request->action && $request->action == "wooc"))
                           DB::commit();

                       if (!$request->action)
                           echo "<script>window.top.location.href='".url('clientes',$clientes_id)."'</script>";
                       elseif ($request->action && $request->action == "wooc")
                           return response()->json(["status" => true, "ventas_id" => $ventaid]);


                   } catch (Exception $e) {
                       DB::rollback();
                       if (!$request->action)
                           return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor vuelva a intentarlo.']);
                   }

               }
           }
        } elseif (!$request->action) {
            return redirect()->back()->withErrors(['Ha ocurrido un error al intentar obtener los datos de la compra.']);
        }

    }

    private function dataSale($venta, $row_rsSTK, $existEmailCliente, $slot = '', $deuda = false)
    {
        $date = date('Y-m-d H:i:s');
        $clientes_id = $existEmailCliente->ID;
        $order_item_id = $venta->order_item_id;
        $order_id_ml = '';
        $slot_def = '';
        $medio_venta = '';
        $titulo = '';


        if ($deuda) {
            $stock_id = 6;
            $cons = "x";
        } else {
            $cons = $row_rsSTK[0]->consola;
            $stock_id = $row_rsSTK[0]->ID_stk;
            $titulo = $row_rsSTK[0]->titulo;
        }

        //Si es una vta de ps4 o plus slot por ML asigno el slot desde el parametro GET
        //if ((($row_rsClient['user_id_ml']) && ($row_rsClient['user_id_ml'] != "")) && (($cons === "ps4") or ($row_rsClient['producto'] === "plus-12-meses-slot"))): $slot = ucwords($colname_rsSlot);
        //Si es una vta de ps4 o plus slot que NO ES de ML asigno el slot desde la consulta SQL
        //elseif --> antes de escapar el IF anterior que iba primero

        if (($cons === "ps4") or $cons === "ps5" or ($titulo === "plus-12-meses-slot")): $slot_def = ucwords($slot); $estado = "pendiente";
        elseif ($cons === "ps3"): $slot_def = "Primario"; $estado = "pendiente";

        //Si no cumple con ninguno de los parametros anteriores seguramente se trata de una venta de Gift Card y el slot se define en "No"
        else: $slot_def = "No"; $estado = "listo";
        endif;

        $medio_venta = "Web";
        $order_id_web = $venta->order_id;

        $data = [];
        $data['clientes_id'] = $clientes_id;
        $data['stock_id'] = $stock_id;
        $data['order_item_id'] = $order_item_id;
        $data['cons'] = $cons;
        $data['slot'] = $slot_def;
        $data['medio_venta'] = $medio_venta;
        $data['order_id_web'] = $order_id_web;
        $data['estado'] = $stock_id == 6 ? "pago-deuda" : $estado;
        $data['Day'] = $date;
        $data['Day_modif'] = $date;
        $data['usuario'] = session()->get('usuario') != null ? session()->get('usuario')->Nombre : "Xavi";

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

        $configuraciones = DB::table('configuraciones')->where('ID', 1)->first();
        $ventas_excluidas = $configuraciones->ventas_sinentregar;
        $clientes_excluidos = $configuraciones->clientes_sinentregar;

        $sales = Sales::getSalesSinEntregar($obj, $ventas_excluidas, $clientes_excluidos)->paginate(50);


        $columns = Schema::getColumnListing('ventas');

        return view('sales.lista_sin_entregar')->with(['datos' => $sales, 'columns' => $columns, 'ventas_excluidas' => $ventas_excluidas, 'clientes_excluidos' => $clientes_excluidos]);
    }

    public function salesClient($id_sale) {
        $venta = DB::table('ventas')->where('ID',$id_sale)->first();
        $id_cliente = 0;

        if ($venta) {
            $id_cliente = $venta->clientes_id;
        }

        echo json_encode(['id_cliente' => $id_cliente]);
    }

    public function salesListRecupero(Request $request) {
        $obj = new \stdClass;
        $obj->column = $request->column;
        $obj->word = $request->word;

        $ventas = Sales::ventasRecupero($obj)->paginate(50);
        $obj->column = "";
        $obj->word = "";
        $vtaColumns = Sales::ventasRecupero($obj)->limit(1)->get();
        $columns = [];
        foreach ($vtaColumns as $sales) {
            foreach ($sales as $columna => $value) {
                if (!in_array($columna,$columns)) {
                    $columns[] = $columna;
                }
            }
        }

        $configuracion = DB::table('configuraciones')->where('ID',1)->first();

        $prod_primarios = explode(",",$configuracion->prod_excluidos_pri);
        $prod_secundarios = explode(",",$configuracion->prod_excluidos_secu);

        return view('sales.sales_recupero', compact('ventas','columns','prod_primarios','prod_secundarios'));
    }

    public function desvincularMati($id_mati)
    {
        try {
            DB::table("mati")->where("id", $id_mati)->update(["order_id" => -1]);
            // Mensaje de notificacion
            \Helper::messageFlash('Ventas','Mati desvinculado correctamente','alert_ventas');

            return redirect()->back();
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['Ha ocurrido un error inesperado. Por favor vuelva a intentarlo.']);
        }
    }

    public function salesSummary()
    {
        $sales = Sales::salesSummaryByMonthSince2021()->get();
        $sales2 = Sales::salesSummaryByDayLastTwoMonth()->get();

        return view("sales.summary", compact("sales", "sales2"));
    }

}
