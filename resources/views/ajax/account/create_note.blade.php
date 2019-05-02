<div class="container">
  <div class="row">

    <div class="col-lg-5 col-md-5">
        <h3 style="color:#000">Agregar Nota - Cuenta #{{$account}}</h3>

      <form method="post" id="form_create_note" action="{{ url('guardar_nota_cuenta',[$account]) }}">
        {{ csrf_field() }}
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
          <textarea class="form-control" rows="4" name="notes" id="Notas" style="font-size: 22px;"></textarea>

        </div>
        <button class="btn btn-warning btn-block" id="create_note" type="button">Guardar</button>
      </form>
    </div>
    <div class="col-lg-2 col-md-2"></div>
    <div class="col-lg-5 col-md-5">
      <h3 style="color:#000">Notas Predefinidas</h3>

      <div class="form-group">
        
        <button class="btn btn-secondary" id="btnCambiaronId">Cambiaron ID</button>
      </div>


      <div id="div-clientes" style="display: none;">
        <form action="{{ url('notas_predefinidas') }}" method="post">
          {{ csrf_field() }}
          <input type="hidden" name="id" value="{{ $account }}">
          <input type="hidden" name="opt" value="1">
          <div class="form-group">
              <select name="clientes[]" id="cliente-select" value="" class="form-control select2-multiple" multiple>
                  @foreach($clientes_sales as $cliente)
                  <option value="{{ $cliente->clientes_id }}">{{ $cliente->cliente }}</option>
                  @endforeach
              </select><br>

              <button type="submit" class="btn btn-primary btn-block">Guardar</button>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>

<script>
  $(document).ready(function(){
    $('#create_note').on('click', function(){
      $(this).prop('disabled', true);
      $('#form_create_note').submit();
    });

    $('#btnCambiaronId').on('click', function(event) {
      var divClientes = $('#div-clientes');

      if (divClientes.is(':visible')) {
        divClientes.fadeOut();
      } else {
        divClientes.fadeIn();

        setTimeout(function(){
          initSelect2();
        },200);
      }
    });

    setTimeout(function(){
      document.getElementById('Notas').focus();
    },600)
  });

  function initSelect2(){
    $( "#cliente-select" ).select2({
        theme: "bootstrap"
    });
  }
</script>
