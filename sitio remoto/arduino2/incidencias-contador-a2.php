<?php

    require_once "conexion-a2.php";
 
    function traerDatos(){
        $conexion = connectdb2();
        $arr = array();
        $sql = "SELECT * FROM CONTADORES_INCIDENCIAS";
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