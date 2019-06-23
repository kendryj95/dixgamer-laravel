@extends('layouts.master-layouts')
@section('title', 'Balance')

@section('container')

@if (count($errors) > 0)
    <div class="alert alert-danger text-center">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
@endif

    <div class="container">
	<h1>Balance</h1>
    <!-- InstanceBeginEditable name="body" -->
    
    <div class="pricing">
        <ul>
            <li class="unit price-success" style="min-width:200px;">
                <div class="price-title">
                    <h3>${{ round($row_rsVentas->Ingresos) }}</h3>
                    <p>ingresos</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>${{ round($row_rsStock->TotalP) }}</h3>
                    <p>costos</p>
                </div>
            </li>
            <li class="unit price-warning" style="min-width:200px;">
                <div class="price-title">
                    <h3>${{ round($row_rsVentas->Comisiones) }}</h3>
                    <p>comisiones</p>
                </div>
            </li>
            <li class="unit price-warning" style="min-width:200px;">
                <div class="price-title">
                    <h3>${{ round($row_rsGastos->gastos) }}</h3>
                    <p>gastos</p>
                </div>
            </li>
            <li class="unit" style="background-color:#efefef;min-width:200px;" >
                <div class="price-title" style="color:#000;">
                    <h3>${{ round(($row_rsVentas->Ingresos - $row_rsStock->TotalP - $row_rsVentas->Comisiones - $row_rsGastos->gastos)) }}</h3>
                    <p>ganancia</p>
                </div>
            </li>
        </ul>
    </div>
    <div class="pricing">
        <ul>
            <li class="unit" style="min-width:200px; max-height:1px;">
                <div class="price-title">
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>${{ round(($row_rsStock->TotalP - $row_rsStockVendido->costo)) }}</h3>
                    <p>costo no consumido</p>
                </div>
            </li>
           
        </ul>
    </div>
    <h3>Balance Mensual Fcro</h3>
    <table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
      <tr>
        <th width="100">Mes</th>
        <th title="Cantidad">Qty</th>
        <th title="Precio">Ingreso</th>
        <th title="Costo">Costo</th>
        <th title="Comision">Comision</th>
        
        <th title="Gasto">Gasto</th>
        <th title="Ganancia">Ganancia</th>
      </tr>
      @php $Qty = 0; $Ingreso = 0; $Comision = 0; $Costo = 0; $Gasto = 0; $Ganancia = 0; @endphp
      @foreach($balance_mensual as $row_rsMesFcro)
      <tr>
        
        <td>{{ $row_rsMesFcro->M_S }}</td>
        <td>{{ $row_rsMesFcro->qty }}</td>
        <td>{{ $row_rsMesFcro->precio }}</td>
        
        <td>{{ $row_rsMesFcro->costo }}</td>
        <td>{{ $row_rsMesFcro->comision }}</td>
        <td>{{ $row_rsMesFcro->gasto }}</td>
        <td>{{ $row_rsMesFcro->ganancia }}</td>
        <td>
        
        </td>
      </tr>   
      @php 
      $Qty = $Qty + $row_rsMesFcro->qty;
      $Ingreso = $Ingreso + $row_rsMesFcro->precio;
      $Comision = $Comision + $row_rsMesFcro->comision;
      $Costo = $Costo + $row_rsMesFcro->costo;
      $Gasto = $Gasto + $row_rsMesFcro->gasto;
      $Ganancia = $Ganancia + $row_rsMesFcro->ganancia;
      @endphp
      @endforeach
      <tr>
        <th></th>
        <th>{{ $Qty }}</th>
        <th>{{ $Ingreso }}</th>
        <th>{{ $Comision }}</th>
        <th>{{ $Costo }}</th>
        <th>{{ $Gasto }}</th>
        <th>{{ $Ganancia }}</th>
      </tr> 
    </table>
    <h3>Ciclo de Ventas</h3>
    <div class="pricing">
        <ul>
            <li class="unit price-success" style="min-width:200px;">
                <div class="price-title">
                    <h3>{{ round($row_rsCicloVtaGRAL->diasfromcompra) }} días</h3>
                    <p>Ciclo de Ventta</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>{{ round($row_rsCicloVta->diasfromcompra) }} días</h3>
                    <p>Solo ciclos >= 1 día</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>{{ round($row_rsCicloVtaPS4->diasfromcompra) }} días</h3>
                    <p>Solo PS4</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>{{ round($row_rsCicloVtaPS3->diasfromcompra) }} días</h3>
                    <p>Solo PS3</p>
                </div>
            </li>
            <li class="unit price-primary" style="min-width:200px;">
                <div class="price-title">
                    <h3>{{ round($row_rsCicloVtaPS->diasfromcompra) }} días</h3>
                    <p>Solo PSN</p>
                </div>
            </li>
        </ul>
    </div>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->

@endsection