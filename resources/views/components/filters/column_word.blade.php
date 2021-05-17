<!-- Columna en la cual buscar -->
@if(count($columns) > 0)
<!-- Filtrador -->
<form class="form-inline" name="buscador" method="get" action="{{ url($path) }}">
    <div class="form-group col-md-3">

      <label for="column">Buscar en: </label> <br>
      <select name="column" class="form-control" id="column">
        @php $selected = ''; @endphp
        @foreach($columns as $i => $column)
          @if(app('request')->input('column') == $column)
            <option selected value="{{ $column }}"> {{$column}} </option>
          @else
              @if($path == 'cuentas' || $path == 'cuentas_robadas')
                @if($column == 'mail_fake')
                  @php $selected = 'selected'; @endphp
                @else
                  @php $selected = ''; @endphp
                @endif
              @elseif($path == 'sales/lista_cobro')
                @if($column == 'ref_cobro')
                  @php $selected = 'selected'; @endphp
                @else
                  @php $selected = ''; @endphp
                @endif
              @else
                @if($column == 'email')
                  @php $selected = 'selected'; @endphp
                @else
                  @php $selected = ''; @endphp
                @endif
              @endif
              <option value="{{ $column }}" {{ $selected }}> {{$column}} </option>
              {{-- option adicional para cuentas_notas --}}
              @if($path == 'cuentas_notas' && $i == (count($columns)-1))
              <option value="Notas" @if(!app('request')->input('column') || app('request')->input('column') == 'Notas') selected @endif>Notas</option>
              @endif
              {{-- option adicional para cuentas_notas --}}
              @if($path == 'stock_notas' && $i == (count($columns)-1))
              <option value="Notas" @if(!app('request')->input('column') || app('request')->input('column') == 'Notas') selected @endif>Notas</option>
              @endif
          @endif
        @endforeach
      </select>

    </div>


    <!-- Palabra a buscar en la columna -->
    <div class="form-group col-md-3">

      <label for="word">Palabra(s): </label> <br>
      <input id="word" type="text" class="form-control" name="word" value="{{ app('request')->input('word') }}">

    </div>

    @if(isset($consolas))
        <div style="display: @if(app('request')->input('column') != "titulo") none @else block @endif " id="showConsola" class="form-group col-md-3">

            <label for="consola">Consola: </label> <br>
            <select name="consola" id="consola" class="form-control">
                <option value="">Seleccione consola</option>
                @foreach($consolas as $obj)
                    <option value="{{$obj->consola}}" @if($obj->consola == app('request')->input('consola')) selected @endif>{{$obj->consola}}</option>
                @endforeach
            </select>

        </div>
    @endif

    <div class="form-group">
      <label for="palabra">&nbsp;</label> <br>
      <button type="submit" value="Buscar" name="enviar" class="btn btn-default">Buscar</button>

    </div>


</form>
@endif

<script>
    setTimeout(function() {
        $(document).ready(function() {
            $('#column').change(function() {
                var value = $(this).val();
                var path = '{{ $path }}';
                if (path === "stock" && value === "titulo") {
                    $('#showConsola').show();
                } else {
                    $('#showConsola').hide();
                }
            });
        });
    }, 500)
</script>
