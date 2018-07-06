@extends('layouts.master-layouts')

@section('container')
<div class="container">
  <h1>Listar cuentas</h1>

  <!-- COMPONENTE DE CUENTAS -->
  @component('components.account.index')
    @slot('accounts', $accounts)
  @endcomponent


</div><!--/.container-->

@endsection
