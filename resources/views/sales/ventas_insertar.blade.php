@extends('layouts.master-layouts')
@section('container')
    <div class="container">
        <div class="row text-center" style="background-color:#cfcfcf;padding:5px; border: 1px dashed #efefef">
            <img class="img-rounded" width="60" src="/img/productos/<?php echo $colname_rsCON."/".$colname_rsTIT;?>.jpg" alt="<?php echo $colname_rsTIT." - ".$colname_rsCON;?>" /><h4 style="display: inline"> Asignar <?php echo $colname_rsTIT." (".$colname_rsCON.")";?></h4>
        <?php if(($colname_rsCON === "ps4") or ($colname_rsTIT === "plus-12-meses-slot")): echo '<em style="font-size:0.8m">' . $colname_rsSlot . '</em>'; endif;?>


        <!--- Si hay order item id directamente ya confirmo la venta y dejo de imprimir -->
            <?php if (isset($_GET['order_item_id'])) {
                $insertGoTo = "ventas_insertar_web.php";
                if (isset($_SERVER['QUERY_STRING'])) {
                    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
                    $insertGoTo .= $_SERVER['QUERY_STRING'];}
                exit("<a href='$insertGoTo' class='btn btn-success'>Confirmar</a></div>");}
            ?>
        </div>



        <div class="row" style="padding: 20px 0">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <h5>Venta Manual</h5>
                <form method="post" name="form1" id="form1" action="<?php echo $editFormAction; ?>">

                    <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value="<?php echo '[ ' .$row_rsClientes['ID'] . ' ] ' . $row_rsClientes['nombre'] . ' ' . $row_rsClientes['apellido'] . ' - '. $row_rsClientes['email'] ?>">              <br /><br />

                    <input type="text" id="clientes_id" name="clientes_id" value="<?php echo $row_rsClientes['ID']?>" hidden>

                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
                        <select id="medio_venta" name="medio_venta" class="selectpicker form-control">
                            <option selected value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                            <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
                            <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
                        </select>
                    </div>

                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
                        <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
                            <option selected value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
                            <option value="Deposito/Transferencia" data-content="<span class='label label-info'>Deposito/Transferencia</span>">Deposito/Transferencia</option>
                        </select>
                    </div>
                    <div class="input-group form-group" id="n_cobro">
                        <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                        <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" placeholder="N° de Cobro">
                    </div>
                    <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i> completar</span>

                    <input type="text" name="slot" value="<?php echo $colname_rsSlot;?>" hidden>
                    <br><br>
                    <div class="col-md-5">
                        <div class="input-group form-group">
                            <span class="input-group-addon">precio</span>
                            <input class="form-control" type="text" id="precio" name="precio" value="<?php echo $row_rsStock2['precio']; ?>">
                        </div>
                    </div>

                    <div class="col-md-3" style="opacity:0.7">
                        <div class="input-group form-group">
                            <select id="porcentaje" class="form-control">
                                <option selected value="0.12">12 %</option>
                                <option value="0.0538">6 %</option>
                                <option value="0.00">0 %</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="input-group form-group">
                            <span class="input-group-addon">comision</span>
                            <input class="form-control" type="text" id="comision" name="comision" value="">
                        </div>
                    </div>

                    <input type="text" name="estado" value="<?php if(($colname_rsCON == 'ps') || ($colname_rsCON == 'steam')):?>listo<?php else:?>pendiente<?php endif;?>" hidden>

                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                        <input class="form-control" type="text" name="Notas" placeholder="Notas de la venta">
                    </div>

                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                        <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro">
                    </div>

                    <button class="btn btn-primary botonero" id="submiter" type="submit">Insertar</button>
                    <input type="hidden" name="MM_insert" value="form1">

                </form>
            </div>
            <div class="col-sm-3">
            </div>

        </div>

        <!--/row-->
    </div><!--/.container-->
@section('sccripts')
    @parent

@endsection
@endsection