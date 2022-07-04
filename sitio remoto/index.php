<?php
require_once "header.php";
require_once "arduino1/conexion-a1.php";
require_once "arduino2/conexion-a2.php";
$conexion1 = connectdb1();
$conexion2 = connectdb2();

$sql2 = "SELECT p.NOMBRE, p.MINIMO, p.MAXIMO, t.VALOR FROM PARAMETROS AS p, TIEMPO_REAL AS t WHERE p.NOMBRE = t.PARAMETRO ORDER BY PARAMETRO";
$result2_a1 = pg_query($conexion1, $sql2);
$result2_a2 = pg_query($conexion2, $sql2);

$sql3 = "SELECT NOMBRE FROM PARAMETROS ORDER BY NOMBRE";
$result3_a1 = pg_query($conexion1, $sql3);
$result3_a2 = pg_query($conexion2, $sql3);

function medidas($nombre)
{
  if ($nombre == 'Temperatura') {
    $resultado = 'ºC';
    return $resultado;
  } else if ($nombre == 'Sonido') {
    $resultado = 'dB';
    return $resultado;
  } else {
    $resultado = 'ppm';
    return $resultado;
  }
}

function identificador($parametro)
{
  $p = $parametro;
  if ($p == 'Temperatura') {
    $id = 'temperatura';
  } else if ($p == 'Sonido') {
    $id = 'sonido';
  } else if ($p == 'Monoxido de carbono (CO)') {
    $id = 'monoxido';
  } else if ($p == 'Dioxido de carbono (CO2)') {
    $id = 'dioxido';
  } else if ($p == 'Tolueno (C7H8)') {
    $id = 'tolueno';
  } else if ($p == 'Amoniaco (NH3)') {
    $id = 'amoniaco';
  }

  return $id;
}
?>

<!-- Página -->

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h2>
      <span>
        <img src="/static/svg/logo.svg" alt="" width="3.5%">
      </span>
      Safe Worker Security Monitor <span style="color:gray">Reportes</span>
    </h2>

  </div>

  <div class="bg-light mb-3">
    <h3 class="py-3 text-center">Tiempo real: <span style="color:teal"> Mediciones de los parámetros en tiempo real.</h3>
    <div style="margin: 10px 70px 20px 70px">
      <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active text-dark" id="estacion1-tab" data-bs-toggle="tab" data-bs-target="#estacion1" type="button" role="tab" aria-controls="estacion1" aria-selected="true">Estación 1</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link text-dark" id="estacion2-tab" data-bs-toggle="tab" data-bs-target="#estacion2" type="button" role="tab" aria-controls="estacion2" aria-selected="false">Estación 2</button>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="estacion1" role="tabpanel" aria-labelledby="estacion1-tab">
          <div id="tiempo-real-a1">

          </div>

        </div>
        <div class="tab-pane fade" id="estacion2" role="tabpanel" aria-labelledby="estacion2-tab">
          <div id="tiempo-real-a2">

          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="bg-light mb-3">
    <h3 class="py-3 text-center py-3">Incidencias: <span style="color:teal"> Gráficos de las incidencias cometidas por cada parámetro en un tiempo determinado.</h3>
    <div style="margin: 10px 70px 20px 70px">
      <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active text-dark" id="estacion1I-tab" data-bs-toggle="tab" data-bs-target="#estacion1I" type="button" role="tab" aria-controls="estacion1I" aria-selected="true">Estación 1</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link text-dark" id="estacion2I-tab" data-bs-toggle="tab" data-bs-target="#estacion2I" type="button" role="tab" aria-controls="estacion2I" aria-selected="false">Estación 2</button>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="estacion1I" role="tabpanel" aria-labelledby="estacion1I-tab">
          <div class="row g-4 py-5 row-cols-1 row-cols-lg-3 px-5">
            <?php

            if ($result3_a1) {

              while ($datos = pg_fetch_row($result3_a1, null, PGSQL_ASSOC)) {


            ?>
                <div class="feature col bg-light">

                  <h4><?php echo $datos['nombre'] ?></h4>
                  <canvas id="<?php echo identificador($datos['nombre']), 1 ?>" width="100" height="100"></canvas>

                </div>
            <?php
              }
            }

            ?>
          </div>
        </div>
        <div class="tab-pane fade" id="estacion2I" role="tabpanel" aria-labelledby="estacion2I-tab">
          <div class="row g-4 py-5 row-cols-1 row-cols-lg-3 px-5">
            <?php

            if ($result3_a2) {

              while ($datos = pg_fetch_row($result3_a2, null, PGSQL_ASSOC)) {


            ?>
                <div class="feature col bg-light">

                  <h4><?php echo $datos['nombre'] ?></h4>
                  <canvas id="<?php echo identificador($datos['nombre']), 2 ?>" width="100" height="100"></canvas>

                </div>
            <?php
              }
            }

            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-light mb-3 py-3">
    <h3 class="py-3 text-center mb-1">Contador de incidencias: <span style="color:teal"> Cantidad total de incidencias cometidas por cada parámetro.</h3>
    <div style="margin: 10px 70px 20px 70px">
      <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active text-dark" id="estacion1C-tab" data-bs-toggle="tab" data-bs-target="#estacion1C" type="button" role="tab" aria-controls="estacion1C" aria-selected="true">Estación 1</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link text-dark" id="estacion2C-tab" data-bs-toggle="tab" data-bs-target="#estacion2C" type="button" role="tab" aria-controls="estacion2C" aria-selected="false">Estación 2</button>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="estacion1C" role="tabpanel" aria-labelledby="estacion1C-tab">
          <div class="row row-cols-1 row-cols-md-3 mb-3 text-center" width="100" height="100">
            <div class="col">
            </div>
            <div class="col">
              <canvas class="center" id="incidencias1" width="400" height="100"></canvas>
            </div>
          </div>

        </div>
        <div class="tab-pane fade" id="estacion2C" role="tabpanel" aria-labelledby="estacion2C-tab">
          <div class="row row-cols-1 row-cols-md-3 mb-3 text-center" width="100" height="100">
            <div class="col">
            </div>
            <div class="col">
              <canvas class="center" id="incidencias2" width="400" height="100"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer>
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">

    </ul>
    <p class="text-center text-muted">© 2022 Safe Worker Security Monitor, Inc</p>
  </footer>

