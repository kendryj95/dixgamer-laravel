@extends('layouts.master-layouts')

@section('title', "Listar cuentas robadas")

<style>

.bootstrap-tagsinput {
  width: 100%;
}

.tag.label {
  cursor: pointer;
}

</style>

@section('container')
<div class="container">
  <h1>Listar cuentas robadas</h1>
  @if (count($errors) > 0)
				<div class="alert alert-danger text-center">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
  @endif
  
  <div class="row">
    <form action="{{ url('config/general') }}" id="cuentas_excluidas" method="post">
      <div class="col-md-8">
          {{ csrf_field() }}
          <input type="hidden" name="opt" value="5">
          <div class="form-group">
            <label for="">Cuentas Excluidas:</label><br>
            <input type="text" name="cuentas_excluidas" id="cuentas" value="{{$cuentas_excluidas}}" data-role="tagsinput">
          </div>
      </div>
      <div class="col-md-4">
        <button style="margin-top: 24px" type="submit" class="btn btn-primary">Actualizar Config</button>
      </div>
    </form>
  </div>


  <!-- Filter -->
    <div class="row">
        @component('components/filters/column_word')
            @slot('columns',$columns);
            @slot('path','cuentas_robadas');
        @endcomponent
    </div>


  <!-- COMPONENTE DE CUENTAS -->

    <div class="row">
        @component('components.account.index')
            @slot('accounts', $accounts)
        @endcomponent
    </div>



</div><!--/.container-->

@endsection

@section('scripts')
@parent

<script>
var band = true;
$(document).ready(function() {
  
  $("#cuentas_excluidas").keypress(function(e) {
    if (e.which == 13) {
        return false;
    }
  });

  $('.bootstrap-tagsinput span.tag.label span[data-role="remove"]').on('click', function(event) {
    band = false; // Bandera que evita que haga un redirect si eliminan un tag
  })
  $('.bootstrap-tagsinput span.tag.label').on('click', function() {
    var el = $(this);
    var id_cta = el.text();

    if (band) {
      setTimeout(() => {
        window.location.href = "{{ url('cuentas') }}/"+id_cta;
      }, 400);  
    }

    band = true;    
  })
  
})

</script>

@endsection
