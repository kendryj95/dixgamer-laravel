@extends('layouts.master-layouts')

@section('container')
<div class="container">
	<h1>Crear cuentas</h1>
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

		<div class="col-sm-3">
		</div>

		<div class="col-sm-6">
			<form method="post" action="{{ url('cuentas') }}">
				{{ csrf_field() }}
				<div id="user-result-div" class="input-group form-group">
					<span class="input-group-addon"><i class="fa fa-user-secret fa-fw"></i></span>
					<input class="form-control"
						type="text"
						name="mail"
						id="mail"
						autocomplete="off"
						spellcheck="false"
						placeholder="Email real (secreto)"
						autofocus>
					<span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
				</div>

				<div id="ml-user-result-div" class="input-group form-group">
					<span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
					<input class="form-control"
					type="text"
					name="mail_fake"
					id="mail_fake"
					autocomplete="off"
					placeholder="Email falso: 'alt. ...'">
					<span class="input-group-addon"><i id="ml-user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
				</div>

				<div class="input-group form-group">
					<span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
					<input class="form-control" type="text" id="pass" name="pass" value="{{ Helper::getRandomPass() }}">
				</div>

				<div class="col-sm-6">
					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-user fa-fw"></i> Nombre</span>
						<input class="form-control" type="text" id="name" name="name" autocomplete="off" value="{{ Helper::getRandomName() }}">
					</div>
				</div>

				<div class="col-sm-6">
					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-user fa-fw"></i> Apellido</span>
						<input class="form-control" type="text" id="surname" name="surname" autocomplete="off" value="{{ Helper::getRandomLastName() }}">
					</div>
				</div>

				<div class="col-sm-2" style="opacity:0.5">
					<div class="input-group form-group">
						<input class="form-control" type="text" name="country" value="EEUU" readonly>
					</div>
				</div>

				<div class="col-sm-3" style="opacity:0.5">
					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
						<input class="form-control" type="text" name="state" value="Florida" readonly>
					</div>
				</div>

				<div class="col-sm-4" style="opacity:0.5">
					<div class="input-group form-group">
						<span class="input-group-addon"><i class="fa fa-street-view fa-fw"></i></span>
						<input class="form-control" type="text" name="city" value="Miami Beach" readonly>
					</div>
				</div>

				<div class="col-sm-3" style="opacity:0.5">
					<div class="input-group form-group">
						<span class="input-group-addon">cp</span>
						<input class="form-control" type="text" name="pc" value="33139" readonly>
					</div>
				</div>

				<div class="col-sm-6" style="opacity:0.8;">
					<div class="input-group form-group">
						<span class="input-group-addon" style="background-color:#FFCE55;border-color:#F6BB43;"><i class="fa fa-location-arrow fa-fw"></i></span>
						<input style="border-color:#F6BB43;" class="form-control" type="text" name="address" id="address" value="{{ Helper::getRandomNumber() }} {{ Helper::getRandomStreet() }}">
					</div>
				</div>

				<div class="col-sm-6" style="opacity:0.8;">
					<div class="input-group form-group">
						<span class="input-group-addon" style="background-color:#FFCE55;border-color:#F6BB43;">Nac</span>
						<input style="border-color:#F6BB43;" class="form-control" type="text" name="nacimiento" id="nacimiento" value="{{ Helper::getRandomPlace() }}">
					</div>
				</div>


				<div class="col-sm-4" style="opacity:0.6">
					<div class="input-group form-group">
						<span class="input-group-addon" style="background-color:#FB6E52; border-color:#E9573E; color:#efefef">día</span>
						<input style=" background-color:#F6F6F6; border-color:#E9573E;" class="form-control" type="text" name="days" id="days" value="{{ Helper::getRandomDay() }}">
					</div>
				</div>

				<div class="col-sm-4" style="opacity:0.6">
					<div class="input-group form-group">
						<span class="input-group-addon" style="background-color:#FB6E52; border-color:#E9573E; color:#efefef">mes</span>
						<input style="background-color:#F6F6F6; border-color:#E9573E;" class="form-control" type="text" name="months" id="months" value="{{ Helper::getRandomMonth() }}">
					</div>
				</div>

				<div class="col-sm-4" style="opacity:0.6">
					<div class="input-group form-group">
						<span class="input-group-addon" style="background-color:#FB6E52; border-color:#E9573E; color:#efefef">año</span>
						<input style="background-color:#F6F6F6; border-color:#E9573E;" class="form-control" type="text" name="years" id="years" value="{{ Helper::getRandomYear() }}">
					</div>
				</div>

				<button class="btn btn-primary btn-block btn-lg" type="submit">Insertar</button>
				<!-- <label class="btn-cuenta"><input type="radio"  value="form1" checked="checked" />New <i class="fa fa-plus" aria-hidden="true"></i></label> -->
				<!-- oculto las opciones de cargar cuenta con ultimo stock o dos ultimos stock (repetir stocks / carga masiva)
				<label class="btn-info btn-cuenta"><input type="radio" name="MM_insert" value="form2" />Last Stk <i class="fa fa-database" aria-hidden="true"></i></label> <label class="btn-info btn-cuenta"><input type="radio" name="MM_insert" value="form3" />2x Last Stk <i class="fa fa-database" aria-hidden="true"></i></label> -->
			</form>
		</div>
		<div class="col-sm-3">
		</div>

	</div>
		 <!--/row-->
</div><!--/.container-->

@stop


@section('scripts')
<script type="text/javascript">
	var x_timer_fake;
	$("#mail_fake").keyup(function (e){
			clearTimeout(x_timer_fake);
			var data = $(this).val();
			x_timer_fake = setTimeout(function(){
					check_mail_fake_account_ajax(data);
			}, 1000);
	});

	var x_timer_mail;
	$("#mail").keyup(function (e){
	    clearTimeout(x_timer_mail);
	    var data = $(this).val();
	    x_timer_mail = setTimeout(function(){
	        check_account_mail_ajax(data);
	    }, 1000);
	});
</script>
@stop
