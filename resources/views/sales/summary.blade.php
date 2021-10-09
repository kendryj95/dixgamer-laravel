@extends('layouts.master-layouts')

@section('title', 'Resumen ventas')

@section('container')
    <div class="container">
        <h3>Resumen de Ventas por mes desde 2021</h3>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Q</th>
                            <th>Ticket</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->D }} </td>
                                <td>{{ $sale->q }}</td>
                                <td>{{ $sale->ticket }}</td>
                                <td>{{ $sale->total }}</td>
                            </tr>
                        @endforeach
                
                        </tbody>
                    </table>
                <div>
            </div>
        </div>
    </div>

        <h3>Resumen de Ventas por día - últimos 2 meses</h3>
        <!-- InstanceBeginEditable name="body" -->

        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Q</th>
                            <th>Ticket</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($sales2 as $sale)
                            <tr>
                                <td>{{ $sale->D }} </td>
                                <td>{{ $sale->q }}</td>
                                <td>{{ $sale->ticket }}</td>
                                <td>{{ $sale->total }}</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                <div>
            </div>
        </div>
    </div>

@endsection