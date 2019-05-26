@extends('layouts.master-layouts')

@section('title', 'Asignar Stock')

@section('container')
  <div class="container text-center">
  	<h1>Asignar Stock</h1>
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
        <form method="post" name="form1" action="{{ url('asignar_stock') }}">
          {{ csrf_field() }}
          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control" type="number" name="cantidad" id="cantidad" autocomplete="off" spellcheck="false" placeholder="Cantidad" autofocus>
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-gamepad fa-fw"></i></span>
            <select name="titulo" class="selectpicker form-control" onchange="formatTitleAndGetData()" data-live-search="true" data-size="5" id="titulo">
              <option value="">Seleccione Titulo</option>
              @foreach($titles as $t)
               <option value="{{ explode(" (",$t->nombre_web)[0] }}">{{ str_replace('-', ' ', $t->nombre_web) }}</option>
              @endforeach
            </select>
          </div>

          <input type="hidden" name="consola" id="consola">

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-users fa-fw"></i></span>
              <select name="usuarios[]" id="users-select" value="" class="form-control select2-multiple" multiple>
                  @foreach($users as $user)
                  <option value="{{ $user->Nombre }}">{{ $user->Nombre }}</option>
                  @endforeach
              </select>
          </div>

          <button class="btn btn-primary btn-block btn-lg" type="submit">Guardar</button>
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

  $(document).ready(function() {
    setTimeout(function(){
      initSelect2();
    },200);
  });

  function formatTitleAndGetData()
  {
    var select = document.getElementById('titulo');

    var consola = select.options[select.selectedIndex].text;

    var index = consola.indexOf("(");

    consola = consola.substring(index+1);

    consola = (consola.replace(")","")).replace(" ","-");

    document.getElementById('consola').value = consola.trim();
  }

  function initSelect2(){
    $( "#users-select" ).select2({
        theme: "bootstrap"
    });
  }
</script>
@stop
