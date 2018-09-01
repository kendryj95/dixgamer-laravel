@extends('layouts.master-layouts')

@section('title', 'Cuentas on Saldo libre')

@section('container')
<div class="container">
  <h1>Listar cuentas</h1>
  <a class="btn btn-default btn-sm" href="cuentas_con_saldo" title="Todos" style="margin:5px 0 0 0;">Todos</a>
  <a class="btn btn-default btn-sm" href="cuentas_con_saldo?console=ps3" title="Libres para PS4" style="margin:5px 0 0 0;">Libres para PS4</a>
  <a class="btn btn-default btn-sm" href="cuentas_con_saldo?console=ps4" title="Libres para PS3" style="margin:5px 0 0 0;">Libres para PS3</a>

  <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

      <thead>
        <tr>
          <th>#</th>
          <th>Libre en USD</th>
            @if(Helper::validateAdministrator(session()->get('usuario')->Level))
              <th>Libre en Pesos</th>
            @endif
          <th>Para Consola</th>
        </tr>
      </thead>
      <tbody>

          @if(count($accounts) > 0)

            @foreach($accounts as $account)
              <!--- Si ya se cargó juego de ps4 la consola libre es ps3, y viceversa -->
              @if($account-> consola == 'ps4')
                <?php $consol = "ps3"; ?>
              @elseif($account-> consola == "ps3")
                <?php $consol = "ps4"; ?>
              @else
                <?php $consol = ""; ?>
              @endif
              <tr>

                <td>
                  <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                    {{ $account->cuentas_id }}
                  </a>
                </td>

                <td>
                  <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                    {{ $account->libre_usd }}
                  </a>
                </td>

                <?php // validamos administracion  ?>
                @if(Helper::validateAdministrator(session()->get('usuario')->Level))
                  <td>
                    <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                      {{ $account->libre_ars }}
                    </a>
                  </td>
                @endif

                <td>
                  <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                    {{ $consol }}
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
        {{ $accounts->render() }}
      </ul>

    </div>

  </div>
</div><!--/.container-->

@endsection
