<div class="container">
  <h1 style="color:#000">Agregar Nota - Cuenta #{{$account}}</h1>
  <div class="row">

    <form method="post" action="{{ url('guardar_nota_cuenta',[$account]) }}">
      {{ csrf_field() }}
      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
        <textarea class="form-control" rows="4" name="notes" id="Notas" style="font-size: 22px;"></textarea>

      </div>
      <button class="btn btn-warning btn-block" type="submit">Guardar</button>
    </form>

  </div>
</div>
