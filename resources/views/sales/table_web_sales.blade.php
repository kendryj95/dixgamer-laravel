<?php

do{
    $pedido = $row_rsAsignarVta['order_id'];
    $oii = $row_rsAsignarVta['order_item_id'];

    $apellido1 = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = '_billing_last_name' and post_id= %d", $pedido);
    mysql_select_db($database_Conexion, $Conexion);
    $apellido = mysql_query($apellido1, $Conexion) or die(mysql_error());

    $nombre = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = '_billing_first_name' and post_id = %d", $pedido );
    $email = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = '_billing_email' and post_id = %d", $pedido );
    $user_id_ml = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = 'user_id_ml' and post_id = %d", $pedido );
    $order_id_ml = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key = 'order_id_ml' and post_id = %d", $pedido );
    $_payment_method_title = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key='_payment_method_title' and post_id=%d", $pedido );
    $_payment_method = sprintf("SELECT meta_value as rdo FROM cbgw_postmeta WHERE meta_key='_payment_method' and post_id=%d", $pedido );

    $_qty = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_qty' and order_item_id=%d", $oii );
    $pa_slot = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='pa_slot' and order_item_id=%d", $oii );
    $_product_id = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_product_id' and order_item_id=%d", $oii );
    $_variation_id = sprintf("SELECT meta_value as rdo FROM cbgw_woocommerce_order_itemmeta WHERE meta_key='_variation_id' and order_item_id=%d", $oii );

} while($row);

