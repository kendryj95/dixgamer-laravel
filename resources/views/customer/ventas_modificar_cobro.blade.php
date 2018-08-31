@extends('layouts.master-layouts')

@section('container')

<div class="container">
	<h1 class="text-center">Modificar cobro #{{ $ventas_cobro->ventas_id }}</h1>
	@if (count($errors) > 0)
	      <div class="alert alert-danger text-center">
	        <ul>
	          @foreach ($errors->all() as $error)
	            <li>{{ $error }}</li>
	          @endforeach
	        </ul>
	      </div>
	@endif
    <!-- InstanceBeginEditable name="body" -->
	<div class="row">
	    <div class="col-sm-3">
	    </div>
	    <div class="col-sm-6">
	    <form method="post" name="form1" action="{{ url('customer_ventas_cobro_modificar') }}">

	    	{{ csrf_field() }}
	    		
	    		@php $colorcons = ''; @endphp
	            @if (strpos($ventas_cobro->medio_cobro, 'Ticket') !== false) @php $colorcons = 'success'; @endphp
	            @elseif (strpos($ventas_cobro->medio_cobro, 'Mercado') !== false) @php $colorcons = 'primary'; @endphp
	            @elseif (strpos($ventas_cobro->medio_cobro, 'Transferencia') !== false) @php $colorcons = 'info'; @endphp
	            @endif

	            <div class="input-group form-group">
	              <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span>
	              <select id="medio_cobro" name="medio_cobro" class="selectpicker form-control">
	              		<option value="{{ $ventas_cobro->medio_cobro }}" selected="selected" data-content="<span class='label label-{{ $colorcons }}'>{{ $ventas_cobro->medio_cobro }}</span> - <span class='label label-success'>Actual</span>">{{ $ventas_cobro->medio_cobro }} - Actual</option>
	              		@if ($ventas_cobro->medio_venta == 'Web' || $ventas_cobro->medio_venta == 'Mail')
		                    <option value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
		                    <option value="MercadoPago - Tarjeta" data-content="<span class='label label-primary'>MercadoPago - Tarjeta</span>">MercadoPago - Tarjeta</option>
		                    <option value="MercadoPago - Ticket" data-content="<span class='label label-success'>MercadoPago - Ticket</span>">MercadoPago - Ticket</option>
		                    <option value="Deposito/Transferencia" data-content="<span class='label label-info'>Deposito/Transferencia</span>">Deposito/Transferencia</option>
	                    @else
		                    <option value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
		                    <option value="MercadoPago - Tarjeta" data-content="<span class='label label-primary'>MercadoPago - Tarjeta</span>">MercadoPago - Tarjeta</option>
		                    <option value="MercadoPago - Ticket" data-content="<span class='label label-success'>MercadoPago - Ticket</span>">MercadoPago - Ticket</option>
	                    @endif
	              </select>
	            </div>
	            
				<div class="input-group form-group" id="n_cobro">
	              <span class="input-group-addon"><i class="fa fa-hashtag fa-fw"></i></span>
	              <input class="form-control" type="text" name="ref_cobro" id="ref_cobro" placeholder="N° de Cobro" value="{{ $ventas_cobro->ref_cobro }}">             
	            </div>
	            <span id="faltacobro" style="color:#777;display:none;"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i> completar</span>
	                 
				<br />
				
				
				{{-- // si es admin permito modificar precio y comision --}}
				@if ((session()->get('usuario')->Level ==  'Adm') || (session()->get('usuario')->Nombre ==  'Leo'))
				<div class="col-md-5">
	            <div class="input-group form-group">
	              <span class="input-group-addon">precio</span>
	              <input class="form-control" type="text" id="precio" name="precio" value="{{ $ventas_cobro->precio }}">
				</div>
	          	</div>
	            
	            <div class="col-md-3" style="opacity:0.7">
	            <div class="input-group form-group">
	            <select id="porcentaje" class="form-control">
	            	<option value="0.13">13 %</option>
	                <option selected value="0.0538">6 %</option>
	                <option value="0.00">0 %</option>
	            </select>  
	            </div>
	            </div>
	            
				<div class="col-md-4">
	            <div class="input-group form-group">
	                <span class="input-group-addon">com</span>
	                <input class="form-control" type="text" id="comision" name="comision" value="{{ $ventas_cobro->comision }}">
	            </div>
	            </div>
				 {{-- Si no es Admin oculto los campos de precio y comision para que no se puedan modificar --}}
				@else
					<input class="form-control" type="hidden" id="precio" name="precio" value="{{ $ventas_cobro->precio }}">
					<input class="form-control" type="hidden" id="comision" name="comision" value="{{ $ventas_cobro->comision }}">
				@endif
	                        
	            <div class="input-group form-group">
	              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
					
	              <input class="form-control" type="text" name="Notas_cobro" placeholder="Notas del cobro" value="{{ $ventas_cobro->Notas }}">
	            </div>

	            <button class="btn btn-primary" type="submit">Modificar</button>
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

@endsection

@section('scripts')

<script>
	$(document).ready(function() {
		var isFormValid = false;

		$("form :input").change(function() {
			var val = $('#medio_cobro').val();
			//alert(val2); 
			if (val == "Deposito/Transferencia") {
				$("#porcentaje").html("<option value='0.00'>0%</option>");
			} else {
		        let html = '<option value="0.13">13 %</option><option selected value="0.0538">6 %</option><option value="0.00">0 %</option>';
		        $("#porcentaje").html(html);
		    } 
		});

		$("#porcentaje").change(function() {
	      m1 = document.getElementById("precio").value;
	      m2 = document.getElementById("porcentaje").value;
	      r = m1*m2;
	      document.getElementById("comision").value = r;
	    });
	});
</script>

@stop