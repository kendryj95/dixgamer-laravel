@extends('layouts.master-layouts')

@section('title', 'Listar Cuentas con Saldo libre')

@section('container')
<div class="container">
  <h1>Listar Cuentas con Saldo libre</h1>

  @php

  $queryParams = "";

  if (isset($_GET['order'])) {
    $queryParams .= "&order=" . $_GET['order'];
  }

  @endphp
  
  <div class="row">
    <div class="col-md-12">
      <h4>Dominios Excluidos</h4>
      @foreach ($dominios_excluidos as $i => $value)
        <span class="label label-normal">{{($i+1)}}. {{$value}}</span>
      @endforeach

    <hr>

    </div>
    <div class="col-md-3">
      <a class="btn @if(!isset($_GET['console'])) btn-success @else btn-default @endif btn-sm" href="cuentas_con_saldo" title="Todos" style="margin:5px 0 0 0;">Todos</a>
      <a class="btn @if(isset($_GET['console']) && $_GET['console'] == 'ps3') btn-success @else btn-default @endif btn-sm" href="cuentas_con_saldo?console=ps3{{$queryParams}}" title="Libres para PS4" style="margin:5px 0 0 0;">Libres para PS4</a>
      <a class="btn @if(isset($_GET['console']) && $_GET['console'] == 'ps4') btn-success @else btn-default @endif btn-sm" href="cuentas_con_saldo?console=ps4{{$queryParams}}" title="Libres para PS3" style="margin:5px 0 0 0;">Libres para PS3</a>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <select style="margin-top: 5px" onchange="filtrar(this.value)" id="orderBy" class="form-control input-sm">
          <option value="monto" @if(isset($_GET['order']) && $_GET['order'] == 'monto') selected @endif>Ordenar Monto Libre</option>
          <option value="cuenta" @if(isset($_GET['order']) && $_GET['order'] == 'cuenta') selected @endif>Ordenar ID Cuenta</option>
          <option value="monto-cuenta" @if(isset($_GET['order']) && $_GET['order'] == 'monto-cuenta') selected @endif>Ordenar Monto-Cuenta</option>
          <option value="cuenta-monto" @if(isset($_GET['order']) && $_GET['order'] == 'cuenta-monto') selected @endif>Ordenar Cuenta-Monto</option>
        </select>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input style="margin-top: 5px" type="number" name="min" id="min" onchange="filtrar()" value="{{$range->saldoMin}}" placeholder="Saldo USD Min" class="form-control input-sm">
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-group">
        <input style="margin-top: 5px" type="number" name="max" id="max" onchange="filtrar()" value="{{$range->saldoMax}}" placeholder="Saldo USD Max" class="form-control input-sm">
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
          <th>Dominio</th>
          <th>Reseteos</th>
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
                
                <td>
                  <a title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                    {{ explode('@',$account->mail_fake)[1] }}
                  </a>
                </td>
                
                <td>
                  <a class="badge @if($account->reseteos == 0) badge-success @elseif ($account->reseteos == 1) badge-warning @else badge-danger @endif" title="Ir a cuenta." href="{{ url('/cuentas', [$account->cuentas_id] ) }}">
                    {{ $account->reseteos }}
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
        {{ $accounts->appends(
          [
            'console' => app('request')->input('console'),
            'order' => app('request')->input('order'),
            'saldoMin' => app('request')->input('saldoMin'),
            'saldoMax' => app('request')->input('saldoMax'),
          ]
          )->render() }}
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
