@extends('layouts.master-layouts')

@section('title', "Lista Con TC sin Juego")

@section('container')
<div class="container">
  <h1>Lista Con TC sin Juego</h1>

  <table class="table table-striped">
      <tr>
          <th>ID</th>
          <th>Cuenta ID</th>
          <th>Fecha</th>
      </tr>
      @if(count($cuentas) == 0)
      <tr>
          <td colspan="3" class="text-center">
              <span>No se encontraron datos para mostrar!</span>
          </td>
      </tr>
      @endif
      @foreach($cuentas as $cta)
      <tr>
        <td>{{ $cta->ID }}</td>
        <td><a href="{{ url('cuentas', $cta->cuentas_id) }}" target="_blank">{{ $cta->cuentas_id }}</a></td>
        <td>{{ $cta->Day }}</td>
      </tr>
      @endforeach
  </table>

</div>

@endsection