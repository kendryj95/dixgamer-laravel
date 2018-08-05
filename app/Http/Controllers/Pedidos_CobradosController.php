<?php

namespace App\Http\Controllers;

use App\Pedidoscobrados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class Pedidos_CobradosController extends Controller
{
    //

    public function index(Request $request)
    {
        $pedido = " and post_id != ''";

        if($request->pedido){
            $nro_pedido = (get_magic_quotes_gpc()) ? $pedido : addslashes($pedido);
            $pedido = " and post_id=" . $nro_pedido;

            $row_rsClientes = DB::select(DB::raw("SELECT clientes_id, order_id_web, nombre, apellido 
                  FROM ventas 
                  LEFT JOIN clientes on ventas.clientes_id = clientes.ID where ventas.order_id_web=".$nro_pedido));
        }

        $passdata = DB::select("SELECT
                                wco.order_item_id,
                                wco.order_id,
                                    max( CASE WHEN d.meta_key = '_billing_last_name' and wco.order_id = d.post_id THEN d.meta_value END ) as apellido,
                                    max( CASE WHEN d.meta_key = '_billing_first_name' and wco.order_id = d.post_id THEN d.meta_value END ) as nombre,
                                    max( CASE WHEN d.meta_key = '_billing_email' and wco.order_id = d.post_id THEN d.meta_value END ) as email,
                                    max( CASE WHEN d.meta_key = '_billing_email' and wco.order_id = d.post_id THEN d.meta_value END ) as cliente_email,
                                    max( CASE WHEN d.meta_key = 'user_id_ml' and wco.order_id = d.post_id THEN d.meta_value END ) as user_id_ml,
                                    
                                    max( CASE WHEN d.meta_key = 'user_id_ml' and wco.order_id = d.post_id THEN d.meta_value END ) as cliente_ID, -- Modificar
                                    
                                    max( CASE WHEN d.meta_key = 'order_id_ml' and wco.order_id = d.post_id THEN d.meta_value END ) as order_id_ml,
                                    max( CASE WHEN d.meta_key = '_payment_method_title' and wco.order_id = d.post_id THEN d.meta_value END ) as _payment_method_title, 
                                    max( CASE WHEN d.meta_key = '_payment_method' and wco.order_id = d.post_id THEN d.meta_value END ) as _payment_method,
                                    max( CASE WHEN d.meta_key = 'consola' and wco.order_id = d.post_id THEN d.meta_value END ) as consola,

                                    max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
                                    max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
                                    max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
                                    max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id,
                                    
                                    max( CASE WHEN wcom.meta_key = '_billing_email' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as cliente_auto, -- modificar
																

                                SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto
                                FROM cbgw_woocommerce_order_items as wco
                                JOIN cbgw_posts as p ON wco.order_id = p.ID
                                INNER JOIN cbgw_postmeta as d ON p.ID = d.post_id
								INNER JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
                                WHERE p.post_status = 'wc-processing' and p.post_type='shop_order' and order_id!=''
                                GROUP BY wco.order_item_id
                                ORDER BY order_item_id DESC");

            return view('sales.web_sales')->with(['row_rsAsignarVta' => $passdata]);
    }

    public function getDataClientWebSales(Request $request){
        $cliente = DB::table('clientes')->where('email', $request->email)->first();

        return Response()->json($cliente);
    }

}
