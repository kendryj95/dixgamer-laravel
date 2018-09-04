
<div class="container">

  <div class="row">
    @if(count($gifts) > 0)

      <h1 style="color:#000">Cargar Saldo - Cuenta #{{$account_id}}</h1>
      @php
        $bandera1 = 0;
        $bandera2 = 0;
        $bandera3 = 0;
        $gifts_array = [];
      @endphp
      @foreach($gifts as $gift)

        @if ($gift->titulo == 'gift-card-10-usd' || $gift->titulo == 'gift-card-50-usd') <!-- Determinar si las dos gift card están disponibles -->

          @php

            $bandera1++;
          @endphp
        @endif

        @if ($gift->titulo == 'gift-card-10-usd' || $gift->titulo == 'gift-card-20-usd') <!-- Determinar si las dos gift card están disponibles -->

          @php

            $bandera2++;
          @endphp
        @endif

        @if ($gift->titulo == 'gift-card-20-usd') <!-- Determinar si las dos gift card están disponibles -->

          @php

            $bandera3++;
          @endphp
        @endif

        @php
            if(($gift->costo_usd*100) < 30) {
              $gifts_array[] = [
                "account_id" => $account_id,
                "titulo" => $gift->titulo,
                "consola" => $gift->consola,
                "costo_usd" => $gift->costo_usd*100
              ];
            } elseif (($gift->costo_usd*100) < 60) {
              if ($bandera1 == 2) { // Para 20+10 y 20+20
                $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => 'gift-card-30-usd-org',
                  "consola" => $gift->consola,
                  "costo_usd" => 30
                ];
              }
              if ($bandera2 == 2) {
                $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => 'gift-card-40-usd-org',
                  "consola" => $gift->consola,
                  "costo_usd" => 40
                ];
              }
              $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => $gift->titulo,
                  "consola" => $gift->consola,
                  "costo_usd" => $gift->costo_usd*100
              ];
            } else {
              if ($bandera3 != 0) {
                $gifts_array[] = [
                    "account_id" => $account_id,
                    "titulo" => 'gift-card-60-usd-org',
                    "consola" => $gift->consola,
                    "costo_usd" => 60
                ];
              }
              $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => $gift->titulo,
                  "consola" => $gift->consola,
                  "costo_usd" => $gift->costo_usd*100
              ];
            }
        @endphp

      @endforeach

      @foreach ($gifts_array as $gift)

      
      <div class="col-xs-6 col-sm-2">

          <div class="thumbnail">

            <div style="height: 305px; border: 1px solid #ccc">
              <a
                title="cargar saldo"
                onclick="request(event)"
                href="{{url('crear_saldo_cuenta',[$gift['account_id'],$gift['titulo'],$gift['consola']])}}">
              
                <div style="color: #000">
                    <br>
                    <br>
                    <br><br>
                    <br>
                    @if ($gift['titulo'] == 'plus-12-meses')
                      <h4>{{ $gift['titulo'] }}</h4>
                    @else
                      <h2>{{ $gift['costo_usd'] }} USD</h2>
                    @endif
                    
                </div> 
              
              
                
              </a>
            </div>

            

          </div>

      </div>
      @endforeach
    @else
      <h2 style="color:#000">No se encontraron datos</h2>
    @endif
  </div>
</div>
