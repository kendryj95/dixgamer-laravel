<div class="container">
	<h1 style="color:#000">Modificar Venta</h1>
  <div class="row">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6">


        @if($opt == 1)

          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">
            {{csrf_field()}}

            <input type="hidden" name="opt" value="1">

            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-slack fa-fw"></i></span> 
               <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value="{{$clientes->clientes_id}}" name="clientes_id">
               <span class="input-group-addon"><em>clientes_id</em> antes: {{$clientes->clientes_id}}</span> 
             </div>

            <input type="hidden" name="ID" value="{{ $clientes->ID }}">

            <button class="btn btn-primary" type="submit">Modificar</button>
            <input type="reset" class="btn btn-secondary">

          </form>

        @elseif ($opt == 2)

          @php $colorventa = ''; @endphp

          @if ($clientes->medio_venta == 'MercadoLibre') @php $colorventa='warning'; @endphp
          @elseif ($clientes->medio_venta == 'Web') @php $colorventa='info'; @endphp
          @elseif ($clientes->medio_venta == 'Mail') @php $colorventa='danger'; @endphp
          @endif
          
          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">

           {{csrf_field()}}

           <input type="hidden" name="opt" value="2">

            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
                        <select name="medio_venta" class="selectpicker form-control">
               <option value="{{$clientes->medio_venta}}" selected="selected" data-content="<span class='label label-{{$colorventa}}'>{{$clientes->medio_venta}}</span> - <span class='label label-success'>Actual</span>">{{$clientes->medio_venta}} - Actual</option>
               <option value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
                <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
            </select>                
            </div>

            <input type="hidden" name="ID" value="{{ $clientes->ID }}">

            <button class="btn btn-primary" type="submit">Modificar</button>
           <input type="reset" class="btn btn-secondary">
          </form>

        @elseif ($opt == 3)

          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">

            {{csrf_field()}}

            <input type="hidden" name="opt" value="3">

            @if ($clientes->medio_venta == 'MercadoLibre')

              <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_ml }}" class="form-control" type="text" name="order_id_ml" placeholder="order_id_ml">
                <span class="input-group-addon"><em class="text-muted">order id ml</em></span>
              </div>

              <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" name="order_item_id" placeholder="order_item_id">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" name="order_id_web" placeholder="order_id_web">
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">

              <button class="btn btn-primary" type="submit">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @elseif ($clientes->medio_venta == 'Web')

              <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" name="order_item_id" placeholder="order_item_id">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" name="order_id_web" placeholder="order_id_web">
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">

              <button class="btn btn-primary" type="submit">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @elseif ($clientes->medio_venta == 'Mail')

              <div class="text-center">
                <h3 style="color: #000">Ningún campo que actualizar.</h3>
              </div>

            @endif

          </form>

        @elseif ($opt == 4)

          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">
            
            {{csrf_field()}}

            <input type="hidden" name="opt" value="4">

            <em class="text-muted">{{ $clientes->Notas }}</em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input value="{{ $clientes->Notas }}" class="form-control" type="text" name="Notas" placeholder="Notas de la venta">
            </div>

            <input type="hidden" name="ID" value="{{ $clientes->ID }}">

            <button class="btn btn-primary" type="submit">Modificar</button>
            <input type="reset" class="btn btn-secondary">


          </form>

        @endif
        
      </div>
      <div class="col-sm-3">
      </div>
  </div>
  <br /><br />
  <!--/row-->
</div><!--/.container-->

<script>
  $(document).ready(function() {
    // To style only <select>s with the selectpicker class
    $('.selectpicker').selectpicker();
  });
</script>