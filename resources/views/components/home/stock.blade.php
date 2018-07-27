@if(count($stocks) > 0)
  <div class="row">
    <div class="page-header"><h3>{{ $title }}</h3> </div>

    <?php // Si hay stock los recorre ?>
    @foreach($stocks as $stock)

      <div class="col-sm-1b col-xs-6" style="width: 12.5%">
        <div class="thumbnail" >
          <div>

            <div style="position:relative; overflow:hidden; padding-bottom:100%;">
              <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;">
                {{ str_replace('-', ' ', $stock->titulo) }}
              </span>
              <a title="vender {{$stock->titulo}}"
                  href="{{ url('sales/manual/add',[$stock->consola,$stock->titulo,'No']) }}">

                <img src="/img/productos/{{$stock->consola}}/{{$stock->titulo}}.jpg" alt="{{$stock->consola}} - {{$stock->titulo}}.jpg"
                    class="img img-responsive full-width"
                    style="border-radius:5px; position:absolute;">

              </a>
            </div>

            <span class="badge @if($stock->q_stock > 4) badge-success @else badge-danger @endif pull-right"
                  style="position: relative; top: 8px; left: -8px;">

              <?php // Valido que sea administrador o analista para mostrar valor real ?>
              @if(Helper::validateAdminAnalyst(Auth::user()->Level))
                  {{$stock->q_stock}}
              @else

                @if($stock->q_stock > 10)
                  +10
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
              @if(Helper::validateAdministrator(Auth::user()->Level))
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
