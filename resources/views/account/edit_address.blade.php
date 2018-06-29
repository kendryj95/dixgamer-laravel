<div class="container">
	<h1 style="color:#000">Modificar cuenta - #{{$account->ID}}</h1>

  <div class="row">

    <div class="col-sm-4">
    </div>

    <div class="col-sm-4">
      <form method="post" action="{{url('actualizar_direccion_cuenta',[$account->ID])}}">
        {{ csrf_field() }}

        <em class="text-muted">{{$account->mail}}</em>
        <em class="text-muted">{{$account->country}}</em>

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
          <input class="form-control" value="{{$account->country}}" type="text" name="country">
        </div>

        <em class="text-muted">{{$account->state}}</em>
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
          <input class="form-control" value="{{$account->state}}" type="text" name="state">
        </div>

        <em class="text-muted">{{$account->city}}</em>
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-street-view fa-fw"></i></span>
          <input class="form-control" value="{{$account->city}}" type="text" name="city">
        </div>

        <em class="text-muted">{{$account->pc}}</em>
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
          <input class="form-control" value="{{$account->pc}}" type="text" name="pc">
        </div>

        <em class="text-muted">{{$account->address}}</em>
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-location-arrow fa-fw"></i></span>
          <input class="form-control" type="text" value="{{$account->address}}" name="address">
        </div>

        <button class="btn btn-primary btn-block btn-lg" type="submit">Modificar</button>
      </form>
    </div>

    <div class="col-sm-4">
    </div>

  </div>
</div>
