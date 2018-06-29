@extends('layouts.master-layouts')

@section('container')
<div class="container">
  <h1>Gastos</h1>

  @component('components/filters/column_word')
    @slot('columns',$columns);
    @slot('path','gastos');
  @endcomponent

  <a class="btn btn-success btn-sm" href="{{ url('gastos') }}" title="Ver Todos" style="margin:5px 0 0 0;">Todos</a>

  @foreach($concepts as $concept)

    <a
      class="btn btn-default btn-sm"
      href="/gastos?concepto={{$concept->concepto}}"
      title="Filtrar {{$concept->concepto}}"
      style="margin:5px 0 0 0;">

      {{$concept->concepto}}

    </a>

  @endforeach


  <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

      <thead>
        <tr>
          <th>#</th>
          <th>Fecha</th>
          <th>Concepto</th>
          <th>Importe</th>
          <th>Nro Transac</th>
          <th>Medio Pago</th>
          <th>Notas</th>
        </tr>
      </thead>
      <tbody>

        @if(count($expenses) > 0)

          @foreach($expenses as $ex)
            <tr>

              <td>{{ $ex->ID }}</td>
              <td>{{ $ex->Day }}</td>
              <td>{{ $ex->concepto }}</td>
              <td>${{$ex->importe}}</td>
              <td>{{$ex->nro_transac}}</td>
              <?php

                switch ($ex->medio_pago) {
                  case 'MercadoPago':
                    $color = 'primary';
                    break;
                  case 'Transferencia Bancaria':
                    $color = 'default';
                    break;
                  case 'Efectivo':
                    $color = 'success';
                    break;
                  case 'Tarjeta':
                    $color = 'info';
                    break;

                  default:
                    $color = 'primary';
                    break;
                }

              ?>


              <td>
                <span class="label label-<?php echo $color;?>">
                  {{ $ex->medio_pago }}
                </span>
              </td>
              <td>{{ $ex->Notas }}</td>
            </tr>

            @endforeach

          @else
            <tr>
              <td colspan = '10' class="text-center">No se encontraron datos</td>
            </tr>
          @endif

      </tbody>
    </table>
    <div class="col-md-12">

      <ul class="pager">
        {{$expenses->appends(
          [
            'email' => app('request')->input('email'),
            'column' => app('request')->input('column'),
            'word' => app('request')->input('word'),
          ]
          )->render()}}
      </ul>

    </div>

  </div>
</div><!--/.container-->

@endsection
