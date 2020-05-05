@extends('layouts.master-layouts')

@section('title', "Listar ventas notas")

@section('container')
<div class="container">
  <h1>Listar ventas notas</h1>
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
            @slot('path','sales/list/notas');
        @endcomponent
    </div>


  <!-- COMPONENTE DE clientes -->

    <div class="row">
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
          
              <thead>
                <tr>
                  <th>#</th>
                  <th>Vta ID</th>
                  <th>Notas</th>
                  <th>Fecha</th>
                  <th>Operador</th>
                </tr>
              </thead>
              <tbody>
          
                  @if(count($datos) > 0)
          
                    @foreach($datos as $i => $sale)
          
                      <tr>
          
                        <td>
                          <a title="Ir a cliente." href="{{ url('/clientes', [$sale->clientes_id] ) }}">
                            {{ $sale->ID }}
                          </a>
                        </td>
          
                        <td>
                          <a title="Ir a cliente." href="{{ url('/clientes', [$sale->clientes_id] ) }}">
                            {{ $sale->id_ventas }}
                          </a>
                        </td>
                        <td>
                            <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i>
                                @if (strpos($sale->Notas, "Antes asignado a cliente") !== false)
                  
                                    @php 
                                    $cliente = substr($sale->Notas, 26);
                                    @endphp
                                    Antes asignado a cliente <a href="{{ url('clientes', $cliente) }}" class="alert-link" target="_blank">#{{ $cliente }}</a>

                                    @elseif(strpos($sale->Notas, "Antes tenía") !== false) {{-- Solo notas para cambios de productos --}}
                                    @if(strpos($sale->Notas, "#", 14) !== false) {{-- Esta validación que funcione las notas anteriores antes de colocar el link para las cuentas --}}

                                        @php
                                        $string = $sale->Notas;
                                        $pos = strripos($string, "#"); // calculando la posicion de ultima aparicion de cuenta_id
                                        $cuenta = substr($string, $pos+1);
                                        $nota = substr($string, 0, $pos);
                                        /* if ($cuenta != "") {
                                            $data_nota =  explode(" ",substr($nota,14));
                                            if (count($data_nota) == 5) {
                                            list($id_stock,$title,$cons,$slot,$otro) = $data_nota;
                                            } elseif (count($data_nota) == 4) {
                                            list($id_stock,$title,$cons,$slot) = $data_nota;
                                            }
                                        } */
                                        
                                        @endphp

                                        {{$nota}} @if ($cuenta != "") <a href="{{url('cuentas',$cuenta)}}" target="_blank" class="alert-link">#{{$cuenta}}</a> @endif

                                        @else
                                        {{ $sale->Notas }}
                                        @endif

                                    @else

                                    {{ ($sale->Notas) }}
                                    @endif
                            </div>
                        </td>
                        <td>
                          @php
                          $dia = date('d', strtotime($sale->Day));
                          $mes = date('n', strtotime($sale->Day));
                          $mes = \Helper::getMonthLetter($mes);
                          $anio = date('Y', strtotime($sale->Day));
                          $fecha = "$dia-$mes-$anio";
                          @endphp
                          
                          <a title="Ir a cliente." href="{{ url('/clientes', [$sale->clientes_id] ) }}"> 
                            {{ $fecha }}
                          </a>
                        </td>
          
                        <td class="text-center">
                          <span class="badge badge-{{ $sale->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$sale->usuario}}">{{ substr($sale->usuario,0 , 1) }}</span>
                        </td>
          
          
                      </tr>
          
                    @endforeach
          
                  @else
                    <tr>
                      <td colspan = '5' class="text-center">No se encontraron datos</td>
                    </tr>
                  @endif
          
              </tbody>
            </table>
            <div class="col-md-12">
          
              <ul class="pager">
                {{ $datos->appends(
                  [
                    'column' => app('request')->input('column'),
                    'word' => app('request')->input('word'),
                  ]
                  )->render() }}
              </ul>
          
            </div>
          
          </div>
          
    </div>



</div><!--/.container-->

@endsection
