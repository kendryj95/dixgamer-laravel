<div class="table-responsive">
  <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

    <thead>
      <tr>
        <th>#</th>
        <th>Cuenta</th>
        <th>Notas</th>
        <th>Fecha</th>
        <th>Operador</th>
      </tr>
    </thead>
    <tbody>

        @if(count($accounts_notes) > 0)

          @foreach($accounts_notes as $i => $account)

            <tr>

              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                  {{ $account->ID }}
                </a>
              </td>

              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                  {{ $account->cuentas_id }}
                </a>
              </td>
              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                  {{ $account->Notas }}
                </a>
              </td>
              <td>
                @php
                $dia = date('d', strtotime($account->Day));
                $mes = date('n', strtotime($account->Day));
                $mes = \Helper::getMonthLetter($mes);
                $anio = date('Y', strtotime($account->Day));
                $fecha = "$dia-$mes-$anio";
                @endphp
                
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                  {{ $fecha }}
                </a>
              </td>

              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                  {{ $account->usuario }}
                </a>
              </td>


            </tr>

          @endforeach

        @else
          <tr>
            <td colspan = '5' class="text-center">No se encontraron datos</td>
          </tr>
        @endif

    </tbody>
  </table>
  <div class="col-md-12">

    <ul class="pager">
      {{ $accounts_notes->appends(
        [
          'column' => app('request')->input('column'),
          'word' => app('request')->input('word'),
        ]
        )->render() }}
    </ul>

  </div>

</div>
