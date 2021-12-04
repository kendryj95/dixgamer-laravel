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
                        <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                            <thead>
                            <tr>
                                @foreach($columns as $column => $value)
                                    <th>{{$column}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $i => $item)
                                <tr>
                                    @foreach($data[$i] as $attrib => $value)
                                        <td>{{$value}}</td>
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