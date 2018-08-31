<div class="container">
	<h1 style="color: #000">Agregar Cobro (vta #{{$venta_stock->ID}}) </h1>
  <div class="row">
      <div class="col-sm-3">
  	   <img class="img-rounded pull-right" width="100" src="{{asset('img/productos')}}/{{ $venta_stock->consola }}/{{ $venta_stock->titulo }}.jpg" alt="{{ $venta_stock->titulo }} - {{ $venta_stock->consola }}" />
      </div>
      <div class="col-sm-6">
      <form method="post" name="form1" id="form1" action="{{ url('customer_addVentasCobro') }}">
        {{ csrf_field() }}
        <input type="text" id="clientes_id" name="clientes_id" value="{{ $cliente->ID }}" hidden>
      	<input type="text" id="ventas_id" name="ventas_id" value="{{ $venta_stock->ID }}" hidden>
              
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
                   
  			<br />

  			<div class="col-sm-5">
              <div class="input-group form-group">
                <span class="input-group-addon">precio</span>
                <input class="form-control" type="text" id="precio" name="precio" value="">
  			</div>
            	</div>
              
              <div class="col-sm-3" style="opacity:0.7">
              <div class="input-group form-group">
              <select id="porcentaje" class="form-control">
              	<option value="0.12">12 %</option>
                  <option selected value="0.0538">6 %</option>
                  <option value="0.00">0 %</option>
              </select>  
              </div>
              </div>
              
  			<div class="col-sm-4">
              <div class="input-group form-group">
                  <span class="input-group-addon">comision</span>
                  <input class="form-control" type="text" id="comision" name="comision" value="">
              </div>
              </div>
                          
              <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro">
              </div>

              <button class="btn btn-primary botonero" id="submiter" type="submit">Insertar</button>
          
      </form>
      </div>
      <div class="col-sm-3">
      </div>
  </div>
  <br><br>
     <!--/row-->
</div><!--/.container-->

<script>
  $(document).ready(function() {
    $("form :input").change(function() {
      var val = $('#medio_cobro').val();
      //alert(val2); 
      if (val == "Deposito/Transferencia") {
        $("#porcentaje").html("<option value='0.00'>0%</option>");
      } else {
        let html = '<option value="0.12">12 %</option><option selected value="0.0538">6 %</option><option value="0.00">0 %</option>';
        $("#porcentaje").html(html);
      } 
    });

    window.setInterval(function() {
      m1 = document.getElementById("precio").value;
      m2 = document.getElementById("porcentaje").value;
      r = m1*m2;
      document.getElementById("comision").value = r;
    },500);

    // To style only <select>s with the selectpicker class
    $('.selectpicker').selectpicker();


  });
</script>