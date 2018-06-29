@extends('layouts.master-layouts')

@section('container')
<div class="container">

  <div class="row">
    <div class="col-sm-6 col-md-offset-3">

      <h1 class="text-center">Guardar gastos</h1>

      <form method="post" name="form1" action="{{ url('gastos') }}">
        {{ csrf_field() }}
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-compass fa-fw"></i></span>
            <select name="concepto" class="form-control">
            <option value="Autonomos">Autonomos</option>
            <option value="Publicidad Google" >Publicidad Google</option>
            <option value="Publicidad Facebook" selected="selected">Publicidad Facebook</option>
            <option value="Publicidad ML" >Publicidad ML</option>
            <option value="Honorarios Enri" >Honorarios Enri</option>
            <option value="Honorarios Euge" >Honorarios Euge</option>
            <option value="Honorarios Leo" >Honorarios Leo</option>
            <option value="Honorarios Betina" >Honorarios Betina</option>
            <option value="Honorarios Francisco" >Honorarios Francisco</option>
            <option value="Honorarios Diseño" >Honorarios Diseño</option>
            <option value="Honorarios Contador" >Honorarios Contador</option>
            <option value="Facturante" >Facturante</option>
            <option value="Real Trends" >Real Trends</option>
            <option value="MailChimp" >MailChimp</option>
            <option value="Kinsta" >Kinsta</option>
            <option value="Otros Gastos" >Otros Gastos</option>
            <option value="IIBB">IIBB</option>
            <option value="Otros Impuestos" >Otros Impuestos</option>
          </select>
        </div>

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-dollar fa-fw"></i></span>
          <input class="form-control" type="text" name="importe" value="" autocomplete="off" placeholder="Importe">
        </div>

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-barcode fa-fw"></i></span>
          <input class="form-control" type="text" name="nro_transac" value="" autocomplete="off" placeholder="Nro Transac">
        </div>

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-bank fa-fw"></i></span>
          <select name="medio_pago" class="selectpicker form-control">

            <option selected value="Transferencia Bancaria" data-content="<span class='label label-default'>Transferencia Bancaria</span>">Transferencia Bancaria</option>
            <option  value="MercadoPago" data-content="<span class='label label-primary'>MercadoPago</span>">MercadoPago</option>
            <option value="Tarjeta" data-content="<span class='label label-info'>Tarjeta</span>">Tarjeta</option>
            <option value="Efectivo" data-content="<span class='label label-success'>Efectivo</span>">Efectivo</option>
            <option value="Saldo de Google" data-content="<span class='label label-normal'>Saldo de Google</span>">Saldo de Google</option>

          </select>
        </div>

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
          <input class="form-control" type="text" name="Notas" autocomplete="off" placeholder="Notas">
        </div>

        <button class="btn btn-primary btn-block " type="submit">Guardar</button>
      </form>
    </div>


  </div>
</div><!--/.container-->

@endsection
