@extends('layouts.master-layouts')

@section('container')


<div class="container">
	<h1>Listar stock</h1>

	@component('components/filters/column_word')
		@slot('columns',$columns);
		@slot('path','stock');
	@endcomponent

    <div class="table-responsive">
      <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
        <thead>
          <tr>
            <th>#</th>
            <th>Producto</th>
            @if(Helper::validateAdministrator(Auth::user()->Level))
              <th>Code Prov</th>
            @endif
            <th>Cuenta</th>
            <th>Costo USD</th>
            @if(Helper::validateAdminAnalyst(Auth::user()->Level))
              <th>Costo</th>
            @endif
            @if(Helper::validateAdministrator(Auth::user()->Level))
              <th>Pago por</th>
            @endif
          </tr>
        </thead>
  		  <tbody>
          @if(count($stocks) > 0)
            @foreach($stocks as $stock)
              <tr>

                <td>
                  {{ $stock->ID }}
                  @if($stock->Notas)
                    <button
                      data-toggle="popover"
                      data-placement="bottom"
                      data-trigger="focus"
                      title="Notas"
                      data-content="{{ $stock->Notas }}"
                      class="h6 btn btn-secondary"
                      style="color: #555555;">

                        <i class="fa fa-comment fa-fw"></i>

                      </button>
                  @endif
                </td>

                <td>
                  {{ $stock->titulo }}
                  <span class="label label-default {{$stock->consola}}">
                      {{$stock->consola}}
                  </span>
                </td>

                @if(Helper::validateAdministrator(Auth::user()->Level))
                  <td>
                    @if($stock->code)
                         <small class="label label-default">
                           {{ $stock->code }}
                         </small>
                         <span class="text-muted" style="font-size:0.8em;">
                           {{ '('.substr($stock->code_prov, 0 , 3) . ') ' }} {{ $stock->n_order }}
                         </span>
                    @endif
                  </td>
                @endif

                <td>
                  @if($stock->cuentas_id)
                    <a title="Ir a Cuenta" href="{{ url('/cuentas_detalles',[$stock->cuentas_id]) }}">
                      {{$stock->cuentas_id}}
                    </a>
                  @endif
                </td>

                <td>{{$stock->costo_usd}}</td>

                @if(Helper::validateAdminAnalyst(Auth::user()->Level))
                  <td>{{ $stock->costo }}</td>
                @endif

                @if(Helper::validateAdministrator(Auth::user()->Level))
                  <td>{{ $stock->medio_pago }}</td>
                @endif

              </tr>
            @endforeach
          @else
            <td colspan = '10' class="text-center">No se encontraron datos</td>
          @endif
        </tbody>
      </table>
      <div>
        <div class="col-md-12">
          {{ $stocks->render() }}
        </div>
      </div>
    </div>
</div><!--/.container-->



@endsection
