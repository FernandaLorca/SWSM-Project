<?php

session_start();
//$_SESSION ['id'] = 0;

include_once "conexion.php";
include_once "header.php";
$conexion = connectdb();

if (isset($_POST['modificar'])) {
    $id0 = $_POST['id'];
    $nMinimo = $_POST['mMinimo'];
    $nMaximo = $_POST['mMaximo'];

    $sql = "UPDATE PARAMETROS SET MINIMO = '$nMinimo', MAXIMO = '$nMaximo' WHERE ID = '$id0' ";
    $result = pg_query($conexion, $sql);

    //$_SESSION ['id'] = $id0;

    if ($result) {
        header('Location:configuracion.php?msg=0');
    } else {
        header('Location:configuracion.php?msg=1');
    }
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM PARAMETROS WHERE ID= '$id'";
    $result = pg_query($conexion, $sql);
}

?>

<div class="container-md">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap mb-3 border-bottom">
        <h3>
            Safe Worker Security Monitor <span style="color:gray">Modificación</span>
        </h3>
    </div>



    <div class="border bg-light round py-2 mb-5">
        <h4 class="px-4 py-2" align="left">Actualización de valores: <span style="color:teal"> Edición de rangos configurados.</h4>
        <p class=" px-4 " align="left">Digite los nuevos valores mínimo y/o máximo. Considere que los valores ingresados deben ser coherentes,
            es decir, el nuevo valor mínimo no debe superar el actual o nuevo valor máximo, además de que ambos deben ser positivos y no nulos.
            También tenga en cuenta los rangos expuestos a continuación que denotan los límites alcanzables por los sensores.</p>
        <p class=" px-4 " align="left">Amoníaco (NH3) -> [10ppm ~ 1000ppm]</p>
        <p class=" px-4 " align="left">Dióxido de Carbono (CO2) -> [10ppm ~ 1000ppm]</p>
        <p class=" px-4 " align="left">Monóxido de carbono (CO) -> [10ppm ~ 1000ppm]</p>
        <p class=" px-4 " align="left">Sonido -> [20dB ~ 100dB]</p>
        <p class=" px-4 " align="left">Temperatura -> [0ºC ~ 100ºC]</p>
        <p class=" px-4 " align="left">Tolueno (C7H8) -> [10ppm ~ 1000ppm]</p>
        <p class=" px-4 " align="left"></p>
    </div>

    <div class="row justify-content-center">
    <div class="col-12">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="input-group mb-3">

            <?php

            if ($result) {
                while ($dato = pg_fetch_row($result, null, PGSQL_ASSOC)) {

            ?>
                    <input type="hidden" min=0 step=".01" class="form-control " name="id" id="id" value="<?php echo $dato['id']; ?>" required>

                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Parámetro</span>
                        <input type="text" class="form-control" readonly="readonly" placeholder="<?php echo $dato['nombre']; ?>">
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Valor máximo</span><input type="number" min=0 step=".01" placeholder="Valor mínimo" class="form-control " name="mMinimo" id="mMinimo" value="<?php echo $dato['minimo']; ?>" required>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Valor mínimo</span>
                        <input type="number" min=0 placeholder="Valor máximo" class="form-control " name="mMaximo" id="mMaximo" value="<?php echo $dato['maximo']; ?>" required>
                    </div>


            <?php
                }
            }

            ?>



        </div>

        <div class="d-grid py-2 gap-2">
            <input type="submit" name="modificar" value="Modificar" class="btn btn-info"></input>
        </div>

    </form>
    </div>
    </div>

</div>



<div class="container" style="margin:80px auto auto auto;">
    <?php
    include_once "footer.php";
    ?>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>