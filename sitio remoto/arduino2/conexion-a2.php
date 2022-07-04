<?php
 
    function connectdb2(){
        $conexion = pg_connect("user=postgres
                                password=root
                                host=localhost
                                dbname=arduino2"
                            ) or die( "Error al conectar: ".pg_last_error() );
        return $conexion;
    }
    
?>