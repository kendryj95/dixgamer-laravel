<div class="container">
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
  @component('components.modificar_producto.stock')
    @slot('stocks', $stocks)
    @slot('title', 'Stock')
    @slot('id_ventas', $id_ventas)
  @endcomponent

  <!-- PS5 PRIMARIO  -->
  @component('components.modificar_producto.card_ps')
    @slot('datas', $ps5_primary_stocks)
    @slot('title', 'PS5 Primario')
    @slot('sectionId', 'ps5_pri')
    @slot('slot', 'Primario')
    @slot('id_ventas', $id_ventas)
  @endcomponent

  <!-- PS5 Secundario  -->
  @component('components.modificar_producto.card_ps')
    @slot('datas', $ps5_secundary_stocks)
    @slot('title', 'PS5 Secundario')
    @slot('sectionId', 'ps5_secu')
    @slot('slot', 'Secundario')
    @slot('id_ventas', $id_ventas)
  @endcomponent

<!-- PS4 PRIMARIO  -->
  @component('components.modificar_producto.card_ps')
    @slot('datas', $ps4_primary_stocks)
    @slot('title', 'PS4 Primario')
    @slot('sectionId', 'ps4_pri')
    @slot('slot', 'Primario')
    @slot('id_ventas', $id_ventas)
  @endcomponent

  <!-- PS4 Secundario  -->
  @component('components.modificar_producto.card_ps')
    @slot('datas', $ps4_secundary_stocks)
    @slot('title', 'PS4 Secundario')
    @slot('sectionId', 'ps4_secu')
    @slot('slot', 'Secundario')
    @slot('id_ventas', $id_ventas)
  @endcomponent

  <!-- PS3  -->
  @component('components.modificar_producto.card_ps')
    @slot('datas', $ps3_stocks)
    @slot('title', 'PS3')
    @slot('sectionId', 'ps3')
    @slot('slot', 'Primario')
    @slot('id_ventas', $id_ventas)
  @endcomponent



  </div><!--/.container-->