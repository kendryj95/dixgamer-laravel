@extends('layouts.master-layouts')

@section('title', 'Lista CM: ' . $code)

@section('container')


<div class="container">

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
    <div class="col-md-7">
      <h1>Lista CM: {{ $code }}</h1>

      <button style="margin-bottom: 10px" class="btn btn-primary btn-sm" onclick="window.history.back()">Volver</button>

    </div>
    <div class="col-md-5 text-right">
      <span style="margin-top: 30px" class="pull-right fa-2x">Total: {{ $total }}</span>

      <div class="row">
        <div class="col-md-12">
          @if ($control)
            <span class="text-muted">Control: {{ $control->Day }}</span>
          @else
          <div class="dropdown pull-right" style="margin-bottom: 2px">
            <button
              class="btn btn-primary btn-sm dropdown-toggle"
              type="button" id="dropdownMenu1"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false">
                <i class="fa fa-fw fa-check"></i>
                Controlado
            </button>
    
            <ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu1">
              <li class="dropdown-header">¿Seguro deseas</li>
              <li class="dropdown-header">controlar code?</li>
              <li role="separator" class="divider"></li>
              <li>
                <form class="text-center" action="{{ url('stock/controlar_code') }}" method="post">
                  {{ csrf_field() }}
                  <input type="hidden" name="code" value="{{$code}}">
                  <button
                    type="submit"
                    class="btn btn-danger btn-block"
                    title="Controlar"
                    id="controlar_code"
                    type="button">
                    Si, seguro!
                  </button>
                </form>
              </li>
            </ul>
    
          </div>
          @endif

        </div>
      </div>
    </div>
  </div>

	<div class="row">
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
          
              <thead>
                <tr>
                  <th></th>
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
                          <div class="dropdown" style="display:inline;">
                            <button
                              class="btn btn-danger dropdown-toggle btn-xs"
                              type="button"
                              id="dropdownMenu4"
                              data-toggle="dropdown"
                              aria-haspopup="true"
                              aria-expanded="false">
                              <i class="fa fa-fw fa-trash-o"></i>
                            </button>
        
                            <ul class="dropdown-menu bg-info" aria-labelledby="dropdownMenu4">
                              <li class="dropdown-header">¿Eliminar?</li>
                              <li role="separator" class="divider"></li>
                              <li>
                                <form action="{{ url('delete_cm') }}" method="post">
                                  {{ csrf_field() }}
                                  <input type="hidden" name="id" value="{{$stock->ID_key}}">
                                  <input type="hidden" name="tabla" value="{{$stock->tabla}}">
                                  <button type="submit" class="btn btn-danger btn-block">Si, seguro!</button>
                                </form>
        
                              </li>
                            </ul>
                          </div>
                        </td>
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
