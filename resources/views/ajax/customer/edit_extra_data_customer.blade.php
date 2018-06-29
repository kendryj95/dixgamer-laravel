<div class="container">
  @if($count($customer) > 0)
  <h1>Modificar Datos</h1>
  <div class="row">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">

      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-globe fa-fw"></i></span>
        <input value="{{$customer->pais}}" class="form-control" type="text" name="pais">
        <span class="input-group-addon"><em class="text-muted">{{$customer->pais}}</em></span>
      </div>

      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-map-marker fa-fw"></i></span>
        <select name="provincia" class="form-control">
          <option value="{{$customer->provincia}}" selected="selected">{{$customer->provincia}} - Actual</option>
          <option value="Buenos Aires">Buenos Aires</option>
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
        <input value="{{$customer->ciudad}}" class="form-control" type="text" name="ciudad" placeholder="Ciudad">
        <span class="input-group-addon"><em class="text-muted">{{$customer->ciudad}}</em></span>
      </div>

      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-phone-square fa-fw"></i></span>
        <input value="{{$customer->carac}}" class="form-control" type="text" name="carac" placeholder="Carac">
        <span class="input-group-addon"><em class="text-muted">{{$customer->carac}}</em></span>
      </div>

      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-phone fa-fw"></i></span>
        <input value="{{$customer->tel}}" class="form-control" type="text" name="tel" placeholder="Tel">
        <span class="input-group-addon"><em class="text-muted">{{$customer->tel}}</em></span>
      </div>

      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-mobile fa-fw"></i></span>
        <input value="{{$customer->cel}}" class="form-control" type="text" name="cel" placeholder="Cel">
        <span class="input-group-addon"><em class="text-muted">{{$customer->cel}}</em></span>
      </div>

      <button class="btn btn-primary" type="submit">Modificar</button>
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="ID" value="{{$customer->ID}}">
    </form>
  </div>
  @else
  <h1>No se encontraron datos</h1> @endif
</div>
