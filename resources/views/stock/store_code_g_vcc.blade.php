@extends('layouts.master-layouts')

@section('title', 'Gift Cards G - VCC')

@section('container')

<div class="container">
  <h1>Gift Cards G - VCC</h1>
  <h4>Gift Cards Cargadas: <span id="cant_giftcards"></span></h4>
  @if (count($errors) > 0)
				<div class="alert alert-danger text-center">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
	@endif
  <div class="row">
    <div class="col-md-12">
        <form method="POST" name="form_store_codes" action="{{ url('stock_insertar_codigo_g') }}">
            {{ csrf_field() }}
        
            <div class="col-md-9">

                <div class="col-md-12" style="padding-right: 15px !important;padding-left: 10px !important">
                  <div class="row">

                    <div class="col-sm-12">

                      <div class="input-group form-group">

                        <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>

                        <select id="medio_pago" name="medio_pago" class="selectpicker form-control">
                          <option selected value="BiP" data-content="<span class='label label-primary'>BiP</span>">BiP</option>
                          <option value="MC Uala" data-content="<span class='label label-info'>MC U</span>">MC U</option>
                          <option value="MC MercadoPago" data-content="<span class='label label-info'>MC MP</span>">MC MP</option>
                          <option value="MC Galicia" data-content="<span class='label label-default'>MC G</span>">MC G</option>
                          <option value="VISA Galicia" data-content="<span class='label label-default'>VISA G</span>">VISA G</option>
                          <option value="Debito Galicia" data-content="<span class='label label-success'>Deb G</span>">Deb G</option>
                          <option value="Efectivo" data-content="<span class='label label-success'>Efectivo</span>">Efectivo</option>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-12">
                      <div class="input-group form-group" id="div_order">
                        <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                        <input class="form-control input-sm" id="n_order" type="text" name="n_order" placeholder="n° de compra">
                      </div>
                  </div>

                    <div class="col-md-12">
                      <button type="button" style="margin-bottom: 20px" class="btn btn-primary btn-sm" onclick="agregarGift()"><i class="fa fa-plus"></i> Nueva Gift</button>
                      <div id="container-gifts">
                          <div class="filas" id="fila1" class="row">
                              <div class="col-md-3">
                                <div class="input-group form-group">
                                  <span class="input-group-addon">Valor #1</span>
                                  <input type="text" class="form-control input-sm" name="valor_1_1" id="valor_1_1">
                                </div>
                              </div>
      
                              <div class="col-md-3">
                                  <div class="input-group form-group">
                                  <span class="input-group-addon">Valor #2</span>
                                  <input type="text" class="form-control input-sm" name="valor_2_1" id="valor_2_1">
                                </div>
                              </div>
      
                              <div class="col-md-3">
                                  <div class="input-group form-group">
                                  <span class="input-group-addon">Valor #3</span>
                                  <input type="text" class="form-control input-sm" name="valor_3_1" id="valor_3_1">
                                </div>
                              </div>
      
                              <div class="col-md-3">
                                  <div class="input-group form-group">
                                  <span class="input-group-addon">Cant</span>
                                  <input type="text" class="form-control input-sm" name="cant_1" id="cant_1">
                                </div>
                              </div>
                              
                              <div class="col-md-4">
                                <div class="input-group form-group" id="div_costo_usd">
                                  <span class="input-group-addon">usd</span>
                                  <input class="form-control input-sm" data-id="1" onchange="calcularResultado(this)" type="text" name="costo_usd_1" id="multiplicando_1" value="">
                                </div>
                              </div>
            
                              <div class="col-md-4">
                                <div class="input-group form-group">
                                  <span class="input-group-addon">ctz</span>
                                  <input class="form-control input-sm" data-id="1" onchange="calcularResultado(this)" type="text" id="multiplicador_1" value="{{ $cotiz }}">
                                </div><!-- /input-group -->
                              </div><!-- /.col-lg-6 -->
            
                              <div class="col-md-4">
                                <div class="input-group form-group">
                                  <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
                                  <input class="form-control input-sm" type="text" name="costo_1" id="resultado_1" value="" style="text-align:right; color: #777" readonly>
                                </div>
                              </div>

                              <div class="col-md-12">
                                <hr>
                              </div>
                            </div>
                      </div>
                      <button style="margin-bottom: 10px" type="button" class="btn btn-default btn-sm pull-right" onclick="construirCodes()"><i class="fa fa-book"></i> Generar códigos</button>
                    </div>

                    <textarea name="excel_data" id="excel_data" cols="30" rows="10" hidden></textarea>

                    <div class="col-sm-12">
                        <button class="btn btn-primary btn-lg btn-block" type="submit">Guardar</button>
                        <br>
                    </div>
                    
                  </div>
                </div>

            </div>
            <div class="col-md-3" >
                <div class="row" id="container_codes" style="padding-left: 10px;"></div>
            </div>
        </form>
    </div>
  </div>



</div>

