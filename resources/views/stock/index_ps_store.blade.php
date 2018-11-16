@extends('layouts.master-layouts')

@section('title', 'Listado de Links a PS Store')

@section('container')


<div class="container">
	<h1>Listado de Links a PS Store</h1>
    @if (count($errors) > 0)
          <div class="alert alert-danger text-center">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
    @endif
    <div class="table-responsive">
      <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">
        <tr>

          <th width="50">Cover</th>
          <th>Titulo</th>
          <th>Links PS</th>

          @if(Helper::validateAdministrator(session()->get('usuario')->Level))
            <th>Agregar Link</th>
            <th>Post ID</th>
      		@endif
        </tr>

        @if(count($stocks) > 0)
          @foreach($stocks as $stock)
            <tr id="{{ $stock->ID }}">

              <td>
                <img
                  class="img-rounded"
                  width="50" id="image-swap"
                  src="{{asset('img/productos')}}/{{ $stock->consola }}/{{ $stock->titulo }}.jpg"
                  alt="" />
              </td>

              <td
                title="{{ $stock->titulo }} ({{ $stock->consola }})">
                  {{ str_replace("-", " ", $stock->titulo) }}

                  @if(strpos($stock->slot,'primario') !== false)
                    1ro
                  @endif
              </td>

              <td title="{{ $stock->link_ps }}">
                @if(($stock->link_ps) && $stock->link_ps !== "")
                  <?php $array = (explode(',', $stock->link_ps, 10)); ?>
                  @if(count($array) > 0)

                    @foreach($array as $valor)
                      <a title='ver en la tienda de PS' target='_blank' href='{{ $valor }}'>
                        <i aria-hidden='true' class='fa fa-external-link'></i>
                        Tienda PS
                      </a>
                      <br />
                    @endforeach

                  @endif

                @else
                  <span class="label label-danger">NO HAY LINK</span>
                @endif
              </td>

              @if(Helper::validateAdministrator(session()->get('usuario')->Level))

                <td title="Insertar Link">

                  <form method="post" name="form1" action="">
                    {{ csrf_field() }}
                    <input type="text" name="id" value="{{ $stock->ID }}" hidden>

                    <div class="input-group form-group">
                      <input class="form-control" type="text" name="link" value="" autocomplete="off">
                    </div>

                    <button class="btn btn-primary btn-xs" type="submit">Crear</button>

                  </form>

                </td>

                <td>
                  {{ $stock->ID }}
                </td>

              @endif
           </tr>
          @endforeach
        @else
          <td colspan = '10' class="text-center">No se encontraron datos</td>
        @endif

      </table>
      <div>
        <div class="col-md-12">
          {{ $stocks->render() }}
        </div>
      </div>
    </div>
</div><!--/.container-->



@endsection
