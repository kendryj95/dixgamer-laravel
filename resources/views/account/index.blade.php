@extends('layouts.master-layouts')

@section('title', "Listar cuentas")

@section('container')
<div class="container">
  <h1>Listar cuentas</h1>
  @if (count($errors) > 0)
				<div class="alert alert-danger text-center">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
	@endif
  <!-- Filter -->
    <div class="row">
        @component('components/filters/column_word')
            @slot('columns',$columns);
            @slot('path','cuentas');
        @endcomponent
    </div>


  <!-- COMPONENTE DE CUENTAS -->

    <div class="row">
        @component('components.account.index')
            @slot('accounts', $accounts)
        @endcomponent
    </div>



</div><!--/.container-->

@endsection
