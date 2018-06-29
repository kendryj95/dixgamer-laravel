<div class="container">
  @if($count($customer) > 0)
  <h1>Usuario de ML</h1>
  <div class="row">
    <form method="post" name="form1" action="<?php echo $editFormAction; ?>">

      <div id="user-result-div" class="input-group form-group">

        <span class="input-group-addon">
          <i class="fa fa-snapchat-ghost fa-fw"></i>
        </span>

        <input
          value="{{ $customer->ml_user }}"
          class="form-control"
          type="text"
          name="ml_user"
          id="ml_user"
          autocomplete="off"
          spellcheck="false"
          placeholder="ML User"
          autofocus>

        <span class="input-group-addon">
          <em class="text-muted">{{ $customer->ml_user }}</em>
        </span>
      </div>

      <button class="btn btn-primary btn-block btn-lg" type="submit">Actualizar</button>
    </form>
  </div>
  @else
    <h1>No se encontraron datos</h1>
  @endif
</div>
