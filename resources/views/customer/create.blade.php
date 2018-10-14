@extends('layouts.master-layouts')

@section('container')
  <div class="container text-center">
  	<h1>Insertar cliente</h1>
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
        <form method="post" name="form1" action='{{ url("clientes") }}'>
          {{ csrf_field() }}
          <div id="user-result-div" class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
            <input class="form-control" type="text" name="email" id="email" autocomplete="off" spellcheck="false" placeholder="Email" autofocus>
            <span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control" type="text" name="apellido" autocomplete="off" placeholder="Apellido">
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control" type="text" name="nombre" autocomplete="off" placeholder="Nombre">
          </div>

          <div id="ml-user-result-div" class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-snapchat-ghost fa-fw"></i></span>
            <input class="form-control" type="text" name="ml_user" id="ml_user" autocomplete="off" placeholder="Usuario ML">
            <span class="input-group-addon"><i id="ml-user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
            <input class="form-control" type="text" name="pais" value="Argentina">
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
            <select name="provincia" class="form-control">
        			<option value="Buenos Aires" selected="selected">Buenos Aires</option>
        			<option value="Catamarca" >Catamarca</option>
        			<option value="Chaco" >Chaco</option>
        			<option value="Chubut" >Chubut</option>
        			<option value="Cordoba" >Cordoba</option>
              <option value="Corrientes" >Corrientes</option>
        			<option value="Entre Rios" >Entre Rios</option>
        			<option value="Formosa" >Formosa</option>
        			<option value="Jujuy" >Jujuy</option>
        			<option value="La Pampa" >La Pampa</option>
        			<option value="La Rioja" >La Rioja</option>
        			<option value="Mendoza" >Mendoza</option>
        			<option value="Misiones" >Misiones</option>
        			<option value="Neuquen" >Neuquen</option>
        			<option value="Rio Negro" >Rio Negro</option>
        			<option value="San Juan" >San Juan</option>
        			<option value="San Luis" >San Luis</option>
        			<option value="Santa Cruz" >Santa Cruz</option>
        			<option value="Santa Fe" >Santa Fe</option>
        			<option value="Santiago del Estero" >Santiago del Estero</option>
        			<option value="Salta" >Salta</option>
        			<option value="Tierra del Fuego" >Tierra del Fuego</option>
        			<option value="Tucuman" >Tucuman</option>
            </select>
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-street-view fa-fw"></i></span>
            <input class="form-control" type="text" name="ciudad" placeholder="Ciudad">
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-phone-square fa-fw"></i></span>
            <input class="form-control" type="text" name="carac" placeholder="Carac">
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-phone fa-fw"></i></span>
            <input class="form-control" type="text" name="tel" placeholder="Tel">
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-mobile fa-fw"></i></span>
            <input class="form-control" type="text" name="cel" placeholder="Cel">
          </div>

          <button class="btn btn-primary btn-block btn-lg" id="submiterInsert" type="submit">Insertar</button>
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

    var x_timer;
    $("#email").keyup(function (e){
        clearTimeout(x_timer);
        var user_name = $(this).val();
        x_timer = setTimeout(function(){
            check_email_ajax('{{ url("customer_ctrl_email") }}',user_name);
        }, 1000);
    });

    var x_timer_ml_user;
    $("#ml_user").keyup(function (e){
        clearTimeout(x_timer_ml_user);
        var user_name = $(this).val();
        x_timer_ml_user = setTimeout(function(){
            check_ml_user_ajax('{{ url("customer_ctrl_ml_user") }}',user_name);
        }, 1000);
    });

  });
</script>
@stop
