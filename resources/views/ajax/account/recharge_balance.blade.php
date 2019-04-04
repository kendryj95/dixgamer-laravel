
<div class="container">

  <div class="row">
    @if(count($gifts) > 0)

      <h1 style="color:#000">Cargar Saldo - Cuenta #{{$account_id}}</h1>
      @php
        $bandera1 = 0;
        $bandera2 = 0;
        $bandera3 = 0;
        $mayor_60 = false;
        $gifts_array = [];
      @endphp
      @foreach($gifts as $i => $gift)

      @if($gift->titulo != '20-off-playstation')

        @if ($gift->titulo == 'gift-card-10-usd' || $gift->titulo == 'gift-card-50-usd' || $gift->titulo == 'gift-card-25-usd') <!-- Determinar si las dos gift card están disponibles -->

          @php

            $bandera1++;
          @endphp
        @endif

        @if ($gift->titulo == 'gift-card-10-usd' || $gift->titulo == 'gift-card-20-usd' || $gift->titulo == 'gift-card-25-usd') <!-- Determinar si las dos gift card están disponibles -->

          @php

            $bandera2++;
          @endphp
        @endif

        @if ($gift->titulo == 'gift-card-20-usd' || $gift->titulo == 'gift-card-25-usd') <!-- Determinar si las dos gift card están disponibles -->

          @php

            $bandera3++;
          @endphp
        @endif

        @endif

        @php
            if(($gift->costo_usd) < 30) {
              $gifts_array[] = [
                "account_id" => $account_id,
                "titulo" => $gift->titulo,
                "consola" => $gift->consola,
                "costo_usd" => number_format($gift->costo_usd,0,"","")
              ];
            } elseif (($gift->costo_usd) < 60) {
              if ($bandera1 >= 2) { // Para 20+10 y 20+20
                $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => 'gift-card-30-usd-org',
                  "consola" => $gift->consola,
                  "costo_usd" => 30
                ];
                $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => 'gift-card-35-usd-org',
                  "consola" => $gift->consola,
                  "costo_usd" => 35
                ];
              }
              if ($bandera2 >= 2) {
                $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => 'gift-card-40-usd-org',
                  "consola" => $gift->consola,
                  "costo_usd" => 40
                ];

                $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => 'gift-card-45-usd-org',
                  "consola" => $gift->consola,
                  "costo_usd" => 45
                ];
              }
              $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => $gift->titulo,
                  "consola" => $gift->consola,
                  "costo_usd" => number_format($gift->costo_usd,0,"","")
              ];
              if ($bandera3 == 2) {
                $gifts_array[] = [
                  "account_id" => $account_id,
                  "titulo" => 'gift-card-55-usd-org',
                  "consola" => $gift->consola,
                  "costo_usd" => 55
                ];

                $gifts_array[] = [
                    "account_id" => $account_id,
                    "titulo" => 'gift-card-60-usd-org',
                    "consola" => $gift->consola,
                    "costo_usd" => 60
                ];
              }
            } else {
              $mayor_60 = true;
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
                  "costo_usd" => number_format($gift->costo_usd,0,"","")
              ];
            }
        @endphp

      @endforeach

      @php

      $array_temp = []; 

      @endphp

      @foreach ($gifts_array as $gift)

      @if ($gift['titulo'] != '20-off-playstation')
        @if(!in_array($gift['titulo'],$array_temp)) {{-- Evitar duplicaciones --}}

         @php $array_temp[] = $gift['titulo'] @endphp
      
      <div class="col-xs-6 col-sm-2">

          <div class="thumbnail">

            <div style="height: 185px; border: 1px solid #ccc">
              <a
                title="cargar saldo"
                onclick="request(event)"
                href="{{url('crear_saldo_cuenta',[$gift['account_id'],$gift['titulo'],$gift['consola']])}}">
              
                <div style="color: #000">
                    <br>
                    <br>
                    
                      @if ($gift['titulo'] == 'plus-12-meses')
                        <h4>{{ $gift['titulo'] }}</h4>
                      @else
                        <h2>{{ $gift['costo_usd'] }}</h2>
                      @endif
                    
                    
                </div> 
              
              
                
              </a>
            </div>

            

          </div>

      </div>
          @endif
        @endif
      @endforeach
    @else
      <h2 style="color:#000">No se encontraron datos</h2>
    @endif
  </div>
</div>
