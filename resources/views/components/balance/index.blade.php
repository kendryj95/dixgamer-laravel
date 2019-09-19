<div class="table-responsive">
  <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

    @php
    $mostrar = (\Helper::validateAdministrator(session()->get('usuario')->Level)) ? '' : 'style="display:none"';
    @endphp

    <thead>
      <tr>
        <th>ID</th>
        <th {{$mostrar}}>ex_stock_id</th>
        <th>titulo</th>
        <th>consola </th>
        <th {{$mostrar}}>code_prov</th>
        <th {{$mostrar}}>n_order</th>
        <th>costo USD</th>
        <th {{$mostrar}}>costo</th>
        <th>usuario</th>
      </tr>
    </thead>
    <tbody>

        @if(count($saldos) > 0)

          @foreach($saldos as $saldo)

            <tr>

              <td>{{ $saldo->ID }}</td>
              <td {{$mostrar}}>{{ $saldo->ex_stock_id }}</td>
              <td>{{ $saldo->titulo }}</td>
              <td>{{ $saldo->consola }}</td>
              <td {{$mostrar}}>{{ $saldo->code_prov }}</td>
              <td {{$mostrar}}>{{ $saldo->n_order }}</td>
              <td>{{ $saldo->costo_usd }}</td>
              <td {{$mostrar}}>{{ $saldo->costo }}</td>
              <td class="text-center">
                <span class="badge badge-{{ $saldo->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$saldo->usuario}}">{{ substr($saldo->usuario,0 , 1) }}</span>
              </td>

            </tr>

          @endforeach

        @else
          <tr>
            <td colspan = '8' class="text-center">No se encontraron datos</td>
          </tr>
        @endif

    </tbody>
  </table>
  <div class="col-md-12">

    <ul class="pager">
      {{ $saldos->appends(
        [
          'column' => app('request')->input('column'),
          'word' => app('request')->input('word'),
        ]
        )->render() }}
    </ul>

  </div>

</div>
