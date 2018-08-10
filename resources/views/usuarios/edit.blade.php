@extends('layouts.master-layouts')

@section('container')
  <div class="container text-center">
  	<h1>Editar Usuario</h1>
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
        <form method="post" name="form1" action='{{ url("usuario/edit") }}'>
          {{ csrf_field() }}
          <input type="hidden" name="id_usuario" value="{{$usuarios->id}}">
          <div id="user-result-div" class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control" type="text" name="nombre" id="nombre" value="{{$usuarios->Nombre}}" disabled>
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
            <input class="form-control" type="password" name="password" autocomplete="off" placeholder="Contraseña">
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user-secret fa-fw"></i></span>
            <select name="level" class="form-control">
              <option value="Adm" @if ($usuarios->Level == 'Adm') selected @endif>Administrador</option>
              <option value="Analista" @if ($usuarios->Level == 'Analista') selected @endif>Analista</option>
              <option value="Asistente" @if ($usuarios->Level == 'Asistente') selected @endif>Asistente</option>
              <option value="Vendedor" @if ($usuarios->Level == 'Vendedor') selected @endif>Vendedor</option>
              <option value="Revendedor" @if ($usuarios->Level == 'Revendedor') selected @endif>Revendedor</option>
            </select>
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
 
</script>
@stop