@stop
@section('scripts')
  <script type="text/javascript" src="js/typeahead.bundle.js"></script>
  <script type="text/javascript">
    var gifts = 2;
    var ctz = '{{ $cotiz }}';
    $(document).ready(function(){

      // createColumn($('#excel_data'),'code_g');
    	// selecciono los datos dl campo input cliente y quito los espacios en blanco y coloco guion medio, paso todo a minuscula
      $("#clientes").blur(function(){
        cliente = document.getElementById("clientes").value;
        client = cliente.replace(/\s+/g, '-').toLowerCase();

        // selecciono la informacion titulo, consola e ID del campo y actualizo los inputs con los datos del select
        var Strin = client.substring(client.lastIndexOf('<-')+2,client.lastIndexOf('->'));
        var Stri = client.substring(client.lastIndexOf('(')+1,client.lastIndexOf(')'));
        var String = client.substring(client.lastIndexOf('[')+1,client.lastIndexOf(']'));
        document.getElementById("clientes_id1").value = Strin;
        document.getElementById("clientes_id2").value = Stri;
        document.getElementById("multiplicando").value = String;
      })

      $('input[type="text"]').change(function(){
          this.value = $.trim(this.value);
      });




      // initial test
			highlightDuplicates();

			// fix for newer jQuery versions!
			// since you can select by value, but not by current val
			$('form').find('input').bind('input',function() {
				$(this).attr('value',this.value)
			});

			// bind test on any change event
			$('form').find('input').on('input',highlightDuplicates);





    });

    function agregarGift() {
      var html = '<div class="filas" id="fila'+gifts+'" class="row"> <div class="col-md-3"> <div class="input-group form-group"> <span class="input-group-addon">Valor #1</span> <input type="text" class="form-control input-sm" name="valor_1_'+gifts+'" id="valor_1_'+gifts+'"> </div></div><div class="col-md-3"> <div class="input-group form-group"> <span class="input-group-addon">Valor #2</span> <input type="text" class="form-control input-sm" name="valor_2_'+gifts+'" id="valor_2_'+gifts+'"> </div></div><div class="col-md-3"> <div class="input-group form-group"> <span class="input-group-addon">Valor #3</span> <input type="text" class="form-control input-sm" name="valor_3_'+gifts+'" id="valor_3_'+gifts+'"> </div></div><div class="col-md-2"> <div class="input-group form-group"> <span class="input-group-addon">Cant</span> <input type="text" class="form-control input-sm" name="cant_'+gifts+'" id="cant_'+gifts+'"> </div></div><div class="col-md-1"> <button type="button" onclick="eliminarGift(this)" class="btn btn-link btn-sm" data-delete="'+gifts+'"><i class="fa fa-times text-danger"></i></button> </div><div class="col-md-4"> <div class="input-group form-group" id="div_costo_usd"> <span class="input-group-addon">usd</span> <input class="form-control input-sm" type="text" name="costo_usd_'+gifts+'" data-id="'+gifts+'" onchange="calcularResultado(this)" id="multiplicando_'+gifts+'" value=""> </div></div><div class="col-md-4"> <div class="input-group form-group"> <span class="input-group-addon">ctz</span> <input class="form-control input-sm" type="text" data-id="'+gifts+'" onchange="calcularResultado(this)" id="multiplicador_'+gifts+'" value="'+ctz+'"> </div></div><div class="col-md-4"> <div class="input-group form-group"> <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span> <input class="form-control input-sm" type="text" name="costo_'+gifts+'" id="resultado_'+gifts+'" value="" style="text-align:right; color: #777" readonly> </div></div><div class="col-md-12"> <hr> </div></div>';

      $('#container-gifts').append(html);

      gifts++;
    }

    function eliminarGift(ele) {
      var id = $(ele).data();

      $('#fila'+id.delete).remove();

      gifts--;
      
    }

    function construirCodes() {
      var codes = '';
      var filas = $('.filas').length;

      for (let i = 1; i <= filas; i++) {
        var valor1 = $('#valor_1_'+i).val(),
            valor2 = $('#valor_2_'+i).val(),
            valor3 = $('#valor_3_'+i).val(),
            usd = $('#multiplicando_'+i).val(),
            cant = $('#cant_'+i).val();

            for (let j = 0; j < cant; j++) {
              codes += valor1 + ' ' + valor2 + ' ' + valor3 + ' ' + usd + getLetter(j);
              if (j != (cant - 1)) {
                codes += "\n";
              }
            }
      }

      codes = codes.replace(/-/g, "");

      $('#excel_data').val(codes);

      createColumn($('#excel_data'),'code_g');
    }

    function getLetter(pos) {
      var abc = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];

      return abc[pos];
    }

    function calcularResultado(selector) {
      var pos = $(selector).data().id;

      let multiplicando = $('#multiplicando_'+pos).val();
      let multiplicador = $("#multiplicador_"+pos).val();
      let r = multiplicando*multiplicador;
      $('#resultado_'+pos).val(r);
    }


  </script>

@stop
