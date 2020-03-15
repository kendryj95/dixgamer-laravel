
@if($account)
<div class="container">
  <h1 class="text-center" style="color:#000">Modificar Cuenta #{{$account->ID}}</h1>

  <div class="row">
    <div class="col-sm-4">
    </div>

    <div class="col-sm-4">
      <form method="post" name="form1" action="{{url('cuentas',[$account->ID])}}">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        <em class="text-muted">{{$account->mail}}</em>

        <div id="user-result-div" class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
          <input
            value="{{$account->mail}}"
            class="form-control"
            type="text"
            name="mail"
            id="mail"
            autocomplete="off"
            spellcheck="false"
            placeholder="Mail Real"
            autofocus>
          <span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
        </div>

        <em class="text-muted">{{$account->mail_fake}}</em>
        <div id="user-result-div" class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
          <input
            value="{{$account->mail_fake}}"
            class="form-control"
            type="text"
            name="mail_fake"
            id="mail_fake"
            autocomplete="off"
            spellcheck="false"
            placeholder="Mail Fake" autofocus>
          <span class="input-group-addon"><i id="user-result" class="fa fa-pencil" aria-hidden="true"></i></span>
        </div>

        <em class="text-muted">{{$account->name}}</em>
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
          <input class="form-control" type="text" value="{{$account->name}}" name="name" placeholder="Name">
        </div>

        <em class="text-muted">{{$account->surname}}</em>
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
          <input class="form-control" value="{{$account->surname}}" type="text" name="surname" placeholder="Surname">
        </div>

        <em class="text-muted">{{$account->pass}}</em>
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
          <input class="form-control" value="{{$account->pass}}" type="text" name="pass">
        </div>
        <br />

        <button class="btn btn-primary btn-block btn-lg" type="submit">Modificar</button>
      </form>
    </div>

    <div class="col-sm-4">
    </div>

  </div>
</div>
@else
  <h2>Datos no encontrados</h2>
@endif
