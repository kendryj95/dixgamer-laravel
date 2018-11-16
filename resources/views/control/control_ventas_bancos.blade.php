@extends('layouts.master-layouts')
@section('title', 'Listar Ventas por Bancos')

@section('container')

@if (count($errors) > 0)
    <div class="alert alert-danger text-center">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
@endif

    <div class="container">
	<h1>Listar Ventas por Bancos</h1>
    <!-- InstanceBeginEditable name="body" -->
    <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
    <thead>
              <tr>
				<th>#</th>
                <th>ID Vta</th>
                <th>Fecha</th>
                <th width="50">Cover</th>
                <th>Cliente</th>
                <th>Medios</th>
                <th>Precio</th>
                <th>Operador</th>
                <th></th>
              </tr>
            </thead>
		  <tbody>
          
          @foreach ($ventas as $i => $venta)
         <?php
         	$color1 = '';
         	$color2 = '';
         	$text = '';
         	$text2 = '';
           if (strpos($venta->medio_venta, 'Web') !== false): $text = 'W'; $color1 = \Helper::medioVentaColor($venta->medio_venta);
           elseif (strpos($venta->medio_venta, 'Mail') !== false): $text = 'M'; $color1 = \Helper::medioVentaColor($venta->medio_venta);
           elseif (strpos($venta->medio_venta, 'Mercado') !== false): $text = 'ML'; $color1 = \Helper::medioVentaColor($venta->medio_venta);
           endif;
         ?>

         <?php
           if ($venta->medio_cobro == 'Banco'): $text2 = 'Bco'; $color2 = \Helper::medioCobroColor($venta->medio_cobro);
           elseif ($venta->medio_cobro == 'MP - Ticket'): $text2 = 'Cash'; $color2 = \Helper::medioCobroColor($venta->medio_cobro);
           elseif ($venta->medio_cobro == 'MP' || $venta->medio_cobro   == 'MP - Tarjeta'): $text2 = 'MP'; $color2 = \Helper::medioCobroColor($venta->medio_cobro);
           elseif ($venta->medio_cobro == 'Fondos'): $text2 = 'F'; $color2 = \Helper::medioCobroColor($venta->medio_cobro);
           endif;
         ?>
		<?php
			$persona = $venta->ventas_usuario;
		?>
		
        
          	<td><?php echo $i+1;?></td>
          	<td><?php echo $venta->ID_ventas; ?></td>
            <td><?php echo date("d-M", strtotime($venta->ventas_Day)); ?></td>
            <td><img class="img-rounded" width="50" id="image-swap" src="{{asset('img/productos')}}/<?php echo $venta->consola."/".$venta->titulo.".jpg";?>"alt="" /></td>
            <td><a title="Ir a Cliente" href="{{ url('clientes', $venta->clientes_id) }}"><?php echo $venta->nombre; ?> <?php echo $venta->apellido; ?></a><br /><br /><span style="opacity:0.5" class="text-muted btn-xs"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> <?php echo $venta->stock_id; ?></span> <?php if ($venta->cuentas_id):?><a style="opacity:0.5" class="text-muted btn-xs" href="{{ url('cuentas', $venta->cuentas_id) }}" title="Ir a Cuenta"><i class="fa fa-link fa-xs fa-fw" aria-hidden="true"></i> <?php echo $venta->cuentas_id; ?></a> <?php endif; ?><?php if ($venta->slot == 'Secundario'): ?><span class="label label-danger" style="opacity:0.5">2°</span><?php endif; ?></td>
            <td><small class="label label-<?php echo $color1;?>" style="opacity:0.7; font-weight:400;" title="<?php echo $venta->medio_venta; ?>"><?php echo $text;?></small> <small class="label label-<?php echo $color2;?>" style="opacity:0.7; font-weight:400;" title="<?php echo $venta->medio_cobro; ?>"><?php echo $text2;?></small></td>
            <td><span class="<?php if ($venta->precio < 1):?>badge badge-danger<?php endif;?>"><?php echo round($venta->precio); ?></span></td>
            <td><span class="badge badge-{{ \Helper::userColor($venta->ventas_usuario) }} pull-left" style="opacity:0.7; font-weight:400;" title="{{ $venta->ventas_usuario }}"><?php echo substr($venta->ventas_usuario,0 , 1); ?></span></td>
            <td align="left">@if ($venta->verificado == 1) <i class="fa fa-check text-muted pull-left" title="Verificado"></i> @else <a href="{{ url('verificar_venta_banco',$venta->cobro_ID) }}" class="pull-left" title="Sin verificar"><i class="fa fa-check"></i></a> @endif</td>
            
          </tr>
        @endforeach
        </tbody>
        </table>
        

          </div>

          <div class="col-md-12">

              <ul class="pager">
                <ul class="pagination">

                  @php
                      if (isset($_GET['pag'])) {
                        if ($_GET['pag'] != 1) {
                          $previous = intval($_GET['pag']) - 1;
                        } else {
                          $previous = 1;
                        }
                      } else {
                        $previous = 1;
                      }
                  @endphp
                      @if ($previous == 1 && (!isset($_GET['pag']) || $_GET['pag'] == 1))

                      <li class="disabled"><span>«</span></li>
                      @else
                      <li><a href="{{url('control_ventas_bancos')}}?pag={{ $previous }}">«</a></li>
                      @endif 
                     
                     @if ($paginas >= 1 && $paginas <= 5)
                       @for ($i=0;$i<$paginas;$i++)
                         @php 
                         $active = isset($_GET['pag']) ? ($i+1) == $_GET['pag'] ? 'active' : "" : ($i+1) == 1 ? 'active' : ""; 
                         @endphp
                         <li class="{{ $active }}"><a href="{{url('control_ventas_bancos')}}?pag=<?= $i+1 ?>"><?= $i+1 ?></a></li>
                       @endfor
                     @else
                       @if (($paginaAct+4) <= ($paginas))
                         @for ($i=$paginaAct;$i<=($paginaAct+4);$i++)
                              @php
                                  $active = isset($_GET['pag']) ? ($i) == $_GET['pag'] ? 'active' : "" : ($i) == 1 ? 'active' : "";
                              @endphp
                           <li class="{{ $active }}"><a href="{{url('control_ventas_bancos')}}?pag=<?= $i ?>"><?= $i ?></a></li>
                         @endfor
                       @elseif ($paginaAct == $paginas)
                         @for ($i=($paginaAct-4);$i<=$paginas;$i++)
                              @php
                                  $active = isset($_GET['pag']) ? ($i) == $_GET['pag'] ? 'active' : "" : ($i) == 1 ? 'active' : ""; 
                              @endphp
                           <li class="{{ $active }}"><a href="{{url('control_ventas_bancos')}}?pag=<?= $i ?>"><?= $i ?></a></li>
                         @endfor
                       @else
                         @for ($i=$paginaAct;$i<=$paginas;$i++)
                               @php 
                                  $active = isset($_GET['pag']) ? ($i) == $_GET['pag'] ? 'active' : "" : ($i) == 1 ? 'active' : ""; 
                               @endphp
                           <li class="{{ $active }}"><a href="{{url('control_ventas_bancos')}}?pag=<?= $i ?>"><?= $i ?></a></li>
                         @endfor
                       @endif
                       @if (($paginaAct+4) < ($paginas))
                       <li class="disabled"><span>...</span></li>
                       <li><a href="{{url('control_ventas_bancos')}}?pag=<?= $paginas ?>"><?= $paginas ?></a></li>
                       @endif
                     @endif

                      @php

                          if (isset($_GET['pag'])) {
                            if ($_GET['pag'] != $paginas) {
                              $next = intval($_GET['pag']) + 1;
                            } else {
                              $next = $paginas;
                            }
                          } else {
                            $next = 2;
                          }

                      @endphp

                      @if (($next == $paginas) || (!isset($_GET['pag']) && ($paginas-1)==0))

                      <li class="disabled"><span>»</span></li>
                      @else
                      <li><a href="{{url('control_ventas_bancos')}}?pag={{ $next }}" rel="next">»</a></li>
                      @endif
                  </ul>

              </ul>

          </div>
     <!--/row-->
     <!-- InstanceEndEditable -->
    </div><!--/.container-->




@endsection