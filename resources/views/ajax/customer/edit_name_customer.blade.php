<div class="container">
  @if($count($customer) > 0)
    <h1>Modificar Nombre</h1>
    <div class="row">
      <form method="POST" action="{{ url('/cuentas',[$customer->ID]) }}">

        <input type="hidden" name="_method" value="PUT">
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>

          <input
            value="{{ $customer->apellido }}"
            class="form-control"
            type="text"
            name="apellido"
            placeholder="Apellido">

          <span class="input-group-addon">
            <em class="text-muted">{{ $customer->apellido }}</em>
          </span>
        </div>

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
          <input
            value="{{ $customer->nombre }}"
            class="form-control"
            type="text"
            name="nombre"
            placeholder="Nombre">
          <span class="input-group-addon"><em class="text-muted">{{ $customer->nombre }}</em></span>
        </div>

        <button class="btn btn-primary btn-bloc btn-lg" type="submit">Actualizar</button>
      </form>
    </div>
  @else
    <h1>No se encontraron datos</h1>
  @endif
</div>
