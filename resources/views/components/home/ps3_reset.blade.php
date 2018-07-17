@if(count($datas) > 0)
  <div class="row" id="{{ $sectionId }}">
    <div class="page-header"><h3 >{!! $title !!}</h3> </div>

    @foreach($datas as $data)

      <div class="col-xs-6 col-sm-1b" style="width: 12.5%;">

        <div class="thumbnail">
           <div style="position:relative; overflow:hidden; padding-bottom:100%;">
           	<span style="position:absolute; z-index:100; bottom: 0px; BACKGROUND-COLOR: rgba(0, 0, 0, 0.8); font-size: 0.8em; opacity:0.8; color:#FFF; padding:5px;">
              (
              @if($data->days_from_vta > 6)
                + 7
              @else
                {{$data->days_from_vta}} días
              @endif
              ) {{ $data->titulo }}
            </span>

           	<img src="/img/productos/{{ $data->consola }}/{{$data->titulo}}.jpg"
                  alt="{{ $data->consola }} - {{ $data->titulo }}.jpg"
                  class="img img-responsive full-width" style="position:absolute;" />
           </div>
          <div class="caption text-center">
           <a href="{{ url('cuentas/') . '/' . $data->stk_ctas_id }}"
             title="Ir a Cuenta"
             role="button"
             class="btn btn-xs btn-{{ ($data->days_from_vta > 6) ? 'success' : 'normal'  }}">
             <i class="fa fa-link fa-fw" aria-hidden="true"></i>
             {{ $data->stk_ctas_id }}
           </a> <small style="color:#CFCFCF">

             <i class="fa fa-gamepad fa-fw" aria-hidden="true"></i>

             {{ $data->id_stk }}
           </small>

          </div>
        </div>


      </div>
    @endforeach

</div>
@endif
