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

@if (count($errors) > 0)
      <div class="alert alert-danger text-center">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
@endif
<div class="container-fluid">
  <h1>Inicio</h1>
  <div class="row text-center">
    <a href="#ps5_pri" class="btn btn-lg btn-primary">Ir a PS5 Primarios</a>
    <a href="#ps5_secu" class="btn btn-lg btn-info">Ir a PS5 Secundarios</a>
    <a href="#ps4_pri" class="btn btn-lg btn-primary">Ir a PS4 Primarios</a>
    <a href="#ps4_secu" class="btn btn-lg btn-info">Ir a PS4 Secundarios</a>
    <a href="#ps3" class="btn btn-lg btn-default" style="background-color:#000; color:#FFF;">Ir a PS3</a>
    <a href="#reset" class="btn btn-lg btn-warning">Ir a Resetear</a>
  </div>

  <!-- STOCK -->
  @component('components.home.stock')
    @slot('stocks', $stocks)
    @slot('title', 'Stock')
    @slot('OII', $OII)
  @endcomponent

  <!-- PS5 PRIMARIO  -->
  @component('components.home.card_ps')
    @slot('datas', $ps5_primary_stocks)
    @slot('title', 'PS5 Primario')
    @slot('sectionId', 'ps5_pri')
    @slot('slot', 'Primario')
    @slot('OII', $OII)
  @endcomponent

  <!-- PS5 Secundario  -->
  @component('components.home.card_ps')
    @slot('datas', $ps5_secundary_stocks)
    @slot('title', 'PS5 Secundario')
    @slot('sectionId', 'ps5_secu')
    @slot('slot', 'Secundario')
    @slot('OII', $OII)
  @endcomponent

  <!-- PS4 PRIMARIO  -->
  @component('components.home.card_ps')
    @slot('datas', $ps4_primary_stocks)
    @slot('title', 'PS4 Primario')
    @slot('sectionId', 'ps4_pri')
    @slot('slot', 'Primario')
    @slot('OII', $OII)
  @endcomponent

  <!-- PS4 Secundario  -->
  @component('components.home.card_ps')
    @slot('datas', $ps4_secundary_stocks)
    @slot('title', 'PS4 Secundario')
    @slot('sectionId', 'ps4_secu')
    @slot('slot', 'Secundario')
    @slot('OII', $OII)
  @endcomponent

  <!-- PS3  -->
  @component('components.home.card_ps')
    @slot('datas', $ps3_stocks)
    @slot('title', 'PS3')
    @slot('sectionId', 'ps3')
    @slot('slot', 'Primario')
    @slot('OII', $OII)
  @endcomponent

  <!-- PS3 Resetear -->
  @component('components.home.ps3_reset')
    @slot('datas', $ps3_reset_stocks)
    @slot('title', 'PS3 <span class="label label-warning">resetear</span>')
    @slot('sectionId', 'reset')
    @slot('OII', $OII)
  @endcomponent



  </div><!--/.container-->