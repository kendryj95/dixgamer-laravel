<div class="table-responsive">
  <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

    <thead>
      <tr>
        <th>#</th>
        <th>Cuenta</th>
        <th style="width: 400px">Notas</th>
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
              <td style="text-align: center;">
                @if ($account->Notas != '')
                  <div class="alert alert-warning" style="color: #8a6d3b; background-color:#FFDD87; padding: 4px 7px; font-size: 12px; font-style:italic; margin:0px; opacity: 0.9;"><i class="fa fa-comment fa-fw"></i> {!! $account->Notas !!} </div>
                @endif
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
