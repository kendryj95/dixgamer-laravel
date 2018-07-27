@extends('layouts.master-layouts')
@section('container')
    <div class="container">
        <div class="row text-center" style="background-color:#cfcfcf;padding:5px; border: 1px dashed #efefef">
            <img class="img-rounded" width="60"
                 src="/img/productos/<?php echo $colname_rsCON . "/" . $colname_rsTIT;?>.jpg"
                 alt="<?php echo $colname_rsTIT . " - " . $colname_rsCON;?>"/><h4 style="display: inline">
                Asignar <?php echo $colname_rsTIT . " (" . $colname_rsCON . ")";?></h4>
        <?php if (($colname_rsCON === "ps4") or ($colname_rsTIT === "plus-12-meses-slot")): echo '<em style="font-size:0.8m">' . $colname_rsSlot . '</em>'; endif;?>


        <!--- Si hay order item id directamente ya confirmo la venta y dejo de imprimir -->
            <?php if (isset($_GET['order_item_id'])) {
                $insertGoTo = "ventas_insertar_web.php";
                if (isset($_SERVER['QUERY_STRING'])) {
                    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
                    $insertGoTo .= $_SERVER['QUERY_STRING'];
                }
                exit("<a href='$insertGoTo' class='btn btn-success'>Confirmar</a></div>");
            }
            ?>
        </div>


        <div class="row" style="padding: 20px 0">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <h5>Venta Manual</h5>
                <form method="post" name="form1" id="form1" action="{{ url('saveManualSale') }}">
                    {{ csrf_field()  }}

                    <input id="clientes" type="text" class="form-control typeahead" autocomplete="off"
                           spellcheck="false"

                           value="<?php //echo '[ ' . $row_rsClientes['ID'] . ' ] ' . $row_rsClientes['nombre'] . ' ' . $row_rsClientes['apellido'] . ' - ' . $row_rsClientes['email'] ?>">
                    <br/><br/>

                    <input type="text" id="clientes_id" name="clientes_id" value="<?php //echo $row_rsClientes['ID']?>"
                           hidden>
                    <input type="hidden" name="stk_ID" value="{{ $stk_ID }}">
                    <input type="hidden" name="consola" value="{{ $colname_rsCON }}">
                    <input type="hidden" name="titulo" value="{{ $colname_rsTIT }}">
                    <input type="hidden" name="slot" value="{{ $colname_rsSlot }}">

                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
                        <select id="medio_venta" name="medio_venta" class="selectpicker form-control">
                            <option selected value="MercadoLibre"
                                    data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre
                            </option>
                            <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
                            <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail
                            </option>
                        </select>
                    </div>

                    <div class="input-group form-group">
                        <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
                        <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
                            <option selected value="MercadoPago"
                                    data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago
                            </option>
                            <option value="Deposito/Transferencia"
                                    data-content="<span class='label label-info'>Deposito/Transferencia</span>">
                                Deposito/Transferencia
                            </option>
                        </select>
                    </div>
                    <div class="input-group form-group" id="n_cobro">
                        <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                        <input class="form-control" type="text" name="ref_cobro" id="ref_cobro"
                               placeholder="N° de Cobro">
                    </div>
                    <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil"
                                                                              aria-hidden="true"></i> completar</span>

                    <input type="text" name="slot" value="<?php echo $colname_rsSlot;?>" hidden>
                    <br><br>
                    <div class="col-md-5">
                        <div class="input-group form-group">
                            <span class="input-group-addon">precio</span>
                            <input class="form-control" type="text" id="precio" name="precio"
                                   value="@if(!empty($row_rsStock2)) {{ $row_rsStock2[0]->precio }} @else 0 @endif">
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

                    <input type="text" name="estado"
                           value="<?php if(($colname_rsCON == 'ps') || ($colname_rsCON == 'steam')):?>listo<?php else:?>pendiente<?php endif;?>"
                           hidden>

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
    </div>
@section('scripts')
    @parent
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
    <script>new Clipboard('.btn-copiador');</script>
    <!-- Activar popover -->

    <script  type="text/javascript" src="{{ asset('js/typeahead.bundle.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // Defining the local dataset
            var cars = <?php echo json_encode($rowsAA); ?>
            //console.log(cars, "Hello, world!");

            // Constructing the suggestion engine
            var cars = new Bloodhound({
                datumTokenizer: Bloodhound.tokenizers.whitespace,
                queryTokenizer: Bloodhound.tokenizers.whitespace,
                local: cars
            });

            // Initializing the typeahead
            $('.typeahead').typeahead({
                    hint: true,
                    highlight: true, /* Enable substring highlighting */
                    minLength: 2 /* Specify minimum characters required for showing result */

                },
                {
                    name: 'cars',
                    source: cars
                });
        });
    </script>
    <script type="text/javascript">
        var isFormValid = false;
        $('#form1').submit(function() {

            if (!isFormValid) {
                if($('#ref_cobro').val() == ''){
                    document.getElementById("n_cobro").className = "input-group form-group has-error";
                    $("#faltacobro").show(300);
                    isFormValid = true;
                    return false;
                }
            }
        });
    </script>

    <script type="text/javascript">
        // invento para mejorar la carga de clientes y consulta de la lista de clientes
        $("#clientes").blur(function(){
            if(this.value==""){
                this.value = arr[0];
            }
        });
        window.setInterval(function() {

            cliente = document.getElementById("clientes").value;
            var String = cliente.substring(cliente.lastIndexOf('[ ')+1,cliente.lastIndexOf(' ]'));
            document.getElementById("clientes_id").value = String;
        },500);
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $("form :input").change(function() {
                var val = $('#medio_venta').val();
                var val2 = $('#medio_cobro').val();
                //alert(val2);
                if (val == "MercadoLibre") {
                    $("#porcentaje").html("<option value='0.12'>12 %</option>");
                } else if (val == "Mail" && val2 == "MercadoPago") {
                    $("#porcentaje").html("<option value='0.0538'>6 %</option>");
                } else if (val == "Mail" && val2 == "Deposito/Transferencia") {
                    $("#porcentaje").html("<option value='0.00'>0 %</option>");
                } else if (val == "Web" && val2 == "MercadoPago") {
                    $("#porcentaje").html("<option value='0.0538'>6 %</option>");
                } else if (val == "Web" && val2 == "Deposito/Transferencia") {
                    $("#porcentaje").html("<option value='0.00'>0 %</option>");
                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#titulo-selec").change(function() {
                var titulo = document.getElementById('titulo-selec').value;
                var consola = document.getElementById('consola').value;
                $("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
                $('#image-swap').error(function() {
                    $("#image-swap").attr("alt", "no se encuentra");
                });
            });
        });
        $(document).ready(function() {
            $("#consola").change(function() {
                var consola = $(this).val();
                var titulo = document.getElementById('titulo-selec').value;
                $("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");
                $('#image-swap').error(function() {
                    $("#image-swap").attr("alt", "no se encuentra");
                });
            });
        });
    </script>
    <script type="text/javascript">

        window.setInterval(function() {
            m1 = document.getElementById("precio").value;
            m2 = document.getElementById("porcentaje").value;
            r = m1*m2;
            document.getElementById("comision").value = r;
        },500);

    </script>
    <!-- IMAGE PICKER
      <link rel="stylesheet" href="css/image-picker.css">
      <script src="js/image-picker.min.js"></script>
      <script type="text/javascript">
      $("#consola").imagepicker()
      </script>
      <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
@endsection
@endsection