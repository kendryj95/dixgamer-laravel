@extends('layouts.master-layouts')

@push('css')

<link rel="stylesheet" href="{{asset('plugins/timepicker/css/timepicker.less')}}">

@endpush

@section('container')
  <div class="container text-center">
  	<h1>Editar horario</h1>
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
      <div class="col-sm-4">
      </div>

      <div class="col-sm-4">
        <form method="post" name="form1" action='{{url("horarios/editar")}}'>
          {{ csrf_field() }}
          <div id="user-result-div" class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
            <input class="form-control" type="text" name="nombre" id="nombre" autocomplete="off" spellcheck="false" placeholder="Nombre" disabled value="{{$horario_ope->usuario}}">
          </div>
          
          <input type="hidden" name="id_hor" value="{{$horario_ope->ID}}">
          <input type="hidden" name="day_h" id="day_h" value="{{$horario_ope->Day}}">

          <div id="user-result-div" class="input-group form-group">
            <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
            <input class="form-control" type="text" name="day" id="day" autocomplete="off" spellcheck="false" placeholder="Día" value="{{ date('d/m/Y', strtotime($horario_ope->Day)) }}" disabled>
          </div>

          <label for="f_inicio">Hora inicio</label>
          <div id="user-result-div" class="input-group form-group bootstrap-timepicker timepicker">
            <span class="input-group-addon"><i class="fa fa-clock-o fa-fw"></i></span>
            <input class="form-control" type="text" name="f_inicio" id="f_inicio" autocomplete="off" spellcheck="false" placeholder="Fecha inicio" value="{{ substr($horario_ope->inicio, -8) }}">
          </div>

          <label for="f_inicio">Hora fin</label>
          <div id="user-result-div" class="input-group form-group bootstrap-timepicker timepicker">
            <span class="input-group-addon"><i class="fa fa-clock-o fa-fw"></i></span>
            <input class="form-control" type="text" name="f_fin" id="f_fin" autocomplete="off" spellcheck="false" placeholder="Fecha fin" value="{{ substr($horario_ope->fin, -8) }}">
          </div>

          <button class="btn btn-primary btn-block btn-lg" type="submit">Editar</button>
          <br>
      </form>
    </div>
    <div class="col-sm-4">
    </div>
  </div>
</div><!--/.container-->
@endsection



@section('scripts')
<script type="text/javascript" src="{{asset('plugins/timepicker/js/bootstrap-timepicker.js')}}"></script>

<script type="text/javascript">
  $('#f_inicio, #f_fin').timepicker({
      minuteStep: 1,
      template: 'modal',
      appendWidgetTo: 'body',
      showSeconds: true,
      showMeridian: false,
      defaultTime: false
  });
</script>
@stop
