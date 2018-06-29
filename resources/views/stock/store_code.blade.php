@extends('layouts.master-layouts')

@section('container')

<div class="container">
  <h1>Gift Cards</h1>
  @if (count($errors) > 0)
				<div class="alert alert-danger text-center">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
	@endif
  <form method="POST" name="form_store_codes" action="{{ url('stock_insertar_codigo') }}">
    {{ csrf_field() }}
    <div class="col-md-6" style="padding-right: 15px !important;padding-left: 10px !important">
      <div class="row">
        <div class="col-sm-12" style="text-align:right;">
          <span id="alerta" class="label label-danger"></span>
          <img class="img-rounded pull-left" width="100" id="image-swap" src="" alt="" />
        </div>

        <div class="col-sm-12">

          <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value="" />
          <br />
          <br />

          <input type="text" id="clientes_id1" name="clientes_id1" value="" hidden="" />
          <input type="text" id="clientes_id2" name="clientes_id2" value="" hidden="" />

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

          <div class="col-md-4">
            <div class="input-group form-group" id="div_costo_usd">
            <span class="input-group-addon">usd</span>
              <input class="form-control" type="text" name="costo_usd" id="multiplicando" value="">
            </div>
          </div>


          <div class="col-md-4">
            <div class="input-group form-group">
              <span class="input-group-addon">ctz</span>
              <input class="form-control" type="text" id="multiplicador" value="{{ $cotiz }}">
            </div><!-- /input-group -->
          </div><!-- /.col-lg-6 -->

          <div class="col-md-4">
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
              <input class="form-control" type="text" name="costo" id="resultado" value="" style="text-align:right; color: #777" readonly>
            </div>
          </div>

          <p>.</p>

          <div class="col-sm-12">
            <textarea rows="5" id="cod_bruto" name="cod_bruto" class="form-control">{{ old('cod_bruto') }}</textarea><br>
            <!-- no uso mas el boton <input class="btn btn-xs btn-normal" type="button" onClick="javascript:convertirProveedor()" value="Convertir Proveedor" /> -->
          </div>


          <div class="col-sm-12">
            <button class="btn btn-primary btn-lg btn-block" type="submit">Guardar</button>
          </div>

        </div>
      </div>
    </div>

    <div class="col-md-6"  style="padding-right: 10px !important;padding-left: 15px !important">
      <div class="row" id="container_codes">


      </div>
    </div>
  </form>

</div>

@stop
@section('scripts')
  <script type="text/javascript" src="js/typeahead.bundle.js"></script>

  <script type="text/javascript">
    $(document).ready(function(){
      // Defining the local dataset
      var cars = <?php echo $giftCards; ?>;
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


      $("#clientes").blur(function(){
        if(this.value==""){
          this.value = arr[0];
        }
      });


      createColumn($('textarea[name=cod_bruto]'));
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

      window.setInterval(function() {
        var titulo = document.getElementById('clientes_id1').value;
        var consola = document.getElementById('clientes_id2').value;

        $("#image-swap").attr("src", (titulo !== "" &&  + consola !== "") ? "/img/productos/" + consola + "/" + titulo + ".jpg" : "");

        $('#image-swap').load(function() {
          document.getElementById("alerta").innerHTML = "";
        });

        $('#image-swap').error(function() {
          document.getElementById("alerta").innerHTML = "no se encuentra";
        });
      },500);



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


  </script>

@stop
