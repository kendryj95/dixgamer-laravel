@if(count($datas) > 0)
  <div class="row" id="{{ $sectionId }}">
    <div class="page-header"><h3 >{{ $title }}</h3> </div>

    @foreach($datas as $data)

      <div class="col-xs-6 col-sm-1b" style="width: 12.5%;">

        <div class="thumbnail">
          <div>

            <div style="position:relative; overflow:hidden; padding-bottom:100%;">
              <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;">
                {{ str_replace('-', ' ', $data->titulo) }}
              </span>
              <a title="vender {{$data->titulo}}"

                  href="javascript:void(0)" data-toggle="modal" data-target=".modalConfirm" onclick="$('#modalVentas').modal('hide');getPageAjax('{{ url('customer_confirmUpdateProduct',[$data->consola,$data->titulo,$slot,$id_ventas]) }}','#modalConfirm')">

                <img src="{{asset('img/productos/'.$data->consola.'/'.$data->titulo.'.jpg')}}" alt="{{$data->consola}} - {{$data->titulo}}.jpg"
                    class="img img-responsive full-width"
                    style="border-radius:5px; position:absolute;">

              </a>

              <span class="badge @if($data->q_stock > 4) badge-success @else badge-danger @endif pull-right"
                    style="position: relative; top: 8px; left: -8px;">

                <?php // Valido que sea administrador o analista para mostrar valor real ?>
                @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                    {{$data->q_stock}}
                @else

                  @if($data->q_stock > 10)
                    +10
                  @else
                    {{$data->q_stock}}
                  @endif

                @endif
              </span>
            </div>

          </div>

          <div class="caption text-center">
           <a
             href="{{ url('/cuentas', [$data->stk_ctas_id]) }}"
             title="Ir a Cuenta"
             role="button"
             class="btn btn-xs">
              <i class="fa fa-link fa-fw" aria-hidden="true"></i>
              {{ $data->stk_ctas_id }}
            </a>

            <small style="color:#CFCFCF;">
              <i class="fa fa-gamepad fa-fw" aria-hidden="true"></i>
              {{ $data->id_stk }}
            </small>

           <br />

           @if(Helper::validateAdministrator(session()->get('usuario')->Level))
             <span class="badge badge-default">
               ${{$data->costo}}
             </span>
           @endif
          </div>
        </div>

      </div>
    @endforeach

</div>
@endif
