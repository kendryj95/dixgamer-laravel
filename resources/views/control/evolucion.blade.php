@extends('layouts.master-layouts')

@section('title', 'Evolución')

@section('container')

    <div class="container">
	   <h1>Evolución</h1>

     <div class="row">
         <form action="{{ url('evolucion') }}" method="get" class="form-inline">
             <div class="form-group col-md-3">
                 <label for="titulo">Titulo:</label>
                 <select name="titulo" class="selectpicker form-control" onchange="formatTitleAndGetData()" data-live-search="true" data-size="5" id="titulo">
                   <option value="">Seleccione Titulo</option>
                   @foreach($titulos as $t)
                    <option value="{{ explode(" (",$t->titulo)[0] }}">{{ str_replace('-', ' ', $t->titulo) }}</option>
                   @endforeach
                 </select>
             </div>

             <input type="hidden" name="consola" id="consola">

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

  function generarChart(elem, data, data2, labels)
  {
    var myChart = new Chart(elem, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                fill: false,
                label: 'Precio',
                yAxisID: 'A',
                data: data,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                // pointBackgroundColor:'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            },{
                fill: false,
                label: 'Cantidad',
                yAxisID: 'B',
                data: data2,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                // pointBackgroundColor:'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
              filler: {
                  propagate: true
              }
            },
            scales: {
                yAxes: [
                  {
                    id: 'A',
                    type: 'linear',
                    position: 'left',
                    ticks: {
                        max: 5000,
                        min: 0,
                        stepSize: 500
                    }
                  },{
                    id: 'B',
                    type: 'linear',
                    position: 'right'
                  }
                ],
                xAxes: [{
                    ticks: {
                        min: '2017-07-01',
                        max: '2020-07-31'
                    },
                    time: {
                        unit: 'month',
                        unitStepSize: 4
                    },
                }]
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
        var data2 = [];

        datos.forEach(function(value){
          let l = value.Day;
          labels.push(l);
          data.push(value.precio);
          data2.push(value.Q);
        });

        setTimeout(function(){
          resetCanvas();
          generarChart(ctx,data,data2,labels)
        }, 1000);
      },
      error: function (error) {
        console.log(error);
      }
    });
    
  }

  function showSlot(consola)
  {
    if (consola.indexOf('ps4') >= 0) {
      $('#div-slot').show();
    } else {
      $('#slot').val('');
      $('#div-slot').hide();
    }
  }

  function formatTitleAndGetData()
  {
    var select = document.getElementById('titulo');

    var consola = (select.options[select.selectedIndex].text.substr(-4)).replace(")","");

    document.getElementById('consola').value = consola.trim();

    setTimeout(function(){
      getData();
      showSlot(consola.trim());
    },200);
  }
</script>

  @stop