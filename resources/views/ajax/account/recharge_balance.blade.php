
<div class="container">

  <div class="row">
    @if(count($gifts) > 0)

      <h1 style="color:#000">Cargar Saldo - Cuenta #{{$account_id}}</h1>
      @php
        $bandera1 = 0;
        $bandera2 = 0;
        $bandera3 = 0;
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

        <div class="col-xs-6 col-sm-2">

          <div class="thumbnail">

            <a
              title="cargar saldo"
              onclick="request(event)"
              href="{{url('crear_saldo_cuenta/'.$account_id.'/'.$gift->titulo.'/'.$gift->consola)}}">

            <div>
              <img
                src="{{asset('img/productos/'.$gift->consola.'/'.$gift->titulo.'.jpg')}}"
                alt="{{$gift->consola}} - {{$gift->titulo}}.jpg"
                class="img img-responsive full-width" />

              <span
                class="badge badge-<?php if ($gift->Q_Stock > 4): echo 'success'; else: echo 'danger'; endif;?> pull-right"
                style="position: relative; top: 8px; left: -8px;">
                  @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                    {{ $gift->Q_Stock }}
                  @else
                    @if ($gift->Q_Stock > 4)
                      +4
                    @else
                      {{$gift->Q_Stock}}
                    @endif
                  @endif
              </span>
            </div>

            </a>

            <div class="caption text-center">
              <small style="color:#CFCFCF;">
                @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                  <i class="fa fa-gamepad fa-fw" aria-hidden="true"></i>
                  {{ $gift->ID_stk }}
                @endif
              </small>
            </div>

          </div>

        </div>

      @endforeach

      @if ($bandera1 == 2) {{-- 2 en 1: 50 + 10 --}}

      <div class="col-xs-6 col-sm-2">

        <div class="thumbnail">

          <div style="height: 305px; border: 1px solid #ccc">
            <a
              title="cargar saldo"
              onclick="request(event)"
              href="{{url('crear_saldo_cuenta/'.$account_id.'/gift-card-60-usd-org/'.$gift->consola)}}">
            
            <div style="color: #000">
                <h3>50 + 10</h3>
                <br>
                <h2>60 USD</h2>
            </div> 
            
            
              
            </a>
          </div>
        </div>

      </div>

      @endif

      @if ($bandera2 == 2) {{-- 2 en 1: 20 + 10 --}}

      <div class="col-xs-6 col-sm-2">

        <div class="thumbnail">

          <div style="height: 305px; border: 1px solid #ccc">
            <a
              title="cargar saldo"
              onclick="request(event)"
              href="{{url('crear_saldo_cuenta/'.$account_id.'/gift-card-30-usd-org/'.$gift->consola)}}">
            
            <div style="color: #000">
                <h3>20 + 10</h3>
                <br>
                <h2>30 USD</h2>
            </div> 
            
            
              
            </a>
          </div>
        </div>

      </div>

      @endif

      @if ($bandera3 != 0) {{-- 2 en 1: 20 + 20 --}}

      <div class="col-xs-6 col-sm-2">

        <div class="thumbnail">

          <div style="height: 305px; border: 1px solid #ccc">
            <a
              title="cargar saldo"
              onclick="request(event)"
              href="{{url('crear_saldo_cuenta/'.$account_id.'/gift-card-40-usd-org/'.$gift->consola)}}">
            
            <div style="color: #000">
                <h3>20 + 20</h3>
                <br>
                <h2>40 USD</h2>
            </div> 
            
            
              
            </a>
          </div>
        </div>

      </div>

      @endif
    @else
      <h2 style="color:#000">No se encontraron datos</h2>
    @endif
  </div>
</div>
