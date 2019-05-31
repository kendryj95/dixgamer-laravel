@extends('layouts.master-layouts')

@section('title', 'Evolución')

@section('container')

    <div class="container">
	   <h1>Evolución</h1>

     <div class="row">
         <form action="{{ url('evolucion') }}" method="get" class="form-inline">
             <div class="form-group col-md-2">
                 <label for="titulo">Titulo:</label>
                 <select name="titulo" class="selectpicker form-control input-sm" onchange="formatTitleAndGetData()" data-live-search="true" data-size="5" id="titulo">
                   <option value="">Seleccione Titulo</option>
                   @foreach($titulos as $t)
                    <option value="{{ explode(" (",$t->titulo)[0] }}">{{ str_replace('-', ' ', $t->titulo) }}</option>
                   @endforeach
                 </select>
             </div>

             <input type="hidden" name="consola" id="consola">

             <div style="display: none" id="div-slot" class="form-group col-md-2">
                 <label for="slot">Slot:</label>
                 <select name="slot" id="slot" onchange="getData()" class="form-control input-sm">
                     <option value="">Seleccione Slot</option>
                     <option value="Primario">Primario</option>
                     <option value="Secundario">Secundario</option>
                 </select>
             </div>

             <div class="form-group col-md-2">
                 <label for="agrupar">Agrupar por:</label>
                 <select name="agrupar" id="agrupar" onchange="getData()" class="form-control input-sm">
                     <option value="dia" selected>Día</option>
                     <option value="semana">Semana</option>
                     <option value="mes">Mes</option>
                 </select>
             </div>

             <div class="form-group col-md-2">
                 <label for="agrupar">Fecha Inicio:</label>
                 <input type="date" class="form-control input-sm" onchange="getData()" name="f_inicio" id="f_inicio">
             </div>

             <div class="form-group col-md-2">
                 <label for="agrupar">Fecha Fin:</label>
                 <input type="date" class="form-control input-sm" onchange="getData()" name="f_fin" id="f_fin">
             </div>

             <div class="form-group col-md-1">
                 <label for="agrupar">Min Precio:</label>
                 <input type="number" class="form-control input-sm" onchange="getData()" value="0" name="min_precio" id="min_precio">
             </div>

             <div class="form-group col-md-1">
                 <label for="agrupar">Max Precio:</label>
                 <input type="number" class="form-control input-sm" onchange="getData()" value="5000" name="max_precio" id="max_precio">
             </div>

             <div class="form-group col-md-1">
                 <label for="agrupar">Min Cant.:</label>
                 <input type="number" class="form-control input-sm" onchange="getData()" value="0" name="min_cant" id="min_cant">
             </div>

             <div class="form-group col-md-1">
                 <label for="agrupar">Max Cant.:</label>
                 <input type="number" class="form-control input-sm" onchange="getData()" value="200" name="max_cant" id="max_cant">
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
    var min_precio = parseInt($('#min_precio').val()),
        max_precio = parseInt($('#max_precio').val()),
        min_cant = parseInt($('#min_cant').val()),
        max_cant = parseInt($('#max_cant').val())

    var limits = {
      min_precio: min_precio,
      max_precio: max_precio,
      min_cant: min_cant,
      max_cant: max_cant
    };

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
            scales: {
                yAxes: [
                  {
                    id: 'A',
                    type: 'linear',
                    position: 'left',
                    ticks: {
                        max: limits.max_precio,
                        min: limits.min_precio,
                        stepSize: 500
                    }
                  },{
                    id: 'B',
                    type: 'linear',
                    position: 'right',
                    ticks: {
                        max: limits.max_cant,
                        min: limits.min_cant,
                        stepSize: 10
                    }
                  }
                ],
                xAxes: [{
                    ticks: {
                        /*min: '2017-07-01',
                        max: '2020-07-31',
                        source: 'data'*/
                        autoSkip : true,
                    },
                    time: {
                        displayFormats: {
                          'millisecond': 'DD MMM',
                          'second': 'DD MMM',
                          'minute': 'DD MMM',
                          'hour': 'DD MMM',
                          'day': 'DD MMM',
                          'week': 'DD MMM',
                          'month': 'DD MMM',
                          'quarter': 'DD MMM',
                          'year': 'DD MMM',
                        },
                        unitStepSize: 1,
                        unit: 'day',
                    },
                    gridLines : {
                        display : false,
                    }
                }]
            }
        }
    });
  }

  function getData()
  {
    var params = getParams();

    $.ajax({
      url: '{{ url('data_evolucion') }}',
      type: 'GET',
      dataType: 'json',
      data: params,
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

  function getParams() {
    var titulo = $('#titulo').val();
    var consola = $('#consola').val();
    var slot = $('#slot').val();
    var agrupar = $('#agrupar').val();
    var fecha_inicio = $('#f_inicio').val();
    var fecha_fin = $('#f_fin').val();

    var params = {};

    if (titulo != "") {
      params.titulo = titulo;
    }
    if (consola != "") {
      params.consola = consola;
    }
    if (slot != "") {
      params.slot = slot;
    }
    if (agrupar != "") {
      params.agrupar = agrupar;
    }
    if (fecha_inicio != "" && fecha_fin != "") {
      params.fecha_inicio = fecha_inicio;
      params.fecha_fin = fecha_fin;
    }

    return params;
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

    var consola = select.options[select.selectedIndex].text;

    var index = consola.indexOf("(");

    consola = consola.substring(index+1);

    consola = (consola.replace(")","")).replace(" ","-");

    document.getElementById('consola').value = consola.trim();

    setTimeout(function(){
      getData();
      showSlot(consola.trim());
    },200);
  }
</script>

  @stop