@php
  $userAuth = session()->get('usuario');
@endphp
@if(count($stocks) > 0)
  <div class="row">
    <div class="page-header"><h3 style="color:#000;text-align: left;">{{ $title }}</h3> </div>

    <?php // Si hay stock los recorre ?>
    @foreach($stocks as $stock)

      <div class="col-sm-1b col-xs-6" style="width: 12.5%">
        <div class="thumbnail" >
          <div>

            <div style="position:relative; overflow:hidden; padding-bottom:100%;">
              <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em; left: 0px ">
                {{ str_replace('-', ' ', $stock->titulo) }}
              </span>
              <a title="vender {{$stock->titulo}}"
                  href="javascript:void(0)" data-toggle="modal" data-target=".modalConfirm" onclick="$('#modalVentas').modal('hide');getPageAjax('{{ url('customer_confirmDuplicarVenta',[$stock->consola,$stock->titulo,'No',$id_ventas]) }}','#modalConfirm')">

                {{-- <img src="/img/productos/{{$stock->consola}}/{{$stock->titulo}}.jpg" alt="{{$stock->consola}} - {{$stock->titulo}}.jpg" --}}
                <img src="{{ asset('img/productos/'.$stock->consola.'/'.$stock->titulo.'.jpg')}}" alt="{{$stock->consola}} - {{$stock->titulo}}.jpg"
                    class="img img-responsive full-width"
                    style="border-radius:5px; position:absolute;">

              </a>
            </div>

            <span class="badge @if($stock->q_stock > 4) badge-success @else badge-danger @endif pull-right"
                  style="position: relative; top: 8px; left: -8px;">

              <?php // Valido que sea administrador o analista para mostrar valor real ?>
              @if(Helper::validateAdminAnalyst(session()->get('usuario')->Level) || $userAuth->Nombre === "Leo")
                  {{$stock->q_stock}}
              @else

                @if($stock->q_stock > 99)
                  +99
                @else
                  {{$stock->q_stock}}
                @endif

              @endif
            </span>
          </div>
          <div class="caption text-center">
            <small style="color:#CFCFCF;">
              <i class="fa fa-gamepad fa-fw" aria-hidden="true"></i>
              {{$stock->id_stk}}
            </small>
            <br />
              @if(Helper::validateAdministrator(session()->get('usuario')->Level) || $userAuth->Nombre === "Leo")
                <span class="badge badge-default">
                  ${{$stock->costo}}
                </span>
              @endif
          </div>
        </div>
      </div>
    @endforeach

  </div>
@endif
