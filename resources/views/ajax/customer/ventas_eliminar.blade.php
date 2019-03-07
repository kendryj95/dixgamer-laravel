<div class="container">
	<h1 style="color: #000">Eliminar cobro</h1>
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6">
        @if (!($ventasBaja))
            <form style="display: none" method="post" name="form1" action="{{ url('customer_ventas_eliminar') }}">
                {{csrf_field()}}

    			<div class="input-group form-group">
    				<span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
    				<textarea disabled class="form-control" rows="2" name="Notas_baja" id="Notas_baja" style="font-size: 18px;" placeholder="Nota"></textarea>
    			</div>
                
                <button disabled class="btn btn-normal" type="submit"><i class="fa fa-trash fa-fw"></i> Eliminar Venta y Cobro</button>

                <input type="hidden" name="opt" value="1">
                <input type="hidden" name="ID" value="{{ $ventas->ID }}">
                <input type="hidden" name="clientes_id" value="{{ $ventas->clientes_id }}">
            </form> <!-- SE OCULTA ESTE FORMULARIO EL DÍA 07/03/2019 POR ORDEN DE VICTOR. -->
            {{-- <br /><br /> --}}
        
            <form method="post" name="form2" action="{{ url('customer_ventas_eliminar') }}">
                {{csrf_field()}}

    			<div class="input-group form-group">
    				<span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
    				<textarea class="form-control" rows="2" name="Notas_baja" id="Notas_baja" style="font-size: 18px;" placeholder="Nota"></textarea>
    			</div>
                
                <button class="btn btn-danger" type="submit" ><i class="fa fa-frown-o fa-fw"></i> Eliminar Cobro</button>
                <input type="hidden" name="opt" value="2">
                <input type="hidden" name="ID" value="{{ $ventas->ID }}">
                <input type="hidden" name="clientes_id" value="{{ $ventas->clientes_id }}">
            </form><br /><br />
        @else
            <form method="post" name="form3" action="{{ url('customer_ventas_eliminar') }}">
                {{csrf_field()}}
                
                <button class="btn btn-success" type="submit"><i class="fa fa-smile-o fa-fw"></i> Eliminar Venta</button>
                <input type="hidden" name="opt" value="3">
                <input type="hidden" name="ID" value="{{ $ventas->ID }}">
                <input type="hidden" name="clientes_id" value="{{ $ventas->clientes_id }}">
            </form>
        @endif
        </div>
        <div class="col-sm-3">
        </div>
    </div>
		<br><br>
     <!--/row-->
</div><!--/.container-->