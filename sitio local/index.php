<?php
include_once "header.php";

require_once "conexion.php";
$conexion = connectdb();

$sql = "SELECT p.NOMBRE, p.MINIMO, p.MAXIMO, t.VALOR FROM PARAMETROS AS p, TIEMPO_REAL AS t WHERE p.NOMBRE = t.PARAMETRO ORDER BY PARAMETRO";
$result = pg_query($conexion, $sql);

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
?>


<main>
  <div style="margin: 0px 30px 0 30px">
    <!-- Título
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap mb-3 border-bottom"> 
    <h3>
      Safe Worker Security Monitor <span style="color:gray">Mediciones en tiempo real</span>
    </h3>

  </div> -->

    <div id="tiempo-real">
      
    </div>

  </div>

  </div>
  </div>

  </body>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>


<script type="text/javascript">
  $(document).ready(function() {
    setInterval(
      function() {
        $('#tiempo-real').load('tiempo-real.php');
      }, 200
      );
  })
</script> 

  </html>