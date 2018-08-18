<!-- Columna en la cual buscar -->
@if(count($columns) > 0)
<!-- Filtrador -->
<form class="form-inline" name="buscador" method="get" action="{{ url($path) }}">
    <div class="form-group col-md-4">

      <label for="column">Buscar en: </label> <br>
      <select name="column" class="form-control">
        @foreach($columns as $column)
          @if(app('request')->input('column') == $column)
            <option selected value="{{ $column }}"> {{$column}} </option>
          @else
              <option value="{{ $column }}" @if ($column == 'email') selected @endif> {{$column}} </option>
          @endif
        @endforeach
      </select>

    </div>


    <!-- Palabra a buscar en la columna -->
    <div class="form-group col-md-4">

      <label for="word">Palabra(s): </label> <br>
      <input id="word" type="text" class="form-control" name="word" value="{{ app('request')->input('word') }}">

    </div>

    <div class="form-group">
      <label for="palabra">&nbsp;</label> <br>
      <button type="submit" value="Buscar" name="enviar" class="btn btn-default">Buscar</button>

    </div>


</form>
@endif
