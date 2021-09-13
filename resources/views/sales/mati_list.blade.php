@extends('layouts.master-layouts')

@section('title', 'Listar Mati')

@section('container')
    <div class="container">
        <h1>Lista Mati</h1>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            @component('components/filters/column_word')
                @slot('columns',$columns);
                @slot('path','mati/list');
            @endcomponent
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Order_ID</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Desvincular</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($datos as $mati)
                            <tr>
                
                                <td>{{ $mati->id }}</td>
                                <td>{{ $mati->order_id }}</td>
                                <td> 
                                    {{ $mati->email }}
                                </td>
                                <td>{{ $mati->status }}</td>
                                <td>
                                    @if($mati->order_id > 0)
                                        <a href="{{ route("desvincular-mati", $mati->id) }}" class="btn btn-danger btn-xs"><i class="fa fa-sign-out"></i></a>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                
                        </tbody>
                    </table>
                <div>
            </div>

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

@endsection