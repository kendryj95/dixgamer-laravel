
<div class="container">

  <div class="row">
    @if(count($gifts) > 0)

      <h1 class="colorText">Cargar Saldo Minim - Cuenta #{{$account_id}}</h1>

      <div class="col-md-12 text-right">
        <label class="colorText" for="">Modo continuo</label>
        @php $modo_continuo = session()->get('usuario')->modo_continuo; @endphp
        <input type="checkbox" id="modo-continuo" @if ($modo_continuo == 1) checked @endif data-size="small">
      </div>

      @php
      $g = 1;
      @endphp
      
      @foreach($gifts as $i => $gift)

        @if($gift->costo_usd < 10) {{-- Solo se mostraran saldos menor a 10 USD --}}

        <div class="col-xs-6 col-sm-2">

            <div class="thumbnail colorBground">

              <div style="height: 185px; border: 1px solid #ccc">
                @if (session()->get('usuario')->modo_continuo == 0)
                  <label class="badge badge-danger pull-right" style="margin: 2px;">{{ $gift->Q_Stock }}</label>
                @else
                  <label class="badge badge-danger pull-right" style="margin: 2px;">{{ $gift->Q_GC }} GC</label>
                  <label class="badge badge-warning pull-right" style="margin: 2px;">{{ $gift->Q_Uso }} usos</label>
                @endif
                
                <a
                  title="cargar saldo"
                  onclick="request(event)"
                  id="cargar_minim_{{str_replace(['.',','],['',''],$gift->costo_usd)}}"
                  href="{{url('crear_saldo_minimo_cuenta',[$account_id,$gift->ID_stk])}}">
                
                  <div class="colorText">
                      <br>
                      <br>
                      
                      <h2>{{ number_format($gift->costo_usd,2,".","") }}</h2>
                      
                  </div> 
                </a>
              </div>

              

            </div>

        </div>

        @if($g == 5)
        <div class="clearfix"></div>
        @php $g = 0 @endphp
        @endif

        @php $g++ @endphp
          

        @endif

      @endforeach
      
    @else
      <h2 style="color:#000">No se encontraron datos</h2>
    @endif
  </div>
</div>
<script>
  $(document).ready(function() {
    $('#modo-continuo').bootstrapToggle();

    initColors();

    $('#modo-continuo').on('change', function(){
      var elswitch = $(this).prop('checked');
      var modoContinuo = elswitch ? 1 : 0;

      $.ajax({
        url: '{{ url('modo_continuo') }}',
        type: 'GET',
        dataType: 'json',
        data: {modo_continuo: modoContinuo},
        success: function(response) {
          if (response.status == 202) {
            loadGift(elswitch);
          }
        }
      });
    });
  });

  function initColors() {
    @if(session()->get('usuario')->modo_continuo == 1)
      $('.modal-body').css('background','#000');
      $('.colorBground').css('background','#000');
      $('.colorText').css('color', '#FFF');
    @else
      $('.modal-body').css('background','#FFF');
      $('.colorBground').css('background','#FFF');
      $('.colorText').css('color', '#000');
    @endif
  }

  function loadGift(elswitch) {
    // Modal
    var modal = $("#modal-container");
    // Limpiamos la seccion

    var param = '/{{ $account_id }}';

    var path = '{{ url('recharge_minim_account') }}';

    modal.find('.modal-body').html('');
    $.ajax({
      url: path+param,
      type: 'GET',
      dataType: 'HTML',
      beforeSend: function(){
        modal.find('.modal-body').html('Cargando');
      },
      success:function(data){
        modal.find('.modal-body').html(data);
      }
    })
    .fail(function() {
      console.log("error");
    });
  }
</script>