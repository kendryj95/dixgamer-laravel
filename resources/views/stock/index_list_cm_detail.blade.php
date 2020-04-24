@extends('layouts.master-layouts')

@section('title', 'Lista CM: ' . $code)

@section('container')


<div class="container">
  <div class="row">
    <div class="col-md-7">
      <h1>Lista CM: {{ $code }}</h1>
    </div>
    <div class="col-md-5">
      <span style="margin-top: 30px" class="pull-right fa-2x">Total: {{ $total }}</span>
    </div>
  </div>

	<div class="row">
        <button style="margin-bottom: 10px" class="btn btn-primary btn-sm" onclick="window.history.back()">Volver</button>
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
          
              <thead>
                <tr>
                  <th>Stk ID</th>
                  <th>Titulo</th>
                  <th>Costo USD</th>
                  <th>Cta ID</th>
                  <th>Code</th>
                  <th>Nro Order</th>
                  <th>Fecha</th>
                  <th>Uso</th>
                  <th>Operador</th>
                </tr> 
              </thead>
              <tbody>
          
                  @if(count($datos) > 0)
          
                    @foreach($datos as $i => $stock)
          
                      <tr>
          
                        <td>
                            {{ $stock->stk_ID }}
                        </td>
          
                        <td>
                          {{ $stock->titulo }}
                        </td>
                        <td>
                          {{ $stock->costo_usd }}
                        </td>
                        <td>
                          <a href="{{ url('cuentas', $stock->cuentas_id)}}">{{ $stock->cuentas_id }}</a>
                        </td>
                        <td>
                          {{ $stock->code }}
                        </td>
                        <td>
                          {{ $stock->n_order }}
                        </td>
                        <td>
                          {{ $stock->Day }}
                        </td>
                        <td>
                          {{ $stock->uso }}
                        </td>
                        <td class="text-center">
                          <span class="badge badge-{{ $stock->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$stock->usuario}}">{{ $stock->usuario }}</span>
                        </td>
          
          
                      </tr>
          
                    @endforeach
          
                  @else
                    <tr>
                      <td colspan = '9' class="text-center">No se encontraron datos</td>
                    </tr>
                  @endif
          
              </tbody>
            </table>
            {{-- <div class="col-md-12">
          
              <ul class="pager">
                {{ $stocks_notes->appends(
                  [
                    'column' => app('request')->input('column'),
                    'word' => app('request')->input('word'),
                  ]
                  )->render() }}
              </ul>
          
            </div> --}}
          
          </div>
          
    </div>


</div><!--/.container-->



@endsection
