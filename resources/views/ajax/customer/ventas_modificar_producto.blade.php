<div class="container">
	<h1 style="color: #000">Modificar Venta - Producto</h1>
  <div class="row">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6">
        <form method="post" name="form1" action="{{ url('customer_ventas_modificar_producto_store') }}">
     
                {{ csrf_field() }}
                <input type="text" id="clientes_id" name="clientes_id" value="{{ $clientes->clientes_id }}" hidden>
                
                
                <div class="input-group form-group">
                  <span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
                  <input value="{{ $clientes->stock_id }}" class="form-control" type="text" name="stock_id" placeholder="stock_id">
                  <span class="input-group-addon"><em class="text-muted">stock id (Antes {{ $clientes->stock_id }})</em></span>
                </div>
                
                @php $colorcons = ''; @endphp

                @if ($clientes->cons == 'ps3') @php $colorcons='normal'; @endphp
                @elseif ($clientes->cons == 'ps4') @php $colorcons='primary'; @endphp
                @elseif ($clientes->cons == 'ps') @php $colorcons='danger'; @endphp
                @endif

                <div class="input-group form-group">
                  <span class="input-group-addon"><i class="fa fa-cube fa-fw"></i></span>
                  <select name="cons" class="selectpicker form-control">
                  		<option value="{{ $clientes->cons }}" selected="selected" data-content="<span class='label label-{{ $colorcons }}'>{{ $clientes->cons }}</span> - <span class='label label-success'>Actual</span>">{{ $clientes->cons }} - Actual</option>
                        <option value="ps4" data-content="<span class='label label-primary'>ps4</span>">ps4</option>
                        <option value="ps3" data-content="<span class='label label-warning' style='background-color:#000;'>ps3</span>">ps3</option>
                        <option value="ps" data-content="<span class='label label-danger'>ps</span>">ps</option>
                        </select>
                </div>

                @php $colorslot = ''; @endphp

                @if ($clientes->slot == 'Primario') @php $colorslot='primary'; @endphp
                @elseif ($clientes->slot == 'Secundario') @php $colorslot='normal  '; @endphp
                @elseif ($clientes->slot == 'No') @php $colorslot='danger'; @endphp
                @endif
                
                <div class="input-group form-group">
                  <span class="input-group-addon"><i class="fa fa-certificate fa-fw"></i></span>
                  <select name="slot" class="selectpicker form-control">
                 		<option value="{{ $clientes->slot }}" selected="selected" data-content="<span class='label label-{{ $colorslot }}'>{{ $clientes->slot }}</span> - <span class='label label-success'>Actual</span>">{{ $clientes->slot }} - Actual</option>
                        <option value="Primario" data-content="<span class='label label-primary'>Primario</span>">Primario</option>
                        <option value="Secundario" data-content="<span class='label label-normal'>Secundario</span>">Secundario</option>
                        <option value="No" data-content="<span class='label label-danger'>No</span>">No</option>
                        </select>
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