<div class="container">
	<h1 style="color:#000">Cargar Juego</h1>

	<div class="row">
		@if(count($accountStocks) > 0)
			@foreach($accountStocks as $stock)
				<div class="col-xs-6 col-sm-2">
					<div class="thumbnail">
						@if ($balance >= $stock->costo_usd)
							<form  action="{{ url('guardar_stock_masivo',[$account->ID]) }}" method="post">
								{{ csrf_field() }}
								<input type="hidden" name="consola" value="{{$stock->consola}}">
								<input type="hidden" name="titulo" value="{{$stock->titulo}}">
								<input type="hidden" name="costo_usd" value="{{$stock->costo_usd}}">
								<div>
						@else
							<form action="#" method="post">
								<div style="filter: brightness(50%); opacity:0.5;">
						@endif
									<img
										src="{{asset('img/productos')}}/{{$stock->consola}}/{{$stock->titulo}}.jpg "
										alt="{{$stock->consola}} - {{$stock->titulo}}.jpg" class="img img-responsive full-width" />
								</div>

							<div class="caption text-center">
								<span class="badge badge-<?php if ($balance >= $stock->costo_usd): echo 'success'; else: echo 'danger'; endif;?>">
									USD {{$stock->costo_usd}}
								</span>

								@if($balance < $stock->costo_usd)
									<p class="badge badge-danger">falta saldo</p>
								@else
									<button type="submit" class="btn badge badge-primary">Cargar</button>
								@endif
							</div>
						</form>
					</div>
				</div>
			@endforeach
		@else
			<h1 style="color:#000">Datos no encontrados</h1>
		@endif
		</div>
	</div>
