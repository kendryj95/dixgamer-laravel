@extends('layouts.master-layouts')

@section('title', 'Listar stock')

@section('container')


<div class="container">
	<h1>Listar stock</h1>
<div class="row">
    @component('components/filters/column_word')
        @slot('columns',$columns);
        @slot('path','stock_notas');
    @endcomponent
</div>
	<div class="row">
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
          
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Stock</th>
                  <th>Cta ID</th>
                  <th>Notas</th>
                  <th>Operador</th>
                </tr>
              </thead>
              <tbody>
          
                  @if(count($stocks_notes) > 0)
          
                    @foreach($stocks_notes as $i => $stock)
          
                      <tr>
          
                        <td>
                            {{ $stock->Day }}
                        </td>

                        <td>
                            {{ \Helper::strTitleStock($stock->titulo) }} ({{$stock->consola}})
                        </td>

                        <td>
                          <a href="{{url('cuentas',$stock->cuentas_id)}}"> {{$stock->cuentas_id}} </a>
                        </td>
                        
                        <td>
                          <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> {!! $stock->Notas !!} </div>
                        </td>
          
                        <td class="text-center">
                          <span class="badge badge-{{ $stock->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$stock->usuario}}">{{ $stock->usuario }}</span>
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
                {{ $stocks_notes->appends(
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
