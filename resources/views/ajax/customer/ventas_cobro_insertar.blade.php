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

        @if(\Helper::validateAdministrator(session()->get('usuario')->Level))

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
          <input value="{{ date('Y-m-d',strtotime($venta_stock->Day)) }}" class="form-control" type="date" id="Day" name="Day" placeholder="Fecha Cobro">
        </div>

        @endif
              
             <div class="input-group form-group">
             <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
             <input type="hidden" id="medio_venta" value="{{ $venta_stock->medio_venta }}">
             <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
              @if ($venta_stock->medio_venta == 'Web' || $venta_stock->medio_venta == 'Mail')
                <option value="MP" data-content="<span class='label label-primary'>MP</span>">MP</option>
                <option value="MP - Banco" data-content="<span class='label label-warning'>MP - Banco</span>">MP - Banco</option>
                <option value="MP - Tarjeta" data-content="<span class='label label-primary'>MP - Tarjeta</span>">MP - Tarjeta</option>
                <option value="MP - Ticket" data-content="<span class='label label-success'>MP - Ticket</span>">MP - Ticket</option>
                <option value="Banco" data-content="<span class='label label-info'>Banco</span>">Banco</option>
              @else
                <option value="MP" data-content="<span class='label label-primary'>MP</span>">MP</option>
                <option value="MP - Banco" data-content="<span class='label label-warning'>MP - Banco</span>">MP - Banco</option>
                <option value="MP - Tarjeta" data-content="<span class='label label-primary'>MP - Tarjeta</span>">MP - Tarjeta</option>
                <option value="MP - Ticket" data-content="<span class='label label-success'>MP - Ticket</span>">MP - Ticket</option>
              @endif
              </select>                
              </div> 
              
        <div class="input-group form-group" id="n_cobro">
                <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
                <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" onchange="limpiarReferencia(this.value)" placeholder="Ref. de Cobro">             
              </div>
              <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i> completar</span>
                   
        <br />

        <div class="col-sm-5">
          <label for="">&nbsp;</label>
              <div class="input-group form-group">
                <span class="input-group-addon">$</span>
                <input class="form-control" type="text" id="precio" name="precio" value="">
        </div>
              </div>
              
              <div class="col-sm-3" style="opacity:0.7">
                <label for="">&nbsp;</label>
              <div class="input-group form-group">
              <select id="porcentaje" class="form-control">
                <option value="0.13">13 %</option>
                  <option value="0.0538">6 %</option>
                  <option value="0.00">0 %</option>
              </select>  
              </div>
              </div>
              
        <div class="col-sm-4">
          <input type="checkbox" id="calculo_automatico" checked> <label for="calculo_automatico" style="color: #000">Comisión Automatica</label>
              <div class="input-group form-group">
                  <span class="input-group-addon">comision</span>
                  <input class="form-control" type="text" id="comision" name="comision" value="">
              </div>
              
              </div>
                          
              <!-- <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro">
              </div> --> <!-- SE COMENTA TEMPORALMENTE EL DÍA 07/03/2019 POR ORDEN DE VICTOR -->

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
      /*var val = $('#medio_cobro').val();
      //alert(val2); 
      if (val == "Banco") {
        $("#porcentaje").html("<option value='0.00'>0%</option>");
      } else {
        let html = '<option value="0.13" selected>13 %</option><option value="0.0538">6 %</option><option value="0.00">0 %</option>';
        $("#porcentaje").html(html);
      } */
      var val = $('#medio_venta').val();
      var val2 = $('#medio_cobro').val();
      //alert(val2);
      if (val == "MercadoLibre") {
          $("#porcentaje").html("<option value='0.13'>13 %</option>");
      } else if (val == "Mail" && (val2 == "MP" || val2 == "MP - Tarjeta" || val2 == "MP - Ticket" || val2 == "MP - Banco")) {
          $("#porcentaje").html("<option value='0.0538'>6 %</option>");
      } else if (val == "Mail" && val2 == "Banco") {
          $("#porcentaje").html("<option value='0.00'>0 %</option>");
      } else if (val == "Web" && (val2 == "MP" || val2 == "MP - Tarjeta" || val2 == "MP - Ticket" || val2 == "MP - Banco")) {
          $("#porcentaje").html("<option value='0.0538'>6 %</option>");
      } else if (val == "Web" && val2 == "Banco") {
          $("#porcentaje").html("<option value='0.00'>0 %</option>");
      }
    });

    setTimeout(function(){
      document.getElementById('Day').focus();
    }, 600);

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
      if ($('#calculo_automatico').is(':checked')) {
        m1 = document.getElementById("precio").value;
        m2 = document.getElementById("porcentaje").value;
        r = m1*m2;
        document.getElementById("comision").value = r;
      }
    },500);

    // To style only <select>s with the selectpicker class
    $('.selectpicker').selectpicker();


  });

  function isNum(carac) {
    var regex = /^(\d+)$/g;
    return regex.test(carac);
  }

  function limpiarReferencia(ref_cobro) {
    document.getElementById('ref_cobro').value = ref_cobro.trim();
  }

</script>