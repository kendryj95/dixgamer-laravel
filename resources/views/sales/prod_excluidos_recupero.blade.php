@extends('layouts.master-layouts')

@section('title', 'Listar Productos Excluidos Recupero')

@section('container')
    <div class="container">
        <h1>Listado de Productos Excluidos Recupero</h1>
        <!-- InstanceBeginEditable name="body" -->

        
        <div class="row">
            <div class="col-md-5">
                <h4>Primario</h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prod_primarios as $value)
                            <tr>
                                <td>{{ str_replace(['"','-'],['',' '],$value) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-1"></div>
            <div class="col-md-5">
                <h4>Secundario</h4>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prod_secundarios as $value)
                            <tr>
                                <td>{{ str_replace(['"','-'],['',' '],$value) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection