<div class="table-responsive">
  <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

    <thead>
      <tr>
        <th>#</th>
        <th>Cuenta</th>
        <th>Juegos</th>
        <th>Usuario</th>
      </tr>
    </thead>
    <tbody>

        @if(count($accounts) > 0)

          @foreach($accounts as $account)

            <tr>

              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->id] ) }}">
                  {{ $account->id }}
                </a>
              </td>

              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->id] ) }}">
                  {{ $account->mail_fake }}
                </a>
              </td>
              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->id] ) }}">
                  {{ $account->juego }}
                </a>
              </td>
              <td class="text-center">
                <span class="badge badge-{{ $account->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$account->usuario}}">{{ substr($account->usuario,0 , 1) }}</span>
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
      {{ $accounts->appends(
        [
          'column' => app('request')->input('column'),
          'word' => app('request')->input('word'),
        ]
        )->render() }}
    </ul>

  </div>

</div>
