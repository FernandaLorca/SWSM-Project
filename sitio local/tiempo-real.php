<?php
require_once "conexion.php";
$conexion = connectdb();

$sql = "SELECT p.NOMBRE, p.MINIMO, p.MAXIMO, t.VALOR FROM PARAMETROS AS p, TIEMPO_REAL AS t WHERE p.NOMBRE = t.PARAMETRO ORDER BY PARAMETRO";
$result = pg_query($conexion, $sql);

function medidas($nombre){
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

<div class="d-flex justify-content-center">
        <div class="row row-cols-3 row-cols-lg-2 g-2 g-lg-3">
          <?php

          if ($result) {

            while ($datos = pg_fetch_row($result, null, PGSQL_ASSOC)) {


          ?>
              <div class="col">
                <?php
                if ($datos['valor'] >= $datos['minimo'] && $datos['valor'] <= $datos['maximo']) {
                ?>
                  <div class="py-5 px-3 border alert alert-success">
                    <h1 class="display-5" style="color:darkgreen"><?php echo $datos['nombre']; ?></h1>
                    <p class="fs-3">Rango: <?php echo $datos['minimo'], ' ', medidas($datos['nombre']); ?> - <?php echo $datos['maximo'], ' ', medidas($datos['nombre']); ?> </p>
                    <div class="py-1">
                      <h1 class="display-5"><?php echo $datos['valor'], ' ', medidas($datos['nombre']); ?></h1>
                    </div>
                  </div>
                <?php
                } else {
                ?>
                  <div class="py-5 px-3 border alert alert-danger">
                    <h1 class="display-5" style="color:brown"><?php echo $datos['nombre']; ?></h1>
                    <p class="fs-3">Rango: <?php echo $datos['minimo'], ' ', medidas($datos['nombre']); ?> - <?php echo $datos['maximo'], ' ', medidas($datos['nombre']); ?> </p>
                    <div class="py-1">
                      <h1 class="display-5"><?php echo $datos['valor'], ' ', medidas($datos['nombre']); ?></h1>
                    </div>
                  </div>
                <?php
                }
                ?>


              </div>
              <?php
            }
          }

    ?>
        </div>


      </div>