@extends('layouts.master-layouts')

@section('title', 'Catalogo de Productos')

@section('container')


<div class="container">
	<h1>Catalogo de Productos</h1>
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
          <th>Consola</th>
          <th>Peso</th>
          <th>Idioma</th>
          <th>Precio</th>
        </tr>
        @if(count($stocks) > 0)
          @foreach($stocks as $key => $stock)
            <tr>

              <td id="{{ $key }}">
                <img
                class="img-rounded"
                width="50"
                id="image-swap"
                src="{{asset('img/productos')}}/{{$stock->consola}}/{{$stock->titulo}}.jpg"
                alt="" />
              </td>

              <td title="{{$stock->consola}} ({{$stock->titulo}})">
                <p style="font-weight:bold;">
                  {{ str_replace("-", " ", $stock->titulo) }}

									@if(strpos($stock->slot, 'primario') !== false)
										<span class="label label-primary" style="opacity:0.4">1°</span>
									@elseif(strpos($stock->slot, 'secundario') == false)
										<span class="label label-danger" style="opacity:0.4">2°</span>
									@endif
                </p>

                <p>
                  <div style="position:absolute; top:0; left:-500px;">

              			<textarea id="link-web-{{$key}}" type="text" rows="1" cols="1">
											{{ url('/post_type=product&p='.$stock->ID) }}
                    </textarea>

              			<textarea id="link-ml-{{$key}}" type="text" rows="1" cols="1">
                      {{ $stock->ml_url }}
                    </textarea>
            			</div>

									@if($stock->slot != 'secundario')
										<a href="#{{ $key-1 }}>"
											class="btn-copiador btn-xs btn-info label"
											data-clipboard-target="#link-web-{{ $key }}">
											Link Web
										</a>
									@endif

                  <a
                    href="#{{ $key-1 }}"
                    class="btn-copiador btn-xs btn-warning label"
                    data-clipboard-target="#link-ml-{{ $key }}">
                    Link ML
                  </a>

                </p>
              </td>

              <td>{{$stock->consola}}</td>

              <td>
								@if($stock->peso > 0.00):
										{{$stock->peso}} GB
								@endif
							</td>

              <td>{{$stock->idioma}}</td>

              <td>${{$stock->precio}}</td>
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
