<div class="container">
  @if($count($customer) > 0)
  <h1>Modificar Nombre</h1>
  <div class="row">
    <form method="post" name="form1" action="{{ url('',[$customer->ID]) }}">

      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-facebook fa-fw"></i></span>
        <input value="{{$customer->face}}" class="form-control" type="text" name="face" placeholder="facebook" autofocus>
      </div>

      <button class="btn btn-primary" type="submit">Actualizar</button>
    </form>
  </div>
  @else
    <h1>No se encontraron datos</h1>
  @endif
</div>
