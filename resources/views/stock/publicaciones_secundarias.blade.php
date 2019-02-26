@extends('layouts.master-layouts')

@section('title', 'Publicaciones para ML')

@section('container')

<div class="container">
  <h1>Publicaciones para ML</h1>
  
  @if(count($publicaciones) > 0)

    @foreach($publicaciones as $publicacion)

      @if(getimagesize(asset("img/productos/$publicacion->consola/$publicacion->titulo.jpg")) !== false)
        
        <div class="col-xs-6 col-sm-1b">

          <div class="thumbnail">
            
              <div style="position:relative; overflow:hidden; padding-bottom:100%;">
                <span style="position:absolute; z-index:100; bottom: 0px; background-color: rgba(0, 0, 0, 0.8); color:#FFF; padding:3px; font-size:0.9em;">{{ str_replace('-', ' ', $publicacion->titulo) }}</span>
                <img class="img img-responsive full-width" src="{{ asset("img/productos/$publicacion->consola/$publicacion->titulo.jpg") }}" border="0" alt="{{ $publicacion->titulo }} - {{ $publicacion->consola }}" style="border-radius:5px; position:absolute;">
                <span class="badge badge-danger pull-right" style="position: relative; top: 8px; left: -8px;">{{ $publicacion->libre }}</span><br />
              </div>
              <div class="caption text-center">
                <span class="badge badge-success">
                  @if($publicacion->max_price < $publicacion->min_price)
                      {{ $publicacion->max_price }}
                  @else
                      {{ $publicacion->min_price }}
                  @endif
                </span>
              </div>

          </div>

        </div>

      @endif

    @endforeach

  @endif

</div>

@stop