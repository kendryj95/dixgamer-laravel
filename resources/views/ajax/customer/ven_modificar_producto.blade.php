<div class="container">
  <h1>Inicio</h1>
  <div class="row text-center">
    <a href="#pri" class="btn btn-lg btn-primary">Ir a Primarios</a>
    <a href="#secu" name="secu" class="btn btn-lg btn-info">Ir a Secundarios</a>
    <a href="#ps3" class="btn btn-lg btn-default" style="background-color:#000; color:#FFF;">Ir a PS3</a>
    <a href="#reset" class="btn btn-lg btn-warning">Ir a Resetear</a>
  </div>

  <!-- STOCK -->
  @component('components.modificar_producto.stock')
    @slot('stocks', $stocks)
    @slot('title', 'Stock')
    @slot('id_ventas', $id_ventas)
  @endcomponent

  <!-- PS4 PRIMARIO  -->
  @component('components.modificar_producto.card_ps')
    @slot('datas', $ps4_primary_stocks)
    @slot('title', 'PS4 Primario')
    @slot('sectionId', 'pri')
    @slot('slot', 'Primario')
    @slot('id_ventas', $id_ventas)
  @endcomponent

  <!-- PS4 Secundario  -->
  @component('components.modificar_producto.card_ps')
    @slot('datas', $ps4_secundary_stocks)
    @slot('title', 'PS4 Secundario')
    @slot('sectionId', 'secu')
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