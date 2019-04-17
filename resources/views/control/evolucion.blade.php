@extends('layouts.master-layouts')

@section('title', 'Evolución')

@section('container')

    <div class="container">
	   <h1>Evolución</h1>

     <div class="row">
         <form action="{{ url('evolucion') }}" method="get" class="form-inline">
             <div class="form-group col-md-3">
                 <label for="titulo">Titulo:</label>
                 <select name="titulo" class="selectpicker form-control" onchange="getData()" data-live-search="true" data-size="5" id="titulo">
                   <option value="">Seleccione Titulo</option>
                   @foreach($titulos as $t)
                    <option value="{{ $t->titulo }}">{{ str_replace('-', ' ', $t->titulo) }}</option>
                   @endforeach
                 </select>
             </div>

             <div class="form-group col-md-3">
                 <label for="consola">Consola:</label>
                 <select name="consola" class="form-control" onchange="showSlot(this.value);getData()" id="consola">
                   <option value="">Seleccione Consola</option>
                   <option value="Ps3">Ps3</option>
                   <option value="Ps4">Ps4</option>
                 </select>
             </div>

             <div style="display: none" id="div-slot" class="form-group col-md-3">
                 <label for="slot">Slot:</label>
                 <select name="slot" id="slot" onchange="getData()" class="form-control">
                     <option value="">Seleccione Slot</option>
                     <option value="Primario">Primario</option>
                     <option value="Secundario">Secundario</option>
                 </select>
             </div>
         </form>
     </div>

     <div style="margin-top: 20px" class="row">
      {{-- <div class="col-lg-1"></div> --}}
       <div id="graph-container" class="col-lg-12">
         <canvas id="myChart" width="200" height="120"></canvas>
       </div>
       {{-- <div class="col-lg-1"></div> --}}
     </div>
   
    
    </div><!--/.container-->

@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script type="text/javascript">
  var ctx = document.getElementById('myChart');
  var resetCanvas = function(){
    $('#myChart').remove(); // this is my <canvas> element
    $('#graph-container').append('<canvas id="myChart" width="200" height="120"><canvas>');
    canvas = document.querySelector('#myChart');
    ctx = canvas.getContext('2d');
  };

  /*$(document).ready(function() {
    getData();
  });*/

  function generarChart(elem, data, labels)
  {
    var myChart = new Chart(elem, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                fill: false,
                label: 'Precio',
                data: data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)'
                ],
                pointBackgroundColor:[
                    'rgba(255, 99, 132, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
              filler: {
                  propagate: true
              }
            }
        }
    });
  }

  function getData()
  {
    var titulo = $('#titulo').val();
    var consola = $('#consola').val();
    var slot = $('#slot').val();

    $.ajax({
      url: '{{ url('data_evolucion') }}',
      type: 'GET',
      dataType: 'json',
      data: {
        titulo: titulo,
        consola: consola,
        slot: slot
      },
      success: function (response){
        var datos = response;
        var labels = [];
        var data = [];

        datos.forEach(function(value){
          let l = value.Day;
          console.log("precio:",value.precio,"day:",l,"titulo:",value.titulo);
          labels.push(l);
          data.push(value.precio);
        });

        setTimeout(function(){
          console.log(data, labels);
          resetCanvas();
          generarChart(ctx,data,labels)
        }, 1000);
      },
      error: function (error) {
        console.log(error);
      }
    });
    
  }

  function showSlot(consola)
  {
    if (consola == 'Ps4') {
      $('#div-slot').show();
    } else {
      $('#slot').val('');
      $('#div-slot').hide();
    }
  }
</script>

  @stop