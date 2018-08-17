@extends('layouts.master-layouts')

@section('container')
  <div class="container text-center">
  	<h1>Nuevo Usuario</h1>
    @if (count($errors) > 0)
          <div class="alert alert-danger text-center">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
    @endif
    <div class="row">
      <div class="col-sm-4">
      </div>

      <div class="col-sm-4">
        <form method="post" name="form1" action='{{ url("usuario/create") }}'>
          {{ csrf_field() }}
          <div id="user-result-div" class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control" type="text" name="nombre" id="nombre" autocomplete="off" spellcheck="false" placeholder="Nombre" onkeyup="setText(this.value)" autofocus>
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
            <input class="form-control" type="password" name="password" autocomplete="off" placeholder="Contraseña">
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user-secret fa-fw"></i></span>
            <select name="level" class="form-control">
              <option value="Adm">Administrador</option>
              <option value="Analista">Analista</option>
              <option value="Asistente">Asistente</option>
              <option value="Vendedor">Vendedor</option>
              <option value="Revendedor">Revendedor</option>
            </select>
          </div>

          <label for="color">Asignar color</label>
          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-paint-brush fa-fw"></i></span>
            <select name="color" id="color" class="form-control" onchange="setColor(this.value)">
              <option value="primary"> <label for="" class="label label-primary"></label> Primary</option>
              <option value="success">Success</option>
              <option value="info">Info</option>
              <option value="danger">Danger</option>
              <option value="default">Normal</option>
            </select>
            <span class="input-group-addon" style="background:white"><span class="label label-primary" id="asigColor"></span></span>
          </div>

          <button class="btn btn-primary btn-block btn-lg" type="submit">Insertar</button>
          <br>
      </form>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div><!--/.container-->
@endsection



@section('scripts')
<script type="text/javascript">
  function setColor(value)
  {
     $('#asigColor').attr('class', '').addClass('label label-'+value);
  }

  function setText(value)
  {

    var text = value.substring(0,1).toUpperCase();

    $('#asigColor').text(text);
  }
</script>
@stop
