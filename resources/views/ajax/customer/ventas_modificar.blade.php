@php

$data_set = array();

foreach ($data as $value) {
  $data_set[] = $value->nombre;
}

@endphp
<div class="container">
  {{-- <h1 style="color:#000">Modificar Venta</h1> --}}
  <div class="alert alert-danger" style="display:none" id="alert"></div>
  <div class="row">
      <div class="col-sm-3">
      </div>
      <div class="col-sm-6">


        @if($opt == 1)

        <h1 style="color:#000">Modificar Cliente</h1>

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
            <input type="hidden" name="clientes_id" value="{{ $clientes->clientes_id }}">

            <button class="btn btn-primary" type="submit">Modificar</button>
           <input type="reset" class="btn btn-secondary">
          </form>

        @elseif ($opt == 3)

        <h1 style="color:#000">Modificar Venta</h1>

          <form action="{{ url('customer_ventas_modificar_store') }}" method="post" id="form_modificar">

            {{csrf_field()}}

            <input type="hidden" name="opt" value="3">

            @if(\Helper::validateAdministrator(session()->get('usuario')->Level))

            <div class="input-group form-group">
              <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
              <input value="{{ date('Y-m-d',strtotime($clientes->Day)) }}" class="form-control" type="date" id="Day" name="Day" placeholder="Fecha Venta">
              <input type="hidden" name="fecha_old" value="{{$clientes->Day}}">
              <span class="input-group-addon"><em class="text-muted">Fecha Venta</em></span>
            </div>

            @endif

            @php $colorventa = ''; @endphp

            @if ($clientes->medio_venta == 'MercadoLibre') @php $colorventa='warning'; @endphp
            @elseif ($clientes->medio_venta == 'Web') @php $colorventa='info'; @endphp
            @elseif ($clientes->medio_venta == 'Mail') @php $colorventa='danger'; @endphp
            @endif

            <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-shopping-bag fa-fw"></i></span> 
            <select name="medio_venta" id="medio_venta" class="selectpicker form-control" onchange="orders(this.value)">
               <option value="{{$clientes->medio_venta}}" selected="selected" data-content="<span class='label label-{{$colorventa}}'>{{$clientes->medio_venta}}</span> - <span class='label label-success'>Actual</span>">{{$clientes->medio_venta}} - Actual</option>
               <option value="MercadoLibre" data-content="<span class='label label-warning'>MercadoLibre</span>">MercadoLibre</option>
                <option value="Mail" data-content="<span class='label label-danger'>Mail</span>">Mail</option>
                <option value="Web" data-content="<span class='label label-info'>Web</span>">Web</option>
            </select>                
            </div>

            @if ($clientes->medio_venta == 'MercadoLibre')

              <div class="input-group form-group oiml">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_ml }}" class="form-control" type="text" id="oiml" name="order_id_ml" placeholder="order_id_ml">
                <span class="input-group-addon"><em class="text-muted">order id ml</em></span>
              </div>

              <div class="input-group form-group oii">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" id="oii" name="order_item_id" placeholder="order_item_id" onblur="verificarOii(this.value)">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group oiw">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" id="oiw" name="order_id_web" placeholder="order_id_web" readonly>
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <div class="oin" style="">
                <h6 id="oin" style="color: #000; text-align: left; margin: 0"></h6>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">
              <input type="hidden" name="clientes_id" value="{{ $clientes->clientes_id }}">

              <button class="btn btn-primary" id="submiter" type="button">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @elseif ($clientes->medio_venta == 'Web')

              <div class="input-group form-group oiml" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_ml }}" class="form-control" type="text" id="oiml" name="order_id_ml" placeholder="order_id_ml">
                <span class="input-group-addon"><em class="text-muted">order id ml</em></span>
              </div>

              <div class="input-group form-group oii">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" class="form-control" type="text" id="oii" name="order_item_id" placeholder="order_item_id" onblur="verificarOii(this.value)">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group oiw">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" id="oiw" name="order_id_web" placeholder="order_id_web" readonly>
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <div class="oin" style="">
                <h6 id="oin" style="color: #000; text-align: left; margin: 0"></h6>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">
              <input type="hidden" name="clientes_id" value="{{ $clientes->clientes_id }}">

              <button class="btn btn-primary" id="submiter" type="button">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @elseif ($clientes->medio_venta == 'Mail')

              <div class="input-group form-group oiml" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_ml }}" class="form-control" type="text" id="oiml" name="order_id_ml" placeholder="order_id_ml">
                <span class="input-group-addon"><em class="text-muted">order id ml</em></span>
              </div>

              <div class="input-group form-group oii" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_item_id }}" onblur="verificarOii(this.value)" class="form-control" type="text" id="oii" name="order_item_id" placeholder="order_item_id">
                <span class="input-group-addon"><em class="text-muted">order item id</em></span>
              </div>

              <div class="input-group form-group oiw" style="display: none">
                <span class="input-group-addon"><i class="fa fa-shopping-cart fa-fw"></i></span>
                <input value="{{ $clientes->order_id_web }}" class="form-control" type="text" id="oiw" name="order_id_web" placeholder="order_id_web" readonly>
                <span class="input-group-addon"><em class="text-muted">order id web</em></span>
              </div>

              <div class="oin" style="display: none">
                <h6 id="oin" style="color: #000; text-align: left; margin: 0"></h6>
              </div>

              <input type="hidden" name="ID" value="{{ $clientes->ID }}">
              <input type="hidden" name="clientes_id" value="{{ $clientes->clientes_id }}">

              <button class="btn btn-primary" id="submiter" type="button">Modificar</button>
              <input type="reset" class="btn btn-secondary">

            @endif

          </form>

        @elseif ($opt == 4)

        {{-- <div class="container"> --}}
            <h1 style="color:#000">Agregar Nota - Venta #{{$clientes->ID}}</h1>
            <div class="row">
              <div class="col-lg-12">
                <form action="{{ url('customer_ventas_modificar_store') }}" method="post" id="form_addNote">
                  
                  {{csrf_field()}}
                
                  <input type="hidden" name="opt" value="4">
                
                  <div class="input-group form-group">
                    <span class="input-group-addon"><i class="fa fa-comment fa-fw"></i></span>
                    <textarea class="form-control" rows="4" name="Notas" id="Notas" style="font-size: 22px;"></textarea>
                
                  </div>
                  <input type="hidden" name="ID" value="{{ $clientes->ID }}">
                  <input type="hidden" name="clientes_id" value="{{ $clientes->clientes_id }}">
                
                  <button class="btn btn-warning btn-block" id="addNote" type="button">Insertar</button>
                
                
                </form>
              </div>
            </div>
        {{-- </div> --}}

        @elseif ($opt == 5)
        <h1 style="color:#000">Modificar Stock</h1>

        <form action="{{ url('customer_ventas_modificar_store') }}" method="post" id="form_mod_manual">

          {{csrf_field()}}
          
          <input type="hidden" name="opt" value="5">
          

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
            <input type="text" name="stock" id="stock" class="form-control" value="{{ $clientes->stock_id }}">
            <span class="input-group-addon">Nro. Stock (actual: {{ $clientes->stock_id }})</span>
          </div>

          <div class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-bookmark"></i></span>
            <select name="slot" id="slot" class="form-control">
              <option value="">Seleccione Slot</option>
              <option value="No" @if($clientes->slot == 'No') selected @endif>No</option>
              <option value="Primario" @if($clientes->slot == 'Primario') selected @endif>Primario</option>
              <option value="Secundario" @if($clientes->slot == 'Secundario') selected @endif>Secundario</option>
            </select>
            <span class="input-group-addon">Slot (actual: {{ $clientes->slot }})</span>
          </div>
          
          <input type="hidden" name="ID" value="{{ $clientes->ID }}">
          <input type="hidden" name="clientes_id" value="{{ $clientes->clientes_id }}">

          <button class="btn btn-primary btn-block" id="btnModManual" type="button">Modificar</button>
        </form>

        @endif

        <input type="hidden" id="clientes_id2" value="{{ $clientes->clientes_id }}">
        
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

    $('#addNote').on('click', function(){
      $(this).prop('disabled', true);

      $('#form_addNote').submit();
    });

    $('#btnModManual').on('click', function(){

      if ($('#slot').val() != "" && $('#stock').val() != "") {
        $(this).prop('disabled', true);

        $('#form_mod_manual').submit();
      }


    });

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

    $('#submiter').on('click', function(){

      var medio_venta = $('#medio_venta').val();
      $('#alert').fadeOut();

      if (medio_venta == 'Web') {
        if ($('#oii').val() != "" && $('#oiw').val() != "") {
          $('#form_modificar').submit();
        } else {
          let html = '<p>Has dejado vacío campos obligatorios.</p>';
          $('#alert').html(html).fadeIn();
        }
      } else if (medio_venta == 'MercadoLibre') {
        if ($('#oiml').val() != "") {
          $('#form_modificar').submit();
        } else {
          let html = '<p>Has dejado vacío campos obligatorios.</p>';
          $('#alert').html(html).fadeIn();
        }
      } else {
        $('#form_modificar').submit();
      }
    });

    // To style only <select>s with the selectpicker class
    $('.selectpicker').selectpicker();
  });

  function orders(value)
  {
    if (value == 'Mail') {
      $('.oiml').hide();
      $('.oii').hide();
      $('.oiw').hide();
      $('.oin').hide();
    } else if(value == 'MercadoLibre') {
      $('.oiml').show();
      $('.oii').show();
      $('.oiw').show();
      $('.oin').show();
    } else {
      $('.oiml').hide();
      $('.oii').show();
      $('.oiw').show();
      $('.oin').show();
    }
  }

  function verificarOii(oii) {
      let clientes_id = $('#clientes_id2').val();
      $('#alert').hide();

      if (oii != "") {
          $.ajax({
              url: '{{ url('verificarOii') }}/'+oii+'/'+clientes_id,
              type: 'GET',
              dataType: 'json',
              success: function(response){
                  switch(response.status) {
                      case 1:
                          var html2 = `<p>El orden_item_id pertenece al cliente <a href="{{ url('clientes') }}/${response.existInVentas.clientes_id}" class="alert-link"> #${response.existInVentas.clientes_id} </a></p>`;
                          $('#oiw').val('');
                          $('#oin').text('');

                          $('#alert').html(html2).fadeIn();
                          break;
                      case 2:

                          $('#oiw').val(response.datosOii.order_id);
                          $('#oin').text(response.datosOii.order_item_name);

                          break;
                      default:

                          var html2 = `<p>El orden_item_id no existe.</p>`;
                          $('#oiw').val('');
                          $('#oin').text('');

                          $('#alert').html(html2).fadeIn();
                          break;
                  }
              },
              error: function(error){
                  console.log(error);
                  var html = `<p>Ha ocurrido un error inesperado al obtener el order_id_web.</p>`;

                  $('#alert').html(html).fadeIn();
              }
          });
      }
      
  }

</script>