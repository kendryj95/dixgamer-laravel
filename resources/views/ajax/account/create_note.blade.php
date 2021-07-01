<style>
  .select2-results__option[aria-selected=true] {
    background: #cccccc !important;
  }
</style>
<div class="container">
  <div class="row">

    <div class="col-md-1 col-lg-1"></div>
    <div class="col-lg-6 col-md-6">
        <h3 style="color:#000;text-align: left">Agregar Nota - Cuenta #{{$account}}</h3>

      <form method="post" id="form_create_note" action="{{ url('guardar_nota_cuenta',[$account]) }}">
        {{ csrf_field() }}

        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-users fa-fw"></i></span>
          <select id="cliente" name="cliente" class="selectpicker form-control" data-live-search="true" data-size="5">
              <option value="" selected>¿Desea dirigir la nota a un cliente?</option>
              @foreach($clientes_sales as $cliente)
              <option value="{{ $cliente->clientes_id }}">{{ $cliente->cliente }}</option>
              @endforeach
  
          </select>
        </div>
        
        <div class="input-group form-group">
          <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
          <textarea class="form-control" rows="4" name="notes" id="Notas" style="font-size: 22px;"></textarea>

        </div>
        <div style="margin: 5px 5px;" class="btn-group btn-group-justified" role="group" aria-label="accesos-directos">
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-secondary" onclick="shortcut('sony')">Sony solicita...</button>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-secondary" onclick="shortcut('cte_ps3')">Cte PS3 no descargó</button>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-secondary" onclick="shortcut('cambio_pass')">Cambio de pass por...</button>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-secondary" onclick="shortcut('secu_pri')">Secu se activa Pri</button>
          </div>
        </div>

        <div style="display: flex;padding-left: 5px;margin-bottom: 5px;">
          <button style="width: 25%" type="button" class="btn btn-secondary btn-sm" onclick="shortcut('sin_ps3_activas')">Sin ps3 activas</button>

        </div>


        <button class="btn btn-warning btn-block" id="create_note" type="button">Guardar</button>
      </form>
    </div>
    <div class="col-md-1 col-lg-1"></div>
    <div class="col-lg-4 col-md-4">
      <h3 style="color:#000;text-align: left">Notas Predefinidas</h3>

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

    $('.selectpicker').selectpicker();

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

  function shortcut(type) {
    $("#Notas").text("");

    switch (type) {
      case "sony":
        $("#Notas").text("Sony solicita cambiar pass");
        break;
      case "cte_ps3":
        $("#Notas").text("Cte PS3 no descargó");
        break;
      case "cambio_pass":
        $("#Notas").text("Cambio de pass por cambio/devolución");
        break;
      case "secu_pri":
        $("#Notas").text("Secu se activa Pri");
        break;
      case "sin_ps3_activas":
        $("#Notas").text("Sin ps3 activas");
        break;
    }
  }

  function initSelect2(){
    $( "#cliente-select" ).select2({
        theme: "bootstrap"
    });
  }
</script>
