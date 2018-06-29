<div class="container">
  @if($count($customer) > 0)
  <h1>Modificar Nombre</h1>
  <div class="row">
    <form method="post" name="form1" action="{{ url('',[$customer->ID]) }}">

      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
        <textarea class="form-control" rows="4" name="notes" id="Notas" style="font-size: 22px;"></textarea>

      </div>
      <button class="btn btn-warning btn-lg" type="submit">Guardar</button>
      <input type="hidden" name="MM_insert" value="form1">
    </form>
  </div>
  @else
    <h1>No se encontraron datos</h1>
  @endif
</div>
