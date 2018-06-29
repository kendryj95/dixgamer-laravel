<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <meta name="description" content="tienda de productos digitales">
  <meta name="author" content="dixgamer.com argentina">
  <link rel="icon" href="favicon.ico">
  <meta name="_token" content="{{ csrf_token() }}">
  <title>@yield('title','Dixgamer panel')</title>

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
  <style media="screen">
    .form-control{
      width: 100% !important;
    }
  </style>
</head>

<body>

  @yield('container')

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
  <script src="{{ asset('assets/js/docs.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('js/script.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>

  <script>new Clipboard('.btn-copiador');</script>

  <!-- Activar popover -->

  <script>
  	$(document).ready(function(){
  		$('[data-toggle="popover"]').popover();

  	});
  </script>

  @yield('scripts')

</body>
</html>
