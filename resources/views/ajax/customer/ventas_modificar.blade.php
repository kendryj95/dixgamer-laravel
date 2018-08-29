@php

$data_set = array();

foreach ($data as $value) {
  $data_set[] = $value->nombre;
}

@endphp
<div class="container">
  <h1 style="color:#000">Modificar Venta</h1>
  
  <div class="row">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6">


        @if($opt == 1)

          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">
            {{csrf_field()}}

            <input type="hidden" name="opt" value="1">

            <div style="margin-bottom:10px;">
              <input id="clientes" type="text" class="form-control typeahead" autocomplete="off" spellcheck="false" value="[ {{$clientes->clientes_id}} ] {{ $clientes->nombre }} {{ $clientes->apellido }} - {{ $clientes->email }}">
            </div>


             <input type="hidden" name="clientes_id" id="clientes_id" value="{{$clientes->clientes_id}}">
            <input type="hidden" name="ID" value="{{ $clientes->ID }}">
            <input type="hidden" id="data_set" value="{{ json_encode($data_set) }}">

            <button class="btn btn-primary" type="submit">Modificar</button>
            <input type="reset" class="btn btn-secondary">

          </form>

        @elseif ($opt == 2)

          @php $colorventa = ''; @endphp

          @if ($clientes->medio_venta == 'MercadoLibre') @php $colorventa='warning'; @endphp
          @elseif ($clientes->medio_venta == 'Web') @php $colorventa='info'; @endphp
          @elseif ($clientes->medio_venta == 'Mail') @php $colorventa='danger'; @endphp
          @endif
          
          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">

           {{csrf_field()}}

           <input type="hidden" name="opt" value="2">

            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
                        <select name="medio_venta" class="selectpicker form-control">
               <option value="{{$clientes->medio_venta}}" selected="selected" data-content="<span class='label label-{{$colorventa}}'>{{$clientes->medio_venta}}</span> - <span class='label label-success'>Actual</span>">{{$clientes->medio_venta}} - Actual</option>
               <option value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
                <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
            </select>                
            </div>

            <input type="hidden" name="ID" value="{{ $clientes->ID }}">

            <button class="btn btn-primary" type="submit">Modificar</button>
           <input type="reset" class="btn btn-secondary">
          </form>

        @elseif ($opt == 3)

          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">

            {{csrf_field()}}

            <input type="hidden" name="opt" value="3">

            @php $colorventa = ''; @endphp

            @if ($clientes->medio_venta == 'MercadoLibre') @php $colorventa='warning'; @endphp
            @elseif ($clientes->medio_venta == 'Web') @php $colorventa='info'; @endphp
            @elseif ($clientes->medio_venta == 'Mail') @php $colorventa='danger'; @endphp
            @endif

            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
            <select name="medio_venta" class="selectpicker form-control" onchange="orders(this.value)">
               <option value="{{$clientes->medio_venta}}" selected="selected" data-content="<span class='label label-{{$colorventa}}'>{{$clientes->medio_venta}}</span> - <span class='label label-success'>Actual</span>">{{$clientes->medio_venta}} - Actual</option>
               <option value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
                <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
            </select>                
            </div>

            @if ($clientes->medio_venta == 'MercadoLibre')

              <div class="input-group form-group oiml">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_ml }}" class="form-control" type="text" name="order_id_ml" placeholder="order_id_ml">
                <span class="input-group-addon"><em class="text-muted">order id ml</em></span>
              </div>

              <div class="input-group form-group oii">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" name="order_item_id" placeholder="order_item_id">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group oiw">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" name="order_id_web" placeholder="order_id_web">
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">

              <button class="btn btn-primary" type="submit">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @elseif ($clientes->medio_venta == 'Web')

              <div class="input-group form-group oiml" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_ml }}" class="form-control" type="text" name="order_id_ml" placeholder="order_id_ml">
                <span class="input-group-addon"><em class="text-muted">order id ml</em></span>
              </div>

              <div class="input-group form-group oii">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" name="order_item_id" placeholder="order_item_id">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group oiw">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" name="order_id_web" placeholder="order_id_web">
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">

              <button class="btn btn-primary" type="submit">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @elseif ($clientes->medio_venta == 'Mail')

              <div class="input-group form-group oiml" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_ml }}" class="form-control" type="text" name="order_id_ml" placeholder="order_id_ml">
                <span class="input-group-addon"><em class="text-muted">order id ml</em></span>
              </div>

              <div class="input-group form-group oii" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" name="order_item_id" placeholder="order_item_id">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group oiw" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" name="order_id_web" placeholder="order_id_web">
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">

              <button class="btn btn-primary" type="submit">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @endif

          </form>

        @elseif ($opt == 4)

          <form action="{{ url('customer_ventas_modificar_store') }}" method="post">
            
            {{csrf_field()}}

            <input type="hidden" name="opt" value="4">

            <em class="text-muted">{{ $clientes->Notas }}</em>
            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
              <input value="{{ $clientes->Notas }}" class="form-control" type="text" name="Notas" placeholder="Notas de la venta">
            </div>

            <input type="hidden" name="ID" value="{{ $clientes->ID }}">

            <button class="btn btn-primary" type="submit">Modificar</button>
            <input type="reset" class="btn btn-secondary">


          </form>

        @endif
        
      </div>
      <div class="col-sm-3">
      </div>
  </div>
  <br /><br />
  <!--/row-->
</div><!--/.container-->

<script>
  var cliente;
  $(document).ready(function() {

    @if($opt == 1)

    var cars = JSON.parse($('#data_set').val());

    // Constructing the suggestion engine
    var cars = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: cars
    });

    // Initializing the typeahead
    $('.typeahead').typeahead({
        hint: true,
        highlight: true, /* Enable substring highlighting */
        minLength: 2 /* Specify minimum characters required for showing result */
        
    },
    {
        name: 'cars',
        source: cars
    });

      /*.on('typeahead:render', (e,firstOption) => {
                        if (!!firstOption) {
                            enterSelection = firstOption
                        } else {
                            enterSelection = undefined;
                        }
                    }).on('keypress', (e) => {
                        if (e.which == 13 && enterSelection) {
                            $('#typeahead').typeahead('val', enterSelection.value);
                            (this.onDataSelect || _.noop)({ selection: enterSelection });
                            $('#typeahead').typeahead('close');
                        }
                    });*/

            $("#clientes").blur(function(){
              if(this.value==""){
               this.value = arr[0];   
              }
           });

          window.setInterval(function() {

            cliente = $('#clientes').val();
            var String = cliente.substring(cliente.lastIndexOf('[ ')+1,cliente.lastIndexOf(' ]'));
              // document.getElementById("clientes_id").value = String;
              $('#clientes_id').val((String).trim());
            },500);

    @endif

    // To style only <select>s with the selectpicker class
    $('.selectpicker').selectpicker();
  });

  function orders(value)
  {
    if (value == 'Mail') {
      $('.oiml').hide();
      $('.oii').hide();
      $('.oiw').hide();
    } else if(value == 'MercadoLibre') {
      $('.oiml').show();
      $('.oii').show();
      $('.oiw').show();
    } else {
      $('.oiml').hide();
      $('.oii').show();
      $('.oiw').show();
    }
  }
</script>