<div class="table-responsive">
  <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

    <thead>
      <tr>
        <th>#</th>
        <th>Cuenta</th>
      </tr>
    </thead>
    <tbody>

        @if(count($datos) > 0)

          @foreach($datos as $account)

            <tr>

              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                  {{ $account->cuentas_id }}
                </a>
              </td>

              <td>
                <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                  {{ $account->mail_fake }}
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
      {{ $datos->render() }}
    </ul>

  </div>

</div>
