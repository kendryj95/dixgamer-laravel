
<div class="container">

  <div class="row">
    @if(count($gifts) > 0)

      <h1 style="color:#000">Cargar Saldo Minim - Cuenta #{{$account_id}}</h1>
      @php
      $g = 1;
      @endphp
      
      @foreach($gifts as $i => $gift)

        @if($gift->costo_usd < 10) {{-- Solo se mostraran saldos menor a 10 USD --}}

        <div class="col-xs-6 col-sm-2">

            <div class="thumbnail">

              <div style="height: 185px; border: 1px solid #ccc">
                <a
                  title="cargar saldo"
                  onclick="request(event)"
                  href="{{url('crear_saldo_cuenta',[$account_id,$gift->titulo,$gift->consola])}}">
                
                  <div style="color: #000">
                      <br>
                      <br>
                      
                      <h2>{{ number_format($gift->costo_usd,0,"","") }}</h2>
                      
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
