<?php

require_once "header.php";
require_once "arduino2/conexion-a2.php";
require_once "arduino1/conexion-a1.php";

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

if (isset($_GET['parametro'])) {
  $conexion1 = connectdb1();
  $conexion2 = connectdb2();
  $parametro = $_GET['parametro'];
  $medida = medidas($parametro);
  if ($parametro == 'Dioxido de carbono (CO2)') {
    $p = 'dioxido';
  } else if ($parametro == 'Tolueno (C7H8)') {
    $p = 'tolueno';
  } else if ($parametro == 'Monoxido de carbono (CO)') {
    $p = 'monoxido';
  } else if ($parametro == 'Amoniaco (NH3)') {
    $p = 'amoniaco';
  } else {
    $p = $parametro;
  }
}

  $sql1 = "SELECT * FROM INCIDENCIAS_$p";
  $result1_a1 = pg_query($conexion1, $sql1);
  $result1_a2 = pg_query($conexion2, $sql1);

?>
  <style>
    #chart-wrapper {
      margin: auto auto 70px auto;
      width: 45%;
      padding: 10px;
      bottom: 10px;
    }
  </style>

  <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
      <h2>
      <span>
        <img src="/static/svg/logo.svg" alt="" width="3.4%">
      </span>
        Safe Worker Security Monitor <span style="color:gray">Incidencias</span>
      </h2>

    </div>

    <div class="bg-light mb-3">
      <h3 class="py-5 text-center">Incidencias de <?php echo $parametro; ?>: <span style="color:teal"> Ocasiones en las que no se ha cumplido los rangos establecidos.</h3>
      <div style="margin: 10px 70px 20px 70px">
      <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active text-dark" id="estacion1-tab" data-bs-toggle="tab" data-bs-target="#estacion1" type="button" role="tab" aria-controls="estacion1" aria-selected="true" onclick="cargarDatos1('<?php echo $parametro; ?>')">Estación 1</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link text-dark" id="estacion2-tab" data-bs-toggle="tab" data-bs-target="#estacion2" type="button" role="tab" aria-controls="estacion2" aria-selected="false" onclick="cargarDatos2('<?php echo $parametro; ?>')">Estación 2</button>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="estacion1" role="tabpanel" aria-labelledby="estacion1-tab">
          <div class="row g-4 py-3 row-cols-1 row-cols-lg-1">
            <div id="chart-wrapper">
              <canvas id="<?php echo identificador($parametro), 1; ?>" width="400" height="400"></canvas>
            </div>
          </div>


            <div class="row justify-content-center">
            <div class="col-10 text-center">
            <div class="table table-responsive">
              <table class="table table-responsive table-bordered" id="tabla">
                <thead>
                  <tr>
                  <th scope="col" class="text-dark">ID</th>
                    <th scope="col" class="text-dark">Fecha y hora</th>
                    <th scope="col" class="text-dark">Valor</th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  if ($result1_a1) {

                    while ($datos = pg_fetch_row($result1_a1, null, PGSQL_ASSOC)) {

                  ?>

                      <tr class="text-dark">
                      <td class="text-dark">
                          <?php
                          echo $datos['id'];
                          ?>
                        </td>

                        <td class="text-dark">
                          <?php
                          echo $datos['fecha_y_hora'];
                          ?>
                        </td>
                        <td class="text-dark">
                          <?php echo $datos['valor'], " ", $medida ?>
                        </td>
                        


                    <?php
                    }
                  }

                    ?>
                      </tr>
                </tbody>
              </table>
            </div>
            </div>
            </div>


          </div>

        <div class="tab-pane fade" id="estacion2" role="tabpanel" aria-labelledby="estacion2-tab">
          <div class="row g-4 py-3 row-cols-1 row-cols-lg-3">
            <div id="chart-wrapper">
              <canvas id="<?php echo identificador($parametro), 2; ?>" width="400" height="400"></canvas>
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-10 text-center">
            <div class="table table-responsive">
              <table class="table table-responsive table-bordered" id="tabla">
                <thead>
                  <tr>
                  <th scope="col" class="text-dark">ID</th>
                    <th scope="col" class="text-dark">Fecha y hora</th>
                    <th scope="col" class="text-dark">Valor</th>
                  </tr>
                </thead>
                <tbody>
                  <?php

                  if ($result1_a2) {

                    while ($datos = pg_fetch_row($result1_a2, null, PGSQL_ASSOC)) {

                  ?>

                      <tr class="text-dark">
                      <td class="text-dark">
                          <?php
                          echo $datos['id'];
                          ?>
                        </td>

                        <td class="text-dark">
                          <?php
                          echo $datos['fecha_y_hora'];
                          ?>
                        </td>
                        <td class="text-dark">
                          <?php echo $datos['valor'], " ", $medida ?>
                        </td>
                        


                    <?php
                    }
                  }

                    ?>
                      </tr>
                </tbody>
              </table>
            </div>
            </div>
            </div>





          </div>
        </div>
      </div>
      </div>


    <?php

    require_once "footer.php"

    ?>

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



    cargarDatos1('<?php echo $parametro; ?>');

    function cargarDatos1($parametro) {
      $p = $parametro;
      $url = '';
      $id = '';
      if ($p == 'Temperatura') {
        $url = 'arduino1/temperatura-a1.php';
        $id = 'temperatura1';
      } else if ($p == 'Sonido') {
        $url = 'arduino1/sonido-a1.php';
        $id = 'sonido1';
      } else if ($p == 'Monoxido de carbono (CO)') {
        $url = 'arduino1/monoxido-a1.php';
        $id = 'monoxido1';
      } else if ($p == 'Dioxido de carbono (CO2)') {
        $url = 'arduino1/dioxido-a1.php';
        $id = 'dioxido1';
      } else if ($p == 'Tolueno (C7H8)') {
        $url = 'arduino1/tolueno-a1.php';
        $id = 'tolueno1';
      } else if ($p == 'Amoniaco (NH3)') {
        $url = 'arduino1/amoniaco-a1.php';
        $id = 'amoniaco1';
      }
      $.ajax({
        url: $url,
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
          crearGrafico(titulo, cantidad, colores, 'line', $parametro, $id)
        }
      })
    }

    function cargarDatos2($parametro) {
      $p = $parametro;
      $url = '';
      $id = '';
      if ($p == 'Temperatura') {
        $url = 'arduino2/temperatura-a2.php';
        $id = 'temperatura2';
      } else if ($p == 'Sonido') {
        $url = 'arduino2/sonido-a2.php';
        $id = 'sonido2';
      } else if ($p == 'Monoxido de carbono (CO)') {
        $url = 'arduino2/monoxido-a2.php';
        $id = 'monoxido2';
      } else if ($p == 'Dioxido de carbono (CO2)') {
        $url = 'arduino2/dioxido-a2.php';
        $id = 'dioxido2';
      } else if ($p == 'Tolueno (C7H8)') {
        $url = 'arduino2/tolueno-a2.php';
        $id = 'tolueno2';
      } else if ($p == 'Amoniaco (NH3)') {
        $url = 'arduino2/amoniaco-a2.php';
        $id = 'amoniaco2';
      }
      $.ajax({
        url: $url,
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
          crearGrafico(titulo, cantidad, colores, 'line', $parametro, $id)
        }
      })
    }
    let myChart
    function crearGrafico(titulo, cantidad, colores, tipo, encabezado, id) {
      var ctx = document.getElementById(id).getContext('2d');
      if (myChart) {
        myChart.destroy();
    }
       myChart = new Chart(ctx, {
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

  </body>

  </html>