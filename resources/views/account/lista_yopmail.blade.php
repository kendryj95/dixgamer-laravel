@extends('layouts.master-layouts')

@section('title', "Yopmail")

@section('container')
<div class="container">
  <h1>Yopmail</h1>
  @if (count($errors) > 0)
				<div class="alert alert-danger text-center">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
	@endif

  <!-- COMPONENTE DE CUENTAS -->

    <div class="row">
        @component('components.account.lista_yopmail')
            @slot('datos', $datos)
        @endcomponent
    </div>



</div><!--/.container-->

@endsection
