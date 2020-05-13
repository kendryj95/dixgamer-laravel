@extends('layouts.master-layouts')

@section('title', 'Listar ' . ($revendedor ? 'Re vendedores' : 'Clientes'))

@section('container')
<div class="container">
  <h1>Listar {{ $revendedor ? 'Re vendedores' : 'Clientes' }}</h1>
  @if (count($errors) > 0)
				<div class="alert alert-danger text-center">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
	@endif
  <!-- verificando -->
  <div class="row">
    @component('components/filters/column_word')
      @slot('columns',$columns);
      @slot('path',$revendedor ? 'clientes/tipo/re' : 'clientes');
    @endcomponent
  </div>

<!-- verificando -->

  <div class="row">
    <div class="table-responsive">
      <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

        <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Usuario ML</th>
          <th>Ciudad</th>
          <th style="text-align:right;">E-mail</th>
        </tr>
        </thead>
        <tbody>

        @if(count($customers) > 0)

          @foreach($customers as $customer)

            <tr>

              <td>
                <a title="Ir a Cliente." href="{{ url('/clientes', [$customer->ID] ) }}">
                  <!-- <a title="Ir a Cliente." href="https://dixgamer.com/wp-admin/post.php?post=28166&action=edit"> -->
                  {{ $customer->ID }}
                </a>
              </td>

              <td>
                <a title="Ir a Cliente." href="{{ url('/clientes', [$customer->ID] ) }}">
                  {{ $customer->nombre }} {{ $customer->apellido }}
                </a>
              </td>

              <td>
                <a title="Ir a Cliente." href="{{ url('/clientes', [$customer->ID] ) }}">
                  {{ $customer->ml_user }}
                </a>
              </td>
              <td>
                <a title="Ir a Cliente." href="{{ url('/clientes', [$customer->ID] ) }}">
                  @if($customer->ciudad)
                    {{ $customer->ciudad }}
                  @endif
                  {{ $customer->provincia }}
                </a>
              </td>
              <td align="right">
                <a title="Ir a Cliente." href="{{ url('/clientes', [$customer->ID] ) }}">
                  <span class="E-mail">{{ $customer->email }}</span>
                </a>
              </td>

            </tr>

          @endforeach

        @else
          <tr>
            <td colspan = '10' class="text-center">No se encontraron datos</td>
          </tr>
        @endif

        </tbody>
      </table>
      <div class="col-md-12">

        <ul class="pager">
          {{ $customers->appends(
            [
              'email' => app('request')->input('email'),
              'column' => app('request')->input('column'),
              'word' => app('request')->input('word'),
            ]
            )->render() }}
        </ul>

      </div>

    </div>
  </div>

</div><!--/.container-->

@endsection
