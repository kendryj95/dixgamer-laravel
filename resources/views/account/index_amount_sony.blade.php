@extends('layouts.master-layouts')

@section('title', 'Listar Cuentas con Saldo libre sony')

@section('container')
<div class="container">
  <h1>Listar Cuentas con Saldo libre sony</h1>

  <div class="table-responsive">
    <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

      <thead>
        <tr>
          <th>#</th>
          <th>Cta ID</th>
          <th>Balance</th>
          <th>Day</th>
          <th>Usuario</th>
        </tr>
      </thead>
      <tbody>

          @if(count($rows) > 0)

            @foreach($rows as $i => $account)
              <!--- Si ya se cargó juego de ps4 la consola libre es ps3, y viceversa -->

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
                    {{ $account->balance }}
                  </a>
                </td>

                <td>
                  <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                    {{ $account->Day }}
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
        {{ $rows->render() }}
      </ul>

    </div>

  </div>
</div><!--/.container-->

@section('scripts')
@parent

<script>
  function filtrar() {
    var params = {};
    var min = $("#min").val();
    var max = $("#max").val();
    var orderBy = $('#orderBy').val();

    @if(isset($_GET['console']))
      params.console = "{{ $_GET['console'] }}";
    @endif

    if (orderBy != '') {
      params.order = orderBy;
    }
    if(min != "" && max != "") {
      params.saldoMin = min;
      params.saldoMax = max;
    } else {
      if (min != '') {
        return;
      } else if (max != '') {
        return;
      }
    }

    var queryString = "?"+Object.keys(params).map(key => key + '=' + params[key]).join('&');

    window.location.href = "{{  url('cuentas_con_saldo') }}"+queryString;
  }
</script>

@endsection

@endsection
