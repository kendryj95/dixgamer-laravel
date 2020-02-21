@extends('layouts.master-layouts')

@section('title', "Listar cuentas")

@section('container')
<div class="container">
  <h1>Listar cuentas</h1>
  @if (count($errors) > 0)
				<div class="alert alert-danger text-center">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
	@endif
  <!-- Filter -->
    <div class="row">
      <form action="{{ url('dominios_cuentas') }}" method="post" id="form-dominios">
        {{ csrf_field() }}
        <div style="margin-bottom: 20px" class="col-md-12">
          <div class="row">
          @foreach ($dominios as $value) 
            <div class="col-md-2">
            <label for="">{{ $value->dominio }}</label>
              @php $indicador_habilitado = $value->indicador_habilitado; @endphp
              <input type="checkbox" class="dominios" data-id="{{ $value->ID }}" @if ($indicador_habilitado == 1) checked @endif data-size="small">
                <small style="display:block; margin-top: 5px" class="text-muted">Uso: {{ date('d/m/Y H:i:s', strtotime($value->update_at)) }}</small>
            </div>
          @endforeach
          </div>
        </div>
        <input type="hidden" name="ID" id="id">
        <input type="hidden" name="indicador_habilitado" id="indicador">
      </form>

        @component('components/filters/column_word')
            @slot('columns',$columns);
            @slot('path','cuentas');
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
  $(document).ready(function() {
    $('.dominios').bootstrapToggle();

    $('.dominios').on('change', function() {
      var id = $(this).data('id');
      var indicador = $(this).prop('checked') ? 1 : 0;
      
      $('#id').val(id);
      $('#indicador').val(indicador);
      
      setTimeout(() => {
        $('#form-dominios').submit();
      }, 500);
    })
  })
</script>

@endsection
