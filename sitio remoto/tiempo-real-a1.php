<?php
require_once "arduino1/conexion-a1.php";
require_once "arduino2/conexion-a2.php";
$conexion1 = connectdb1();
$conexion2 = connectdb2();

$sql2 = "SELECT p.NOMBRE, p.MINIMO, p.MAXIMO, t.VALOR FROM PARAMETROS AS p, TIEMPO_REAL AS t WHERE p.NOMBRE = t.PARAMETRO ORDER BY PARAMETRO";
$result2_a1 = pg_query($conexion1, $sql2);
$result2_a2 = pg_query($conexion2, $sql2);

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

<div class="row g-4 py-3 row-cols-1 row-cols-lg-3">

    <?php

    if ($result2_a1) {

        while ($datos = pg_fetch_row($result2_a1, null, PGSQL_ASSOC)) {


    ?>
            <div class="feature col px-5">
                <h4 style="color:darkblue"><?php echo $datos['nombre']; ?></h4>
                <!--<p>Rango: <?php echo $datos['minimo'], ' ', medidas($datos['nombre']); ?> - <?php echo $datos['maximo'], ' ', medidas($datos['nombre']); ?> </p>-->
                <p></p>
                <h3 class=""><?php echo $datos['valor'], ' ', medidas($datos['nombre']); ?></h3>
            </div>

    <?php
        }
    }

    ?>
</div>