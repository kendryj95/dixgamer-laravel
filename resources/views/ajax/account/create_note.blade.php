<div class="container">
  <h1 style="color:#000">Agregar Nota - Cuenta #{{$account}}</h1>
  <div class="row">

    <form method="post" id="form_create_note" action="{{ url('guardar_nota_cuenta',[$account]) }}">
      {{ csrf_field() }}
      <div class="input-group form-group">
        <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
        <textarea class="form-control" rows="4" name="notes" id="Notas" style="font-size: 22px;"></textarea>

      </div>
      <button class="btn btn-warning btn-block" id="create_note" type="button">Guardar</button>
    </form>

  </div>
</div>

<script>
  $(document).ready(function(){
    $('#create_note').on('click', function(){
      $(this).prop('disabled', true);
      $('#form_create_note').submit();
    });

    setTimeout(function(){
      document.getElementById('Notas').focus();
    },600)
  });
</script>
