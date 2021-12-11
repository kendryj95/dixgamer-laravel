@php
function changeNameColumn($column) {
    switch ($column) {
        case 'antiguedad_juego':
            return 'Antig';
        default:
            return $column;
    }
}
@endphp
@extends('layouts.master-layouts')

@section('title', 'Info Adicional')

@section('container')
    <div class="container">
        <h1>Info Adicional</h1>
        <!-- InstanceBeginEditable name="body" -->

        @if(count($data) > 0)
            @php $columns = $data[0]; @endphp
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped table-info-adicional">
                            <thead>
                            <tr>
                                @foreach($columns as $column => $value)
                                    @if($column !== "consola") <th>{{changeNameColumn($column)}}</th> @endif
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $i => $item)
                                <tr>
                                    @foreach($data[$i] as $attrib => $value)
                                        @if($attrib === "titulo")
                                            <td>{{ \Helper::strTitleStock($data[$i]->$attrib) }}
                                                <span class="label label-default {{$data[$i]->consola}}">
                                                {{$data[$i]->consola}}</td>
                                        @elseif ($attrib != "consola")
                                            <td>{{$value}}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        <div>
                        </div>
                    </div>
                </div>

    @endif
@endsection