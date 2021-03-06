@extends('layouts.master-layouts')

@section('title', "Listar cuentas notas")

@section('container')
<div class="container">
  <h1>Listar cuentas notas</h1>
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
            @slot('path','cuentas_notas');
        @endcomponent
    </div>


  <!-- COMPONENTE DE CUENTAS -->

    <div class="row">
        @component('components.account.index_cuentas_notas')
            @slot('accounts_notes', $accounts_notes)
        @endcomponent
    </div>



</div><!--/.container-->

@endsection
