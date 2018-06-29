<div class="container">
  @if($count($customer) > 0)
  <h1>Modificar E-mail</h1>
  <div class="row">
    <form method="post" name="form1" action="{{ url('actualizar_email_cliente',[$customer->ID]) }}">

      <div id="user-result-div" class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
        <input
          value="{{$customer->email}}"
          class="form-control"
          type="text"
          name="email" id="email"
          autocomplete="off"
          spellcheck="false"
          placeholder="Email" autofocus>
        <span class="input-group-addon">
          <em class="text-muted">{{$customer->email}}</em>
        </span>
      </div>

      <button class="btn btn-primary btn-block btn-lg" type="submit">Actualizar</button>
    </form>
  </div>
  @else
    <h1>No se encontraron datos</h1>
  @endif
</div>
