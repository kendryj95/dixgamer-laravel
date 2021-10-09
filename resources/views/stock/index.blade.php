@extends('layouts.master-layouts')

@section('title', 'Listar stock')

@section('container')


<div class="container">
	<h1>Listar stock</h1>
<div class="row">
    @component('components/filters/column_word')
        @slot('columns',$columns);
        @slot('consolas',$consolas);
        @slot('path','stock');
    @endcomponent
</div>
	<div class="row">
        <div class="table-responsive">
            <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Day</th>
                    <th>Producto</th>
                    @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                        <th>Code Prov</th>
                    @endif
                    <th>Cuenta</th>
                    <th>Costo USD</th>
                    @if(Helper::validateAdminAnalyst(session()->get('usuario')->Level))
                        <th>Costo</th>
                    @endif
                    @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                        <th>Pago por</th>
                    @endif
                    <th>Operador</th>
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
                                {{ $stock->Day_formatted }}
                            </td>

                            <td>
                                {{ \Helper::strTitleStock($stock->titulo) }}
                                <span class="label label-default {{$stock->consola}}">
                      {{$stock->consola}}
                  </span>
                            </td>

                            @if(Helper::validateAdministrator(session()->get('usuario')->Level))
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
                                    <a title="Ir a Cuenta" href="{{ url('/cuentas',[$stock->cuentas_id]) }}">
                                        {{$stock->cuentas_id}}
                                    </a>
                                @endif
                            </td>

                            <td>{{$stock->costo_usd}}</td>

                            @if(Helper::validateAdminAnalyst(session()->get('usuario')->Level))
                                <td>{{ $stock->costo }}</td>
                            @endif

                            @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                                <td>{{ $stock->medio_pago }}</td>
                            @endif

                            <td class="text-center">
                                <span class="badge badge-{{ $stock->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$stock->usuario}}">{{ substr($stock->usuario,0 , 1) }}</span>
                            </td>

                        </tr>
                    @endforeach
                @else
                    <td colspan = '11' class="text-center">No se encontraron datos</td>
                @endif
                </tbody>
            </table>
            <div>
                <div class="col-md-12">
                    {{ $stocks->appends(
                    [
                      'column' => app('request')->input('column'),
                      'word' => app('request')->input('word'),
                      'consola' => app('request')->input('consola'),
                    ]
                    )->render() }}
                </div>
            </div>
        </div>
    </div>


</div><!--/.container-->



@endsection
