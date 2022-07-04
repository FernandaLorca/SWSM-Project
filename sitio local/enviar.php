<?php
require_once "conexion.php";
if (isset($_GET['msg'])) { //msg=1
    $conexion = connectdb();

    $sql = "SELECT ID, MINIMO, MAXIMO FROM PARAMETROS ORDER BY ID";
    $result = pg_query($conexion, $sql);
    

    $valores = '0';

    if ($result) {
        //$i = 1;
        while ($datos = pg_fetch_row($result, null, PGSQL_ASSOC)) {
            if ($datos['id'] == 1) {
                $valores = $valores . "|" . $datos['minimo'] . "|" . $datos['maximo'];
                //$i++;
            } else {

                $valores = $valores . "|" . $datos['maximo'];
            }
        }
    }
    echo $valores;
}
