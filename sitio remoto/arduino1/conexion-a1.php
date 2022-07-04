<?php
 
    function connectdb1(){
        $conexion = pg_connect("user=postgres
                                password=root
                                host=localhost
                                dbname=arduino1"
                            ) or die( "Error al conectar: ".pg_last_error() );
        return $conexion;
    }
    
?>