</main>
</div>
</div>

<script src="node_modules/chart.js/dist/chart.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<!-- Option 2: Separate Popper and Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js" crossorigin="anonymous"></script>

<!-- Gráficos -->
<script>
  cargarDatosTemperatura1();
  cargarDatosSonido1();
  cargarDatosMonoxido1();
  cargarDatosDioxido1();
  cargarDatosTolueno1();
  cargarDatosAmoniaco1();
  cargarDatosIncidencias1();

  cargarDatosTemperatura2();
  cargarDatosSonido2();
  cargarDatosMonoxido2();
  cargarDatosDioxido2();
  cargarDatosTolueno2();
  cargarDatosAmoniaco2();
  cargarDatosIncidencias2();

  function cargarDatosTemperatura1() {
    $.ajax({
      url: 'arduino1/temperatura-a1.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Temperatura', 'temperatura1')
      }
    })
  }

  function cargarDatosSonido1() {
    $.ajax({
      url: 'arduino1/sonido-a1.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Sonido', 'sonido1')
      }
    })
  }

  function cargarDatosMonoxido1() {
    $.ajax({
      url: 'arduino1/monoxido-a1.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Monoxido', 'monoxido1')
      }
    })
  }

  function cargarDatosDioxido1() {
    $.ajax({
      url: 'arduino1/dioxido-a1.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Dioxido', 'dioxido1')
      }
    })
  }

  function cargarDatosTolueno1() {
    $.ajax({
      url: 'arduino1/tolueno-a1.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Tolueno', 'tolueno1')
      }
    })
  }

  function cargarDatosAmoniaco1() {
    $.ajax({
      url: 'arduino1/amoniaco-a1.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Amoniaco', 'amoniaco1')
      }
    })
  }

  function cargarDatosIncidencias1() {
    $.ajax({
      url: 'arduino1/incidencias-contador-a1.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][1]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'doughnut', 'Contador de incidencias', 'incidencias1')
      }
    })
  }

  ///////////////////////// ///////////////////////////////////////// //////////////////////////////

  function cargarDatosTemperatura2() {
    $.ajax({
      url: 'arduino2/temperatura-a2.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Temperatura', 'temperatura2')
      }
    })
  }

  function cargarDatosSonido2() {
    $.ajax({
      url: 'arduino2/sonido-a2.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Sonido', 'sonido2')
      }
    })
  }

  function cargarDatosMonoxido2() {
    $.ajax({
      url: 'arduino2/monoxido-a2.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Monoxido', 'monoxido2')
      }
    })
  }

  function cargarDatosDioxido2() {
    $.ajax({
      url: 'arduino2/dioxido-a2.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Dioxido', 'dioxido2')
      }
    })
  }

  function cargarDatosTolueno2() {
    $.ajax({
      url: 'arduino2/tolueno-a2.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Tolueno', 'tolueno2')
      }
    })
  }

  function cargarDatosAmoniaco2() {
    $.ajax({
      url: 'arduino2/amoniaco-a2.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][2]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'line', 'Amoniaco', 'amoniaco2')
      }
    })
  }

  function cargarDatosIncidencias2() {
    $.ajax({
      url: 'arduino2/incidencias-contador-a2.php',
      type: 'POST'

    }).done(function(resp) {
      if (resp.length > 0) {
        var titulo = [];
        var cantidad = [];
        var colores = [];
        var data = JSON.parse(resp);
        for (var i = 0; i < data.length; i++) {
          titulo.push(data[i][0]);
          cantidad.push(data[i][1]);
          colores.push(colorRGB());
        }
        crearGrafico(titulo, cantidad, colores, 'doughnut', 'Contador de incidencias', 'incidencias2')
      }
    })
  }


  ////////////////////////////////////////////////////////////////////////////////////////////////

  function crearGrafico(titulo, cantidad, colores, tipo, encabezado, id) {
    const ctx = document.getElementById(id).getContext('2d');
    const myChart = new Chart(ctx, {
      type: tipo,
      data: {
        labels: titulo,
        datasets: [{
          label: encabezado,
          data: cantidad,
          backgroundColor: colores,
          borderColor: colores,
          borderWidth: 1
        }]
      },
      options: {
        animation: {
          duration: 0
        },
        responsive: true,
        layout: {
          padding: {
            right: 20,
            left: 20,
            bottom: 30
          }
        },
      }
    });

  }

  function generarNumero(numero) {
    return (Math.random() * numero).toFixed(0);
  }

  function colorRGB() {
    var coolor = "(" + generarNumero(255) + "," + generarNumero(255) + "," + generarNumero(255) + ")";
    return "rgb" + coolor;
  }
</script>

<script type="text/javascript">
  $(document).ready(function() {
    setInterval(
      function() {
        $('#tiempo-real-a1').load('tiempo-real-a1.php');
      }, 500
    );
  })
</script>

<!-- Tiempo real -->
<script type="text/javascript">
  $(document).ready(function() {
    setInterval(
      function() {
        $('#tiempo-real-a2').load('tiempo-real-a2.php');
      }, 500
    );
  })
</script>

</body>

</html>