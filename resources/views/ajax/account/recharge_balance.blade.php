
<div class="container">

  <div class="row">
    @if(count($gifts) > 0)

      <h1 style="color:#000">Cargar Saldo - Cuenta #{{$account_id}}</h1>
      @php
        $bandera = 0;
      @endphp
      @foreach($gifts as $gift)

        @if ($gift->titulo == 'gift-card-10-usd' || $gift->titulo == 'gift-card-50-usd') <!-- Determinar si las dos gift card están disponibles -->

          @php

            $bandera++;
          @endphp
        @endif

        <div class="col-xs-6 col-sm-2">

          <div class="thumbnail">

            <a
              title="cargar saldo"
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

      @if ($bandera == 2)

      <div class="col-xs-6 col-sm-2">

        <div class="thumbnail">

          <a
            title="cargar saldo"
            href="{{url('crear_saldo_cuenta/'.$account_id.'/gift-card-60-usd-org/'.$gift->consola)}}">

          <div>
            <img
              src="{{asset('img/productos/ps/gift-card-60-usd.jpg')}}"
              alt="gift-card-60-usd-org.jpg"
              class="img img-responsive full-width" />
          </div>

          </a>
          <div class="caption text-center">
            <label for="" class="label label-info">2 en 1: $10 + $50</label>
          </div>

        </div>

      </div>

      @endif
    @else
      <h2 style="color:#000">No se encontraron datos</h2>
    @endif
  </div>
</div>
