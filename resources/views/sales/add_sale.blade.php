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
                echo '<link href="'.asset('css/bootstrap.css').'" rel="stylesheet">';
                $insertGoTo = url('salesInsertWeb', [$_GET['order_item_id'], $colname_rsTIT, $colname_rsCON, $colname_rsSlot]);
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
                            <option selected value="MP"
                                    data-content="<span class='label label-primary'>MP</span>">MP
                            </option>
                            <option value="MP - Tarjeta"
                                    data-content="<span class='label label-primary'>MP - Tarjeta</span>">MP - Tarjeta
                            </option>
                            <option value="MP - Ticket"
                                    data-content="<span class='label label-success'>MP - Ticket</span>">MP - Ticket
                            </option>
                            <option value="Banco"
                                    data-content="<span class='label label-info'>Banco</span>">
                                Banco
                            </option>
                            <option value="Fondos"
                                    data-content="<span class='label label-normal'>Fondos</span>">
                                Banco
                            </option>
                        </select>
                    </div>
                    <div class="input-group form-group" id="n_cobro">
                        <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                        <input class="form-control" type="text" name="ref_cobro" id="ref_cobro"
                               placeholder="N° de Cobro">
                    </div>
                    <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil"
                                                                              aria-hidden="true"></i> Completar Nº de cobro</span>

                    <div class="input-group form-group" id="order_ml">
                        <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                        <input class="form-control" type="text" name="order_id_ml" id="order_id_ml"
                               placeholder="Order_id_ml">
                    </div>
                    <span id="faltaml" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil"
                                                                              aria-hidden="true"></i> Completar Order_id_ml</span>

                    <span class="text-danger" style="display: none" id="alert-error"></span>
                    <div class="input-group form-group" id="order_item" style="display: none">
                        <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                        <input class="form-control" type="text" name="order_item_id" id="order_item_id"
                               placeholder="Order_item_id" onblur="verificarOii(this.value)">
                    </div>
                    <span id="faltaitem" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil"
                                                                              aria-hidden="true"></i> Completar Order_item_id</span>

                    <div class="input-group form-group" id="order_web" style="display: none">
                        <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                        <input class="form-control" type="text" name="order_id_web" id="order_id_web"
                               placeholder="Order_id_web" readonly>
                    </div>
                    <span id="faltaweb" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil"
                                                                              aria-hidden="true"></i> Completar Order_id_web</span>
                    

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

                    <button class="btn btn-primary botonero" id="submiter" type="button">Insertar</button>
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

            $('#submiter').on('click', function(){
                $(this).prop('disabled', true);

                $('#form1').submit();
            });

            // To style only <select>s with the selectpicker class
            // $('.selectpicker').selectpicker();
        });
    </script>
    <script type="text/javascript">
        var isFormValid = false;
        $('#form1').submit(function() {

            if (!isFormValid) {
                if($('#ref_cobro').val() == ''){
                    $('#submiter').prop('disabled', false);
                    document.getElementById("n_cobro").className = "input-group form-group has-error";
                    $("#faltacobro").show(300);
                    
                    return false;
                } else if ($('#order_id_ml').val() == '' && $('#order_id_ml').is(':visible')) {
                    $('#submiter').prop('disabled', false);
                    document.getElementById("order_ml").className = "input-group form-group has-error";
                    $("#faltaml").show(300);
                    
                    return false;
                } else if ($('#order_item_id').val() == '' && $('#order_item_id').is(':visible')) {
                    $('#submiter').prop('disabled', false);
                    document.getElementById("order_item").className = "input-group form-group has-error";
                    $("#faltaitem").show(300);
                    
                    return false;
                } else if ($('#order_id_web').val() == '' && $('#order_id_web').is(':visible')) {
                    $('#submiter').prop('disabled', false);
                    document.getElementById("order_web").className = "input-group form-group has-error";
                    $("#faltaweb").show(300);
                    
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
            document.getElementById("clientes_id").value = String.trim();
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
                    $('#order_ml').show();
                    $('#order_item').hide().val('');
                    $('#order_web').hide().val('');
                } else if (val == "Mail" && (val2 == "MP" || val2 == "MP - Tarjeta" || val2 == "MP - Ticket")) {
                    $("#porcentaje").html("<option value='0.0538'>6 %</option>");
                    $('#order_ml').hide().val('');
                    $('#order_item').hide().val('');
                    $('#order_web').hide().val('');
                } else if (val == "Mail" && val2 == "Banco") {
                    $("#porcentaje").html("<option value='0.00'>0 %</option>");
                    $('#order_ml').hide().val('');
                    $('#order_item').hide().val('');
                    $('#order_web').hide().val('');
                } else if (val == "Web" && (val2 == "MP" || val2 == "MP - Tarjeta" || val2 == "MP - Ticket")) {
                    $("#porcentaje").html("<option value='0.0538'>6 %</option>");
                    $('#order_ml').hide().val('');
                    $('#order_item').show();
                    $('#order_web').show();
                } else if (val == "Web" && val2 == "Banco") {
                    $("#porcentaje").html("<option value='0.00'>0 %</option>");
                    $('#order_ml').hide().val('');
                    $('#order_item').show();
                    $('#order_web').show();
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


        function verificarOii(oii) {
            let clientes_id = $('#clientes_id').val();
            $('#alert-error').hide();

            if (oii != "") {
                $.ajax({
                    url: '{{ url('verificarOii') }}/'+oii+'/'+clientes_id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response){
                        switch(response.status) {
                            case 1:
                                var html2 = `El orden_item_id pertenece al cliente <b><a href="{{ url('clientes') }}/${response.existInVentas.clientes_id}" style="color:#bb4442"> #${response.existInVentas.clientes_id} </a></b>`;

                                $('#alert-error').html(html2).fadeIn();
                                break;
                            case 2:

                                $('#order_id_web').val(response.datosOii.order_id);

                                break;
                            default:

                                var html2 = `El orden_item_id no existe.`;

                                $('#alert-error').text(html2).fadeIn();
                                break;
                        }
                    },
                    error: function(error){
                        console.log(error);
                        var html = `<p>Ha ocurrido un error inesperado al obtener el order_id_web.</p>`;

                        $('#alert-error').html(html).fadeIn();
                    }
                });
            }
            
        }

    </script>
@endsection
@endsection