@extends('layouts.master-layouts')

@section('title', 'Editar usuario')

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
          <input type="hidden" name="id_usuario" value="{{$usuarios->ID}}">
          <div id="user-result-div" class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control" type="text" name="nombre" id="nombre" value="{{$usuarios->Nombre}}" disabled>
          </div>

          <input type="hidden" name="old_pass" value="{{$usuarios->Contra}}">

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
            <input class="form-control" type="password" name="password" autocomplete="off" placeholder="Contraseña" value="{{$usuarios->Contra}}">
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

          <label for="color">Asignar color</label>
          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-paint-brush fa-fw"></i></span>
            <select name="color" id="color" class="form-control" onchange="setColor(this.value)">
              <option value="primary" @if ($usuarios->color == 'primary') selected @endif> <label for="" class="label label-primary"></label> Primary</option>
              <option value="success" @if ($usuarios->color == 'success') selected @endif>Success</option>
              <option value="info" @if ($usuarios->color == 'info') selected @endif>Info</option>
              <option value="warning" @if ($usuarios->color == 'warning') selected @endif>Warning</option>
              <option value="danger" @if ($usuarios->color == 'danger') selected @endif>Danger</option>
              <option value="normal" @if ($usuarios->color == 'normal') selected @endif>Normal</option>
              <option value="default" @if ($usuarios->color == 'default') selected @endif>Default</option>
            </select>
            <span class="input-group-addon" style="background:white"><span class="label label-{{$usuarios->color}}" id="asigColor">{{substr($usuarios->Nombre, 0, 1)}}</span></span>
          </div>

          <button class="btn btn-primary btn-block btn-lg" type="submit">Editar</button>
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
</script>
@stop
