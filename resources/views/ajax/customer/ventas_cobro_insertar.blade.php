<div class="container">
	<h1 style="color: #000">Agregar Cobro (vta #{{$venta_stock->ID}}) </h1>

  <div class="alert alert-danger text-center" id="alert-ventasInsert" style="display:none"></div>
  <div class="row">
      <div class="col-sm-3">
  	   <img class="img-rounded pull-right" width="100" src="/img/productos/{{ $venta_stock->consola }}/{{ $venta_stock->titulo }}.jpg" alt="{{ $venta_stock->titulo }} - {{ $venta_stock->consola }}" />
      </div>
      <div class="col-sm-6">
      <form method="post" name="form1" id="form1" action="{{ url('customer_addVentasCobro') }}">
        {{ csrf_field() }}
        <input type="text" id="clientes_id" name="clientes_id" value="{{ $cliente->ID }}" hidden>
      	<input type="text" id="ventas_id" name="ventas_id" value="{{ $venta_stock->ID }}" hidden>
              
             <div class="input-group form-group">
             <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
             <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
              @if ($venta_stock->medio_venta == 'Web' || $venta_stock->medio_venta == 'Mail')
                <option value="MP" data-content="<span class='label label-primary'>MP</span>">MP</option>
                <option value="MP - Tarjeta" data-content="<span class='label label-primary'>MP - Tarjeta</span>">MP - Tarjeta</option>
                <option value="MP - Ticket" data-content="<span class='label label-success'>MP - Ticket</span>">MP - Ticket</option>
                <option value="Banco" data-content="<span class='label label-info'>Banco</span>">Banco</option>
                <option value="Fondos" data-content="<span class='label label-normal'>Fondos</span>">Fondos</option>
              @else
                <option value="MP" data-content="<span class='label label-primary'>MP</span>">MP</option>
                <option value="MP - Tarjeta" data-content="<span class='label label-primary'>MP - Tarjeta</span>">MP - Tarjeta</option>
                <option value="MP - Ticket" data-content="<span class='label label-success'>MP - Ticket</span>">MP - Ticket</option>
              @endif
              </select>                
              </div> 
              
  			<div class="input-group form-group" id="n_cobro">
                <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" placeholder="Ref. de Cobro">             
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
              	<option value="0.13">13 %</option>
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

              <button class="btn btn-primary botonero" id="submiter" type="button">Insertar</button>
          
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
      if (val == "Banco") {
        $("#porcentaje").html("<option value='0.00'>0%</option>");
      } else {
        let html = '<option value="0.13">13 %</option><option selected value="0.0538">6 %</option><option value="0.00">0 %</option>';
        $("#porcentaje").html(html);
      } 
    });

    $('#submiter').on('click', function(){
      var $btn = $(this);
      $btn.prop('disabled', true);
      var ref_cobro = $('#ref_cobro').val(),
          precio = $('#precio').val(),
          comision = $('#comision').val(),
          medio_cobro = $('#medio_cobro').val();

      $('#alert-ventasInsert').fadeOut();

      if (precio != "" && comision != "") {
        if (medio_cobro.indexOf("Mercado") >= 0) {
          if (ref_cobro != "") {
            if (isNum(ref_cobro)) {

              $('#form1').submit();
            } else {
              $('#alert-ventasInsert').html('<p>Ref. de cobro no es valido.</p>').fadeIn();
              $btn.prop('disabled', false);
            }
          } else {
            $('#alert-ventasInsert').html('<p>Ref. de cobro es obligatorio para MercadoPago.</p>').fadeIn();
            $btn.prop('disabled', false);
          }
        } else {
          if (ref_cobro != "") {
            if (isNum(ref_cobro)) {

              $('#form1').submit();
            } else {
              $('#alert-ventasInsert').html('<p>Ref. de cobro no es valido.</p>').fadeIn();
              $btn.prop('disabled', false);
            }
          } else {
            $('#form1').submit();
          }
        }
      } else {
        $('#alert-ventasInsert').html('<p>Has dejado vacío campos obligatorios.</p>').fadeIn();
        $btn.prop('disabled', false);
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

  function isNum(carac) {
    var regex = /^(\d+)$/g;
    return regex.test(carac);
  }

</script>