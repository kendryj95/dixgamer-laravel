<?php

namespace App\Http\Controllers;

use App\Pedidoscobrados;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use stdClass;

class Pedidos_CobradosController extends Controller
{
    //

    public function index($filtro = null)
    {
                $condicion = " and post_id!=''";
                $condicion2 = '';
                $cliente = [];

                if ($filtro != null) {
                    if (intval($filtro)) {
                        $condicion = " and post_id=$filtro";
                        $cliente = $this->consultarPedido($filtro);
                        if (count($cliente) > 0)
                            return redirect("clientes/{$cliente[0]->clientes_id}?order=$filtro");
                    } else {
                        $condicion2 = " where clientes.email LIKE '%$filtro%'";
                    }
                }

                $passdata = DB::select("
        SELECT pedido.*, clientes.ID as cliente_ID, clientes.email as cliente_email, clientes.auto as cliente_auto
            FROM
            (SELECT
            conjunto2.*,
            ventas.ID as Vta_ID,
            ventas.order_item_id as Vta_oii
                FROM
                ventas
                RIGHT JOIN
                (SELECT 
                conjunto.*,
                max( CASE WHEN pm2.meta_key = 'consola' and conjunto._product_id = pm2.post_id THEN pm2.meta_value END ) as consola
                    FROM
                    cbgw_postmeta as pm2
                    inner JOIN
                        (SELECT order_item_id, order_id, producto, post_id, apellido, nombre, email, user_id_ml, order_id_ml, _payment_method, _qty, _product_id, _variation_id, post_parent, q_variation, slot as slot_original, CASE WHEN slot IS NOT NULL THEN slot Else CASE WHEN q_variation = 2 then 'primario' WHEN q_variation = 1 then 'secundario' ELSE slot END END slot FROM 
                                    (select
                                    wco.order_item_id,
                                    wco.order_id,
                                    SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto,
                                    p.ID as post_id,
                                    p.post_status as estado,
                                    max( CASE WHEN pm.meta_key = '_billing_last_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as apellido,
                                    max( CASE WHEN pm.meta_key = '_billing_first_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as nombre,
                                        max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
                                        max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml,
                                        max( CASE WHEN pm.meta_key = 'order_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as order_id_ml,
                                        max( CASE WHEN pm.meta_key = '_payment_method_title' and wco.order_id = pm.post_id THEN pm.meta_value END ) as _payment_method,
                                        max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
                                        max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
                                        max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
                                        max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id
                                    from
                                    cbgw_woocommerce_order_items as wco
                                    LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID
                                    LEFT JOIN cbgw_postmeta as pm ON wco.order_id = pm.post_id
                                    LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
                                    where 
                                    p.post_status = 'wc-processing' " . $condicion . " group by 
                                    wco.order_item_id
                                    ORDER BY order_item_id DESC) as primer
                        LEFT JOIN
                    (SELECT post_parent, count(*) as q_variation FROM `cbgw_posts` where post_type='product_variation' group by post_parent ASC ORDER BY `cbgw_posts`.`post_parent` DESC) as variaciones
                    ON primer._product_id=variaciones.post_parent ) as conjunto
                    ON conjunto._product_id = pm2.post_id
                    GROUP by conjunto.order_item_id) as conjunto2
                ON conjunto2.order_item_id = ventas.order_item_id
            WHERE
            ventas.order_item_id IS NUll
            GROUP by conjunto2.order_item_id) as pedido

            LEFT JOIN
            clientes
            ON pedido.email = clientes.email
            $condicion2
        GROUP BY pedido.order_item_id
        ORDER BY order_item_id DESC");


                /*$tamPag = 20;
                $numReg = count($passdata);
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

                $pedidos = $this->consultaPagination($pedido,$limit,$tamPag);*/

                $paginaAct = '';
                $paginas = '';

                

                return view('sales.web_sales')->with(['row_rsAsignarVta' => $passdata, 'paginas' => $paginas, 'paginaAct' => $paginaAct, "mostrar" => true, "cliente" => $cliente]);
    }

    public function salesPes21($filtro = null)
    {

        $condicion = " and post_id!=''";
        $condicion2 = '';
        $cliente = [];

        if ($filtro != null) {
            if (intval($filtro)) {
                $condicion = " and post_id=$filtro";
                $cliente = $this->consultarPedido($filtro);
            } else {
                $condicion2 = " where clientes.email LIKE '%$filtro%'";
            }
        }

        $passdata = DB::select("
SELECT pedido.*, clientes.ID as cliente_ID, clientes.email as cliente_email, clientes.auto as cliente_auto
    FROM
    (SELECT
    conjunto2.*,
    ventas.ID as Vta_ID,
    ventas.order_item_id as Vta_oii
        FROM
        ventas
        RIGHT JOIN
        (SELECT 
        conjunto.*,
        max( CASE WHEN pm2.meta_key = 'consola' and conjunto._product_id = pm2.post_id THEN pm2.meta_value END ) as consola
            FROM
            cbgw_postmeta as pm2
            inner JOIN
                (SELECT order_item_id, order_id, producto, post_id, apellido, nombre, email, user_id_ml, order_id_ml, _payment_method, _qty, _product_id, _variation_id, post_parent, q_variation, slot as slot_original, CASE WHEN slot IS NOT NULL THEN slot Else CASE WHEN q_variation = 2 then 'primario' WHEN q_variation = 1 then 'secundario' ELSE slot END END slot FROM 
                            (select
                            wco.order_item_id,
                            wco.order_id,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto,
                            p.ID as post_id,
                            p.post_status as estado,
                            max( CASE WHEN pm.meta_key = '_billing_last_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as apellido,
                            max( CASE WHEN pm.meta_key = '_billing_first_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as nombre,
                                max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
                                max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml,
                                max( CASE WHEN pm.meta_key = 'order_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as order_id_ml,
                                max( CASE WHEN pm.meta_key = '_payment_method_title' and wco.order_id = pm.post_id THEN pm.meta_value END ) as _payment_method,
                                max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
                                max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
                                max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
                                max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id
                            from
                            cbgw_woocommerce_order_items as wco
                            LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID
                            LEFT JOIN cbgw_postmeta as pm ON wco.order_id = pm.post_id
                            LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
                            where 
                            p.post_status = 'wc-processing' " . $condicion . " and wco.order_item_name LIKE '%pes 2021%' group by 
                            wco.order_item_id
                            ORDER BY order_item_id DESC) as primer
                LEFT JOIN
            (SELECT post_parent, count(*) as q_variation FROM `cbgw_posts` where post_type='product_variation' group by post_parent ASC ORDER BY `cbgw_posts`.`post_parent` DESC) as variaciones
            ON primer._product_id=variaciones.post_parent ) as conjunto
            ON conjunto._product_id = pm2.post_id
            GROUP by conjunto.order_item_id) as conjunto2
        ON conjunto2.order_item_id = ventas.order_item_id
    WHERE
    ventas.order_item_id IS NUll
    GROUP by conjunto2.order_item_id) as pedido

    LEFT JOIN
    clientes
    ON pedido.email = clientes.email
    $condicion2
GROUP BY pedido.order_item_id
ORDER BY order_item_id DESC");


        /*$tamPag = 20;
        $numReg = count($passdata);
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

        $pedidos = $this->consultaPagination($pedido,$limit,$tamPag);*/

        $paginaAct = '';
        $paginas = '';

        

        return view('sales.web_sales')->with(['row_rsAsignarVta' => $passdata, 'paginas' => $paginas, 'paginaAct' => $paginaAct, "mostrar" => true, "cliente" => $cliente]);
    }

    public function sinSalesFifaPes22($filtro = null)
    {

        $condicion = " and post_id!=''";
        $condicion2 = '';
        $cliente = [];

        if ($filtro != null) {
            if (intval($filtro)) {
                $condicion = " and post_id=$filtro";
                $cliente = $this->consultarPedido($filtro);
            } else {
                $condicion2 = " where clientes.email LIKE '%$filtro%'";
            }
        }

        $passdata = DB::select("
SELECT pedido.*, clientes.ID as cliente_ID, clientes.email as cliente_email, clientes.auto as cliente_auto
    FROM
    (SELECT
    conjunto2.*,
    ventas.ID as Vta_ID,
    ventas.order_item_id as Vta_oii
        FROM
        ventas
        RIGHT JOIN
        (SELECT 
        conjunto.*,
        max( CASE WHEN pm2.meta_key = 'consola' and conjunto._product_id = pm2.post_id THEN pm2.meta_value END ) as consola
            FROM
            cbgw_postmeta as pm2
            inner JOIN
                (SELECT order_item_id, order_id, producto, post_id, apellido, nombre, email, user_id_ml, order_id_ml, _payment_method, _qty, _product_id, _variation_id, post_parent, q_variation, slot as slot_original, CASE WHEN slot IS NOT NULL THEN slot Else CASE WHEN q_variation = 2 then 'primario' WHEN q_variation = 1 then 'secundario' ELSE slot END END slot FROM 
                            (select
                            wco.order_item_id,
                            wco.order_id,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto,
                            p.ID as post_id,
                            p.post_status as estado,
                            max( CASE WHEN pm.meta_key = '_billing_last_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as apellido,
                            max( CASE WHEN pm.meta_key = '_billing_first_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as nombre,
                                max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
                                max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml,
                                max( CASE WHEN pm.meta_key = 'order_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as order_id_ml,
                                max( CASE WHEN pm.meta_key = '_payment_method_title' and wco.order_id = pm.post_id THEN pm.meta_value END ) as _payment_method,
                                max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
                                max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
                                max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
                                max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id
                            from
                            cbgw_woocommerce_order_items as wco
                            LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID
                            LEFT JOIN cbgw_postmeta as pm ON wco.order_id = pm.post_id
                            LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
                            where 
                            p.post_status = 'wc-processing' " . $condicion . " and wco.order_item_name NOT LIKE '%fifa 22%' group by 
                            wco.order_item_id
                            ORDER BY order_item_id DESC) as primer
                LEFT JOIN
            (SELECT post_parent, count(*) as q_variation FROM `cbgw_posts` where post_type='product_variation' group by post_parent ASC ORDER BY `cbgw_posts`.`post_parent` DESC) as variaciones
            ON primer._product_id=variaciones.post_parent ) as conjunto
            ON conjunto._product_id = pm2.post_id
            GROUP by conjunto.order_item_id) as conjunto2
        ON conjunto2.order_item_id = ventas.order_item_id
    WHERE
    ventas.order_item_id IS NUll
    GROUP by conjunto2.order_item_id) as pedido

    LEFT JOIN
    clientes
    ON pedido.email = clientes.email
    $condicion2
GROUP BY pedido.order_item_id
ORDER BY order_item_id DESC");


        /*$tamPag = 20;
        $numReg = count($passdata);
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

        $pedidos = $this->consultaPagination($pedido,$limit,$tamPag);*/

        $paginaAct = '';
        $paginas = '';



        return view('sales.web_sales')->with(['row_rsAsignarVta' => $passdata, 'paginas' => $paginas, 'paginaAct' => $paginaAct, "mostrar" => true, "cliente" => $cliente]);
    }

    public function salesFifa22($filtro = null)
    {

        $condicion = " and post_id!=''";
        $condicion2 = '';
        $cliente = [];

        if ($filtro != null) {
            if (intval($filtro)) {
                $condicion = " and post_id=$filtro";
                $cliente = $this->consultarPedido($filtro);
            } else {
                $condicion2 = " where clientes.email LIKE '%$filtro%'";
            }
        }

        $passdata = DB::select("
SELECT pedido.*, clientes.ID as cliente_ID, clientes.email as cliente_email, clientes.auto as cliente_auto
    FROM
    (SELECT
    conjunto2.*,
    ventas.ID as Vta_ID,
    ventas.order_item_id as Vta_oii
        FROM
        ventas
        RIGHT JOIN
        (SELECT 
        conjunto.*,
        max( CASE WHEN pm2.meta_key = 'consola' and conjunto._product_id = pm2.post_id THEN pm2.meta_value END ) as consola
            FROM
            cbgw_postmeta as pm2
            inner JOIN
                (SELECT order_item_id, order_id, producto, post_id, apellido, nombre, email, user_id_ml, order_id_ml, _payment_method, _qty, _product_id, _variation_id, post_parent, q_variation, slot as slot_original, CASE WHEN slot IS NOT NULL THEN slot Else CASE WHEN q_variation = 2 then 'primario' WHEN q_variation = 1 then 'secundario' ELSE slot END END slot FROM 
                            (select
                            wco.order_item_id,
                            wco.order_id,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto,
                            p.ID as post_id,
                            p.post_status as estado,
                            max( CASE WHEN pm.meta_key = '_billing_last_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as apellido,
                            max( CASE WHEN pm.meta_key = '_billing_first_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as nombre,
                                max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
                                max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml,
                                max( CASE WHEN pm.meta_key = 'order_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as order_id_ml,
                                max( CASE WHEN pm.meta_key = '_payment_method_title' and wco.order_id = pm.post_id THEN pm.meta_value END ) as _payment_method,
                                max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
                                max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
                                max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
                                max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id
                            from
                            cbgw_woocommerce_order_items as wco
                            LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID
                            LEFT JOIN cbgw_postmeta as pm ON wco.order_id = pm.post_id
                            LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
                            where 
                            p.post_status = 'wc-processing' " . $condicion . " and wco.order_item_name LIKE '%fifa 22%' group by 
                            wco.order_item_id
                            ORDER BY order_item_id DESC) as primer
                LEFT JOIN
            (SELECT post_parent, count(*) as q_variation FROM `cbgw_posts` where post_type='product_variation' group by post_parent ASC ORDER BY `cbgw_posts`.`post_parent` DESC) as variaciones
            ON primer._product_id=variaciones.post_parent ) as conjunto
            ON conjunto._product_id = pm2.post_id
            GROUP by conjunto.order_item_id) as conjunto2
        ON conjunto2.order_item_id = ventas.order_item_id
    WHERE
    ventas.order_item_id IS NUll
    GROUP by conjunto2.order_item_id) as pedido

    LEFT JOIN
    clientes
    ON pedido.email = clientes.email
    $condicion2
GROUP BY pedido.order_item_id
ORDER BY order_item_id DESC");


        /*$tamPag = 20;
        $numReg = count($passdata);
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

        $pedidos = $this->consultaPagination($pedido,$limit,$tamPag);*/

        $paginaAct = '';
        $paginas = '';

        

        return view('sales.web_sales')->with(['row_rsAsignarVta' => $passdata, 'paginas' => $paginas, 'paginaAct' => $paginaAct, "mostrar" => true, "cliente" => $cliente]);
    }

    private function consultaPagination($pedido,$inicio, $fin)
    {
        $passdata = DB::select("SELECT pedido.*, clientes.ID as cliente_ID, clientes.email as cliente_email, clientes.auto as cliente_auto
    FROM
    (SELECT
    conjunto2.*,
    ventas.ID as Vta_ID,
    ventas.order_item_id as Vta_oii
        FROM
        ventas
        RIGHT JOIN
        (SELECT 
        conjunto.*,
        max( CASE WHEN pm2.meta_key = 'consola' and conjunto._product_id = pm2.post_id THEN pm2.meta_value END ) as consola
            FROM
            cbgw_postmeta as pm2
            inner JOIN
                (SELECT order_item_id, order_id, producto, post_id, apellido, nombre, email, user_id_ml, order_id_ml, _payment_method, _qty, _product_id, _variation_id, post_parent, q_variation, slot as slot_original, CASE WHEN slot IS NOT NULL THEN slot Else CASE WHEN q_variation = 2 then 'primario' WHEN q_variation = 1 then 'secundario' ELSE slot END END slot FROM 
                            (select
                            wco.order_item_id,
                            wco.order_id,
                            SUBSTRING_INDEX(SUBSTRING_INDEX(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(LCASE(wco.order_item_name)), ' ', '-'), '''', ''), '’', ''), '.', ''),'-&ndash;',1),'---',1) as producto,
                            p.ID as post_id,
                            p.post_status as estado,
                            max( CASE WHEN pm.meta_key = '_billing_last_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as apellido,
                            max( CASE WHEN pm.meta_key = '_billing_first_name' and wco.order_id = pm.post_id THEN pm.meta_value END ) as nombre,
                                max( CASE WHEN pm.meta_key = '_billing_email' and wco.order_id = pm.post_id THEN pm.meta_value END ) as email,
                                max( CASE WHEN pm.meta_key = 'user_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as user_id_ml,
                                max( CASE WHEN pm.meta_key = 'order_id_ml' and wco.order_id = pm.post_id THEN pm.meta_value END ) as order_id_ml,
                                max( CASE WHEN pm.meta_key = '_payment_method_title' and wco.order_id = pm.post_id THEN pm.meta_value END ) as _payment_method,
                                max( CASE WHEN wcom.meta_key = '_qty' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _qty,
                                max( CASE WHEN wcom.meta_key = 'pa_slot' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as slot,
                                max( CASE WHEN wcom.meta_key = '_product_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _product_id,
                                max( CASE WHEN wcom.meta_key = '_variation_id' and wco.order_item_id = wcom.order_item_id THEN wcom.meta_value END ) as _variation_id
                            from
                            cbgw_woocommerce_order_items as wco
                            LEFT JOIN cbgw_posts as p ON wco.order_id = p.ID
                            LEFT JOIN cbgw_postmeta as pm ON wco.order_id = pm.post_id
                            LEFT JOIN cbgw_woocommerce_order_itemmeta as wcom ON wco.order_item_id = wcom.order_item_id
                            where 
                            p.post_status = 'wc-processing' " . $pedido . " group by 
                            wco.order_item_id
                            ORDER BY order_item_id DESC) as primer
                LEFT JOIN
            (SELECT post_parent, count(*) as q_variation FROM `cbgw_posts` where post_type='product_variation' group by post_parent ASC ORDER BY `cbgw_posts`.`post_parent` DESC) as variaciones
            ON primer._product_id=variaciones.post_parent ) as conjunto
            ON conjunto._product_id = pm2.post_id
            GROUP by conjunto.order_item_id) as conjunto2
        ON conjunto2.order_item_id = ventas.order_item_id
    WHERE
    ventas.order_item_id IS NUll
    GROUP by conjunto2.order_item_id) as pedido

    LEFT JOIN
    clientes
    ON pedido.email = clientes.email
GROUP BY pedido.order_item_id
ORDER BY order_item_id DESC LIMIT $inicio,$fin");

        return $passdata;
    }

    public function getDataClientWebSales(Request $request){
        $cliente = DB::table('clientes')->where('email', $request->email)->first();

        return Response()->json($cliente);
    }

    private function consultarPedido($pedido)
    {
        $resultado = DB::select("SELECT clientes_id, order_id_web, nombre, apellido FROM ventas LEFT JOIN clientes on ventas.clientes_id = clientes.ID where ventas.order_id_web=?", [$pedido]);

        return $resultado;
    }

}
