<?php

    require_once "conexion-a1.php";
 
    function traerDatos(){
        $conexion = connectdb1();
        $arr = array();
        $sql = "SELECT * FROM INCIDENCIAS_AMONIACO";
        $result = pg_query($conexion, $sql);
        //$datos = pg_fetch_row($result, null, PGSQL_ASSOC);
        while ($datos = pg_fetch_array($result, null, PGSQL_NUM)) {
            $arr[] = $datos; 
        }

        return  $arr;
    }

    $consulta = traerDatos();
    echo json_encode($consulta);
    
?>