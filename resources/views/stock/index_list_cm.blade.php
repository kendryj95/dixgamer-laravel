@extends('layouts.master-layouts')

@section('title', 'Lista CM')

@section('container')


<div class="container">
	<h1>Lista CM</h1>

	<div class="row">
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
          
              <thead>
                <tr>
                  <th>#</th>
                  <th>Code</th>
                  <th>Total USD</th>
                  <th>Nro. Order</th>
                  <th>Controlado</th>
                </tr> 
              </thead>
              <tbody>
          
                  @if(count($datos) > 0)
          
                    @foreach($datos as $i => $stock)
          
                      <tr>
          
                        <td>
                            <a href="{{ url('stock_cm', substr($stock->code,0,19))}}">{{ ($i + 1) }}</a>
                        </td>
          
                        <td>
                          <a href="{{ url('stock_cm', substr($stock->code,0,19))}}">{{ $stock->code }}</a>
                        </td>
                        <td>
                          <a href="{{ url('stock_cm', substr($stock->code,0,19))}}">{{ $stock->total_usd }}</a>
                        </td>
                        <td>
                          <a href="{{ url('stock_cm', substr($stock->code,0,19))}}">{{ $stock->n_order }}</a>
                        </td>
                        <td>
                          @if ($stock->controlado == 'No')
                              <span class="badge badge-danger"><i class="fa fa-times"></i></span>
                          @else
                              <span class="badge badge-success"><i class="fa fa-check"></i></span>
                          @endif
                        </td>
          
          
                      </tr>
          
                    @endforeach
          
                  @else
                    <tr>
                      <td colspan = '4' class="text-center">No se encontraron datos</td>
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
