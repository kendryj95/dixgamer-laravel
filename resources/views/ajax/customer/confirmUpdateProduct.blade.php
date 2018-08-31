<div class="container">

	@if (count($errors) > 0)
	      <div class="alert alert-danger text-center">
	        <ul>
	          @foreach ($errors as $error)
	            <li>{{ $error }}</li>
	          @endforeach
	        </ul>
	      </div>
	@else

			<div class="row text-center" style="background-color:#cfcfcf;padding:5px; border: 1px dashed #efefef">
				<img class="img-rounded" width="60" src="{{asset('img/productos')}}/{{$consola}}/{{$titulo}}.jpg" alt="{{ $titulo }} - {{ $consola }}" /><h4 style="display: inline; color: #000"> Asignar {{ $titulo }} ({{ $consola }})</h4>

				@if (($consola === "ps4") || ($titulo === "plus-12-meses-slot"))

					<em style="font-size:0.8m; color: #000"> {{ $slot }} </em>

				@endif

				<a href="{{ url('customer_ventas_modificar_producto_store',[$consola,$titulo,$slot,$id_ventas]) }}" class="btn btn-success">Confirmar</a>
				
			</div>

	@endif
</div>