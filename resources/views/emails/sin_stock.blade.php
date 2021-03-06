
@if ($tipo == "Reasignar")
    El cliente {{$cliente->nombre}} {{$cliente->apellido}} ({{$cliente->email}}) está esperando su <b>compra reciclada</b>.
@else
    El cliente {{$cliente->nombre}} {{$cliente->apellido}} ({{$cliente->email}}) está esperando su <b>nueva compra</b>.
@endif
<br><br>

<span style="color: red">No olvides buscar stock disponible en la</span> @if ($consola == "ps4") <a href="{{url('sales/recupero')}}?column=titulo&word={{$titulo}}&enviar=Buscar">lista de recuperos</a> @elseif($consola == "ps3") <a href="{{url('home')}}#reset">lista de reseteos</a> @endif
<br><br>

@if ($tipo == "Reasignar")
    <a href="{{route('producto.modificar',[$consola,$titulo,$slot,$venta->ID])}}?previousUrl=email{{ time().uniqid() }}">{{route('producto.modificar',[$consola,$titulo,$slot,$venta->ID])}}</a>
@else
    <a href="{{url('salesInsertWeb',[$order,$titulo,$consola,$slot])}}?previousUrl=email{{ time().uniqid() }}"> {{url('salesInsertWeb',[$order,$titulo,$consola,$slot])}}</a>
@endif