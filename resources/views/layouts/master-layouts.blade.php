<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="tienda de productos digitales">
  <meta name="author" content="dixgamer.com argentina">
  <link rel="icon" href="{{asset('favicon.ico')}}">
  <meta name="_token" content="{{ csrf_token() }}">
  <title>@yield('title','Dixgamer panel')</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/css/bootstrap-select.min.css">

  <!-- Font Awesome style desde mi servidor -->
  <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">

  <!-- link a mi css -->
  <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">

  <!-- Bootstrap SITE CSS -->
  <link href="{{ asset('css/site.css') }}" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="{{ asset('css/offcanvas.css') }}" rel="stylesheet">

   <!-- 2017-12-30 Agrego nuevo css de BootFLAT -->
  <link href="{{ asset('css/bootflat.css') }}" rel="stylesheet">

  <!-- Estilo personalizado por mi -->
  <link href="{{ asset('css/personalizado.css') }}" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('js/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">

  <!-- <script src="https://cloud.tinymce.com/5/tinymce.min.js?apiKey=i7mex8wkvz9vr56kpy7j8liybj29rqaiqzj4cvwsznajz481"></script> -->

  @stack('css')
  
  <style media="screen">
    .form-control{
      width: 100% !important;
    }
  </style>
</head>

<body>

    <nav class="navbar navbar-fixed-top navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Navegar</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="{{ url('/') }}">Inicio</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-user fa-fw" aria-hidden="true"></i> Ctes<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ url('clientes') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li class="divider" role="separator"></li>
                <li><a href=" {{ url('clientes','create') }} "><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                </ul>
            </li>

            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-link fa-fw" aria-hidden="true"></i> Ctas<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ url('/cuentas') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li><a href="{{ url('/cuentas_notas') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar Notas</a></li>
                <li><a href="{{ url('/listaYopmail') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Yopmail</a></li>
                <li><a href="{{ url('cuentas_reseteadas') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Reseteadas</a></li>
                <li><a href=" {{url('cuentas_con_saldo') }} "><i class="fa fa-list fa-fw" aria-hidden="true"></i> Saldo libre</a></li>
                <li><a href="{{ url('cuentas_para_ps3') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Para Juego PS3</a></li>
                <li><a href="{{ url('cuentas_para_ps4') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Para Juego PS4</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="{{ url('/cuentas/create') }}"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
              </ul>
            </li>

            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-gamepad fa-fw" aria-hidden="true"></i> Stk<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ url('stock') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li><a href="{{ url('stocks_cargados') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Cargados</a></li>
                @if(\Helper::validateAdministrator(session()->get('usuario')->Level))
                <li><a href="{{ url('pedidos_carga/admin') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Pedidos de carga - Admin</a></li>
                @endif
                <li><a href="{{ url('pedidos_cargar') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Pedidos por Cargar</a></li>
                <li><a href="{{ url('catalogo_link_ps_store') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Link PS Store</a></li>
                <li><a href="{{ url('productos_catalogo') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Catalogo Completo</a></li>

                @if(Helper::validateAdminAnalyst(session()->get('usuario')->Level) || Helper::permissionPerUser(session()->get('usuario')->Nombre, "Gift"))
                    <li class="divider" role="separator"></li>
                    <li><a href="{{ url('falta_cargar') }}"><i class="fa fa-gift fa-fw" aria-hidden="true"></i> Falta Cargar</a></li>
                    <li><a href="{{ url('stock_insertar_codigo') }}"><i class="fa fa-gift fa-fw" aria-hidden="true"></i> P1</a></li>
                @endif

                @if(\Helper::validateAdministrator(session()->get('usuario')->Level))
                <li><a href="{{ url('stock_insertar_codigo_control') }}"><i class="fa fa-gift fa-fw" aria-hidden="true"></i> P1 - Control</a></li>
                @endif

                @if(Helper::validateAdministrator(session()->get('usuario')->Level) || Helper::permissionPerUser(session()->get('usuario')->Nombre, "Gift"))
                  <li><a href="{{ url('stock_insertar_codigo_g') }}"><i class="fa fa-gift fa-fw" aria-hidden="true"></i> P2</a></li>
                @endif

                @if(Helper::validateAdministrator(session()->get('usuario')->Level) || Helper::permissionPerUser(session()->get('usuario')->Nombre, "Gift"))
                  <li><a href="{{ url('stock_insertar_codigo_p3') }}"><i class="fa fa-gift fa-fw" aria-hidden="true"></i> P3</a></li>
                @endif
              </ul>
            </li>

            <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i> Vtas<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ url('sales/list') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>
                <li><a href="{{ url('sales/lista_cobro') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar Cobros</a></li>
        <!--
                <li><a href="https://dixgamer.com/db/ventas_web_sin_oii.php"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Sin order_item_id</a></li>

                <li class="divider" role="separator"></li>
                <!--  Solo Admin   -->
                <!--
                <li><a href="https://dixgamer.com/db/ventas_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <!--  Termina Admin   -->
                <!--

                <li><a href="https://dixgamer.com/db/ventas_buscador.php"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Buscar</a></li>

                -->
              </ul>
            </li>
            <li><a href="{{ url('web/sales') }}"><i class="text-success fa fa-check-circle fa-fw" aria-hidden="true"></i> Ped Cobrados</a></li>

            @if(Helper::validateAdministrator(session()->get('usuario')->Level))
              <li class="dropdown">
                <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-bank fa-fw" aria-hidden="true"></i> Gtos<span class="caret"></span></a>
                <ul class="dropdown-menu">

                  <li><a href="{{ url('/gastos') }}"><i class="fa fa-list fa-fw" aria-hidden="true"></i> Listar</a></li>

                  <li class="divider" role="separator"></li>
                  <li><a href="{{ url('gastos/create') }}"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                </ul>
              </li>
            @endif
            </ul>

          <ul class="nav navbar-nav navbar-right">

            <li><a href="javascript:void(0)"><i class="glyphicon glyphicon-user text-success" aria-hidden="true"></i> {{ session()->get('usuario')->Nombre }}</a></li>
          
          @if(Helper::lessAdministrator(session()->get('usuario')->Level))
            <li><a href="{{ url('horario') }}"><i class="fa fa-clock-o fa-fw" aria-hidden="true"></i> Horas</a></li>
          @endif

          @if (Helper::validateAdminAnalyst(session()->get('usuario')->Level))

           <li class="dropdown">
              <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> Config<span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="{{ url('balance') }}"><i class="fa fa-line-chart fa-fw" aria-hidden="true"></i> Balance</a></li>
                <li><a href="{{ url('balance_productos') }}"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i> Por productos</a></li>
                <li><a href="https://dixgamer.com/db/_control/balance_productos_dias.php"><i class="fa fa-bar-chart fa-fw" aria-hidden="true"></i> Por Dias</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="https://dixgamer.com/db/publicaciones_generar_descripcion.php"><i class="fa fa-wpforms fa-fw" aria-hidden="true"></i> Generar Descrip</a></li>
                <li><a href="https://dixgamer.com/db/publicaciones_detalles.php"><i class="fa fa-wpforms fa-fw" aria-hidden="true"></i> Publicaciones</a></li>
                <li><a href="{{ url('publicaciones_secundarias_ml') }}"><i class="fa fa-wpforms fa-fw" aria-hidden="true"></i> Secundarias</a></li>
                <li><a href="https://dixgamer.com/db/publicaciones_insertar.php"><i class="fa fa-plus fa-fw" aria-hidden="true"></i> Agregar</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="{{ url('adwords') }}"><i class="fa fa-google fa-fw" aria-hidden="true"></i> Adwords</a></li>
                <li class="divider" role="separator"></li>
                <li><a href="{{url('config/general')}}"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> General</a></li>
                <li><a href="{{url('usuario')}}"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> Nuevo usuario</a></li>
                <li><a href="{{url('usuario/list')}}"><i class="fa fa-users fa-fw" aria-hidden="true"></i> Listar usuarios</a></li>
              </ul>
            </li>
          

            <li class="dropdown">
                <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="fa fa-database fa-fw" aria-hidden="true"></i> Control<span class="caret"></span></a>
                <ul class="dropdown-menu">
                  <li><a href="{{url('evolucion')}}"><i class="fa fa-line-chart fa-fw" aria-hidden="true"></i> Evolución</a></li>
                  <li><a href="{{url('horarios')}}"><i class="fa fa-clock-o fa-fw" aria-hidden="true"></i> Horas</a></li>
                  <li class="divider" role="separator"></li>
                  <li><a href="{{ url('carga_gc') }}"><i class="fa fa-barcode fa-fw" aria-hidden="true"></i> Carga GC</a></li>
                  <li><a href="https://dixgamer.com/db/control_precios_web.php"><i class="fa fa-money fa-fw" aria-hidden="true"></i> Precios</a></li>
                  <li class="divider" role="separator"></li>
                  <li><a href="{{ url('control_mp') }}"><i class="fa fa-credit-card-alt fa-fw" aria-hidden="true"></i> MP</a></li>
                  <li><a href="{{ url('control_mp','v2') }}"><i class="fa fa-credit-card-alt fa-fw" aria-hidden="true"></i> MP2</a></li>
                  <li><a href="https://dixgamer.com/db/modificaciones_control.php"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Modif</a></li>
                  <li><a href="{{ url('control_ventas') }}"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i> Ventas</a></li>
                  <li><a href="{{ url('control_ventas_bancos') }}"><i class="fa fa-shopping-bag fa-fw" aria-hidden="true"></i> Ventas por Bancos</a></li>
               </ul>
            </li>
          @endif
      <!--  Termina Admin   -->
            <li><a href="{{ url('logout') }}"><i class="fa fa-sign-out fa-fw"></i> Salir</a></li>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </nav><!-- /.navbar -->


    @if(Session::has('success'))
      <div class="container text-center">
        <div class="alert alert-success" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">{{ Session::get('success')->title }}:</span>
            {{ Session::get('success')->body }}
        </div>
      </div>
    @elseif(Session::has('warning'))
      <div class="container text-center">
        <div class="alert alert-warning" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <span class="sr-only">{{ Session::get('warning')->title }}:</span>
            {{ Session::get('warning')->body }}
        </div>
      </div>
    @endif

    
@yield('container')






  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="{{ asset('assets/js/docs.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/script2.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.11.2/js/bootstrap-select.min.js"></script>
  <script src="{{ asset('js/typeahead.bundle.js') }}"></script>
  <script type="text/javascript" src="{{asset('js/tinymce/tinymce.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/tinymce/skins/custom/jquery.tinymce.min.js')}}"></script> 
  <script src="{{ asset('js/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.full.js"></script>

  <script>new Clipboard('.btn-copiador');</script>

  <!-- Activar popover -->

  <script>
    $(document).ready(function(){
      if ($('[data-toggle="popover"]').length>0) {
        $('[data-toggle="popover"]').popover();
      }


      @if(Session::has('success'))
          $('#modal_success').modal('toggle');
      @endif
    });
  </script>

  @yield('scripts')

</body>
</html>
