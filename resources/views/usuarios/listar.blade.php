@extends('layouts.master-layouts')

@section('container')
<div class="container">
  <h1>Listar Usuarios</h1>
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
    <div class="table-responsive">
      <table border="0" align="center" cellpadding="0" cellspacing="5" class="table table-striped">

        <thead>
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Level</th>
          <th>Acción</th>
        </tr>
        </thead>
        <tbody>

        @if(count($usuarios) > 0)

          @foreach($usuarios as $i => $usuario)

            <tr>

              <td>
                  {{ $i+1 }}
              </td>

              <td>
                  {{ $usuario->Nombre }}
              </td>

              <td>
                  {{ $usuario->Level }}
              </td>

              <td>
                  <a href="{{ url("usuario/edit/$usuario->id")}}" title="Editar"><i class="fa fa-pencil"></i></a>
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
      <!-- <div class="col-md-12">
      
        <ul class="pager">
          {{-- {{ $usuarios->render() }} --}}
        </ul>
      
      </div> -->

    </div>
  </div>

</div><!--/.container-->

@endsection
