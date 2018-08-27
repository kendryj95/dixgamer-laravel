<div class="container">
	<h1 style="color:#000">Modificar Venta</h1>
  <div class="row">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6">
        <form method="post" name="form1" action="{{ url('customer_ventas_modificar_store') }}">
          {{csrf_field()}}
        		            
                <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value="[{{$clientes->clientes_id}}] {{$clientes->nombre}} {{$clientes->apellido}} - {{$clientes->email}}" readonly><br /><br />
                
                <input type="text" id="clientes_id" name="clientes_id" value="{{$clientes->clientes_id}}" hidden>
                
                @php $colorventa = ''; @endphp

                @if ($clientes->medio_venta == 'MercadoLibre') @php $colorventa='warning'; @endphp
                @elseif ($clientes->medio_venta == 'Web') @php $colorventa='info'; @endphp
                @elseif ($clientes->medio_venta == 'Mail') @php $colorventa='danger'; @endphp
                @endif
                
                <div class="input-group form-group">
                <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
               <select name="medio_venta" class="selectpicker form-control">
               		<option value="{{$clientes->medio_venta}}" selected="selected" data-content="<span class='label label-{{$colorventa}}'>{{$clientes->medio_venta}}</span> - <span class='label label-success'>Actual</span>">{{$clientes->medio_venta}} - Actual</option>
               		<option value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                    <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
                    <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
                </select>                
                </div> 
                
                <div class="input-group form-group">
                  <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                  <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" name="order_item_id" placeholder="order_item_id">
                  <span class="input-group-addon"><em class="text-muted">order item id</em></span>
                </div>
                
                <em class="text-muted">{{ $clientes->Notas }}</em>
                <div class="input-group form-group">
                  <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                  <input value="{{ $clientes->Notas }}" class="form-control" type="text" name="Notas" placeholder="Notas de la venta">
                </div>
                <button class="btn btn-primary" type="submit">Modificar</button>
                <input type="reset" class="btn btn-secondary">
                
                <input type="hidden" name="ID" value="{{ $clientes->ID }}">
        </form>
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