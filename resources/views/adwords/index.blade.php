@extends('layouts.master-layouts')

@section('title', 'Adwords - Aramdo de KW')

@section('container')

    <div class="container">
		<h1>Adwords - Aramdo de KW</h1>
	    <!-- InstanceBeginEditable name="body" -->
		<table class="table table-striped" border="0" cellpadding="0" cellspacing="5">
	      <tr>
	        <th width="50">Cover</th>
	        <th title="Anuncio">Anuncio</th>
	        <th title="Titulo">Titulo</th>
	        <th title="KW">KW</th>
	      </tr>

	      @foreach ($adwords as $adword)
	      @php 
	      	$linea = $adword->titulo; 
	      	$titulin = ucwords(preg_replace('/([-])/'," ",$linea));
	      @endphp
	      <tr>
	        <td><img class="img-rounded" width="50" id="image-swap" src="{{ asset('img/productos') }}/{{ $adword->consola }}/{{ $adword->titulo }}.jpg" alt="" /></td>
	        <td title="{{ $adword->titulo }} ({{ ucwords($adword->consola) }})">{{ $titulin }}</td>
	        <td>{{ $titulin }} ({{ ucwords($adword->consola) }})</td>
	        <td>+comprar +{{ $titulin }}<br />+{{ $titulin }} +digital</td>
	        
	      </tr>
	      @endforeach   
	    </table>
	     <!--/row-->
	     <!-- InstanceEndEditable -->
    </div><!--/.container-->

@endsection