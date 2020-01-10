@extends('layouts.master-layouts')

@section('title', 'Cuentas on Saldo libre')

@section('container')
<div class="container">
  <h1>Listar cuentas</h1>

  @php

  $queryParams = "";

  if (isset($_GET['order'])) {
    $queryParams .= "&order=" . $_GET['order'];
  }

  @endphp
  
  <div class="row">
    <div class="col-md-3">
      <a class="btn btn-default btn-sm" href="cuentas_con_saldo" title="Todos" style="margin:5px 0 0 0;">Todos</a>
      <a class="btn btn-default btn-sm" href="cuentas_con_saldo?console=ps3{{$queryParams}}" title="Libres para PS4" style="margin:5px 0 0 0;">Libres para PS4</a>
      <a class="btn btn-default btn-sm" href="cuentas_con_saldo?console=ps4{{$queryParams}}" title="Libres para PS3" style="margin:5px 0 0 0;">Libres para PS3</a>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <select style="margin-top: 5px" onchange="filtrar(this.value)" class="form-control input-sm">
          <option value="monto" @if(isset($_GET['order']) && $_GET['order'] == 'monto') selected @endif>Ordenar Monto Libre</option>
          <option value="cuenta" @if(isset($_GET['order']) && $_GET['order'] == 'cuenta') selected @endif>Ordenar ID Cuenta</option>
          <option value="monto-cuenta" @if(isset($_GET['order']) && $_GET['order'] == 'monto-cuenta') selected @endif>Ordenar Monto-Cuenta</option>
          <option value="cuenta-monto" @if(isset($_GET['order']) && $_GET['order'] == 'cuenta-monto') selected @endif>Ordenar Cuenta-Monto</option>
        </select>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input style="margin-top: 5px" type="number" name="min" id="min" onchange="filtroRange()" value="{{$range->saldoMin}}" placeholder="Saldo USD Min" class="form-control input-sm">
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input style="margin-top: 5px" type="number" name="max" id="max" onchange="filtroRange()" value="{{$range->saldoMax}}" placeholder="Saldo USD Max" class="form-control input-sm">
      </div>
    </div>
  </div>

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
          <th>Usuario</th>
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

                <td class="text-center">
                  <span class="badge badge-{{ $account->color_user }}" style="opacity:0.7; font-weight:400;" title="{{$account->usuario}}">{{ substr($account->usuario,0 , 1) }}</span>
                </td>
              </tr>

            @endforeach

          @else
            <tr>
              <td colspan = '6' class="text-center">No se encontraron datos</td>
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

@section('scripts')
@parent

<script>
  function filtrar(orderBy) {
    var queryParams = '';
    @if(isset($_GET['console']))
      queryParams += "?consola={{ $_GET['console'] }}";
    @endif

    var min = $("#min").val();
    var max = $("#max").val();
    var queryRange = "";

    queryParams += queryParams != '' ? "&order="+orderBy : "?order="+orderBy;

    if(min != "" && max != "") {
      queryParams += "&saldoMin="+min+"&saldoMax="+max;
    }

    window.location.href = "{{  url('cuentas_con_saldo') }}"+queryParams;
  }

  function filtroRange() {
    var min = $("#min").val();
    var max = $("#max").val();

    if(min != "" && max != "") {
      var urlParams = new URLSearchParams(window.location.search);
      var queryString = urlParams.toString();
      window.location.href = "?"+queryString+"&saldoMin="+min+"&saldoMax="+max;
    }
  }
</script>

@endsection

@endsection
