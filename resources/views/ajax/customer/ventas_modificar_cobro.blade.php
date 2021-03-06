<div class="container">
	<h1 class="text-center" style="color: #000">Modificar cobro #{{ $ventas_cobro->ID }}</h1>
	<div class="alert alert-danger text-center" id="alert-ventasInsert" style="display:none"></div>
    <!-- InstanceBeginEditable name="body" -->
	<div class="row">
	    <div class="col-sm-3">
	    </div>
	    <div class="col-sm-6">
	    <form method="post" name="form1" id="form1" action="{{ url('customer_ventas_cobro_modificar') }}">

	    	{{ csrf_field() }}

	    	@if(\Helper::validateAdministrator(session()->get('usuario')->Level))

	    	<div class="input-group form-group">
	    	  <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
	    	  <input value="{{ date('Y-m-d',strtotime($ventas_cobro->Day)) }}" class="form-control" type="date" id="Day" name="Day" placeholder="Fecha Cobro">
	    	  <input type="hidden" name="fecha_old" value="{{$ventas_cobro->Day}}">
	    	  <span class="input-group-addon"><em class="text-muted">Actual: {{ date('d/m/Y', strtotime($ventas_cobro->Day)) }}</em></span>
	    	</div>

	    	@endif
	    		
	    		@php $colorcons = ''; @endphp
	            @if (strpos($ventas_cobro->medio_cobro, 'Ticket') !== false) @php $colorcons = 'success'; @endphp
	            @elseif (strpos($ventas_cobro->medio_cobro, 'Mercado') !== false) @php $colorcons = 'primary'; @endphp
	            @elseif (strpos($ventas_cobro->medio_cobro, 'Transferencia') !== false) @php $colorcons = 'info'; @endphp
	            @endif

	            <div class="input-group form-group">
	              <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
	              <input type="hidden" id="medio_venta" value="{{ $ventas_cobro->medio_venta }}">
	              <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
	              		<option value="{{ $ventas_cobro->medio_cobro }}" selected="selected" data-content="<span class='label label-{{ $colorcons }}'>{{ $ventas_cobro->medio_cobro }}</span> - <span class='label label-success'>Actual</span>">{{ $ventas_cobro->medio_cobro }} - Actual</option>
					  @if ($ventas_cobro->medio_venta == 'Web' || $ventas_cobro->medio_venta == 'Mail')
						  @foreach($medios_cobros as $data)
							  <option data-commission="{{$data->commission}}" data-content="<span class='label label-{{$data->color}}'>{{$data->name}}</span>">{{$data->name}}</option>
						  @endforeach
					  @else
						  @if(strpos($data->name,"MP") !== false)
							  <option data-commission="{{$data->commission}}" data-content="<span class='label label-{{$data->color}}'>{{$data->name}}</span>">{{$data->name}}</option>
						  @endif
					  @endif
	              </select>
	            </div>
	            
				<div class="input-group form-group" id="n_cobro">
	              <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
	              <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" onchange="limpiarReferencia(this.value)" placeholder="Ref. de Cobro" value="{{ $ventas_cobro->ref_cobro }}">             
	            </div>
	            <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i> completar</span>
	                 
				<br />
				
				
				{{-- // si es admin permito modificar precio y comision --}}
				@if (Helper::validateAdminAnalyst(session()->get('usuario')->Level))
				<div class="col-md-5">
					<label for="">&nbsp;</label>
	            <div class="input-group form-group">
	              <span class="input-group-addon">$</span>
	              <input class="form-control" type="text" id="precio" name="precio" value="{{ $ventas_cobro->precio }}">
	              <span class="input-group-addon">{{ $ventas_cobro->precio }}</span>
				</div>
	          	</div>
	            
	            <div class="col-md-3" style="opacity:0.7">
	            	<label for="">&nbsp;</label>
	            <div class="input-group form-group">
	            <select id="porcentaje" class="form-control">
	            	<option @if(strpos($ventas_cobro->medio_cobro, 'MP') !== false || strpos($ventas_cobro->medio_cobro, 'MercadoPago') !== false) selected @endif value="0.13">13 %</option>
	                <option @if(strpos($ventas_cobro->medio_cobro, 'Banco') !== false) selected @endif value="0.0538">6 %</option>
	                <option value="0.00">0 %</option>
	            </select>  
	            </div>
	            </div>
	            
				<div class="col-md-4">
					<input type="checkbox" id="calculo_automatico" checked> <label for="calculo_automatico" style="color: #000">Comisión Automatica</label>
	            <div class="input-group form-group">
	                <input class="form-control" type="text" id="comision" name="comision" value="{{ $ventas_cobro->comision }}">
	                <span class="input-group-addon">{{ $ventas_cobro->comision }}</span>
	            </div>
	            
	            </div>
				 {{-- Si no es Admin oculto los campos de precio y comision para que no se puedan modificar --}}
				@else
					<input class="form-control" type="hidden" id="precio" name="precio" value="{{ $ventas_cobro->precio }}">
					<input class="form-control" type="hidden" id="comision" name="comision" value="{{ $ventas_cobro->comision }}">
				@endif
	                        
	            {{--<div class="input-group form-group">
	              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
					
	              <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro" value="{{ $ventas_cobro->Notas }}">
	            </div>--}}

	            <button class="btn btn-primary" id="submiter" type="button">Modificar</button>
	        <input type="hidden" name="ID" value="{{ $ventas_cobro->ID }}">
	        <input type="hidden" name="clientes_id" value="{{ $ventas_cobro->clientes_id }}">
	    </form>
	    </div>
	    <div class="col-sm-3">
	    </div>
	</div>
	     <!--/row-->
     <!-- InstanceEndEditable -->
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
		var commission = $("#medio_cobro option:selected").data('commission');
		var commissionTxt = Math.ceil((parseFloat(commission) * 100));
		if (val == "MercadoLibre") {
			$("#porcentaje").html("<option value='0.13'>13 %</option>");
		} else if (val == "Mail" && (val2 == "MP" || val2 == "MP - Tarjeta" || val2 == "MP - Ticket" || val2 == "MP - Banco")) {
			$("#porcentaje").html("<option value='"+commission+"'>"+commissionTxt+" %</option>");
		} else if (val == "Mail" && val2 == "Banco") {
			$("#porcentaje").html("<option value='"+commission+"'>"+commissionTxt+" %</option>");
		} else if (val == "Web" && (val2 == "MP" || val2 == "MP - Tarjeta" || val2 == "MP - Ticket" || val2 == "MP - Banco")) {
			$("#porcentaje").html("<option value='"+commission+"'>"+commissionTxt+" %</option>");
		} else if (val == "Web" && val2 == "Banco") {
			$("#porcentaje").html("<option value='"+commission+"'>"+commissionTxt+" %</option>");
		} else {
			$("#porcentaje").html("<option value='"+commission+"'>"+commissionTxt+" %</option>");
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
        if (medio_cobro.indexOf("MP") >= 0) {
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
			  console.log(medio_cobro);
			  
            if (medio_cobro != 'PayPal') {
				if (isNum(ref_cobro)) {

					$('#form1').submit();
				} else {
					$('#alert-ventasInsert').html('<p>Ref. de cobro no es valido.</p>').fadeIn();
					$btn.prop('disabled', false);
				}
			} else {
				$('#form1').submit();
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