<?php
    require_once "conexion-a2.php";
    $conexion = connectdb2();
    date_default_timezone_set('America/Santiago');

    $consulta = "SELECT minimo, maximo FROM parametros WHERE nombre = 'Temperatura';";
    $resultado = pg_query($conexion, $consulta);
    $fila = pg_fetch_row($resultado);
    $Temp_MIN = $fila[0];
    $Temp_MAX = $fila[1];

    $consulta = "SELECT maximo FROM parametros WHERE nombre = 'Sonido';";
    $resultado = pg_query($conexion, $consulta);
    $fila = pg_fetch_row($resultado);
    $Noise_MAX = $fila[0];

    $consulta = "SELECT maximo FROM parametros WHERE nombre = 'Dioxido de carbono (CO2)';";
    $resultado = pg_query($conexion, $consulta);
    $fila = pg_fetch_row($resultado);
    $Diox_MAX = $fila[0];

    $consulta = "SELECT maximo FROM parametros WHERE nombre = 'Monoxido de carbono (CO)';";
    $resultado = pg_query($conexion, $consulta);
    $fila = pg_fetch_row($resultado);
    $Monox_MAX = $fila[0];

    $consulta = "SELECT maximo FROM parametros WHERE nombre = 'Tolueno (C7H8)';";
    $resultado = pg_query($conexion, $consulta);
    $fila = pg_fetch_row($resultado);
    $Tolu_MAX = $fila[0];

    $consulta = "SELECT maximo FROM parametros WHERE nombre = 'Amoniaco (NH3)';";
    $resultado = pg_query($conexion, $consulta);
    $fila = pg_fetch_row($resultado);
    $Amon_MAX = $fila[0];

    if(isset($_POST)){
        $Temp_value = $_POST['Temp_value'];
        $Noise_value = $_POST['Noise_value'];
        $Diox_value = $_POST['Diox_value'];
        $Monox_value = $_POST['Monox_value'];
        $Tolu_value = $_POST['Tolu_value'];
        $Amon_value = $_POST['Amon_value'];
        $alarm = $_POST['alarm'];   
        print($_POST['Temp_value']);
            if(!empty($Temp_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Temp_value WHERE parametro = 'Temperatura';";
                $resultado = pg_query($conexion, $consulta);
                
                if($Temp_value >= $Temp_MAX || $Temp_value <= $Temp_MIN){
                    $consulta = "SELECT * FROM contadores_incidencias WHERE parametro='Temperatura';";
                    $resultado = pg_query($conexion, $consulta);
                    $fila = pg_fetch_row($resultado);
                    $id = $fila[1] + 1;
                    $fechayhora = date('d/m/Y h:i:s a', time());
                    $consulta = "INSERT INTO incidencias_temperatura (fecha_y_hora, valor) VALUES ('$fechayhora', '$Temp_value');";
                    $resultado = pg_query($conexion, $consulta);
                    $consulta = "UPDATE contadores_incidencias SET valor = $id WHERE parametro = 'Temperatura';";
                    $resultado = pg_query($conexion, $consulta);
                }
            }if(!empty($Noise_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Noise_value WHERE parametro = 'Sonido';";
                $resultado = pg_query($conexion, $consulta);
                if($Noise_value >= $Noise_MAX){
                    $consulta = "SELECT * FROM contadores_incidencias WHERE parametro='Sonido';";
                    $resultado = pg_query($conexion, $consulta);
                    $fila = pg_fetch_row($resultado);
                    $id = $fila[1] + 1;
                    $fechayhora = date('d/m/Y h:i:s a', time());
                    $consulta = "INSERT INTO incidencias_sonido (fecha_y_hora, valor) VALUES ('$fechayhora', '$Noise_value');";
                    $resultado = pg_query($conexion, $consulta);
                    $consulta = "UPDATE contadores_sonido SET valor = $id WHERE parametro = 'Sonido';";
                    $resultado = pg_query($conexion, $consulta);
                }
            }if(!empty($Diox_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Diox_value WHERE parametro = 'Dioxido de carbono (CO2)';";
                $resultado = pg_query($conexion, $consulta);
                if($Diox_value >= $Diox_MAX){
                    $consulta = "SELECT * FROM contadores_incidencias WHERE parametro='Dioxido de carbono (CO2)';";
                    $resultado = pg_query($conexion, $consulta);
                    $fila = pg_fetch_row($resultado);
                    $id = $fila[1] + 1;
                    $fechayhora = date('d/m/Y h:i:s a', time());
                    $consulta = "INSERT INTO incidencias_dioxido (fecha_y_hora, valor) VALUES ('$fechayhora', '$Diox_value');";
                    $resultado = pg_query($conexion, $consulta);
                    $consulta = "UPDATE contadores_incidencias SET valor = $id WHERE parametro = 'Dioxido de carbono (CO2)';";
                    $resultado = pg_query($conexion, $consulta);
                }
            }if(!empty($Monox_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Monox_value WHERE parametro = 'Monoxido de carbono (CO)';";
                $resultado = pg_query($conexion, $consulta);
                if($Monox_value >= $Monox_MAX){
                    $consulta = "SELECT * FROM contadores_incidencias WHERE parametro='Monoxido de carbono (CO)';";
                    $resultado = pg_query($conexion, $consulta);
                    $fila = pg_fetch_row($resultado);
                    $id = $fila[1] + 1;
                    $fechayhora = date('d/m/Y h:i:s a', time());
                    $consulta = "INSERT INTO incidencias_monoxido (fecha_y_hora, valor) VALUES ('$fechayhora', '$Monox_value');";
                    $resultado = pg_query($conexion, $consulta);
                    $consulta = "UPDATE contadores_incidencias SET valor = $id WHERE parametro = 'Monoxido de carbono (CO)';";
                    $resultado = pg_query($conexion, $consulta);
                }
            }if(!empty($Tolu_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Tolu_value WHERE parametro = 'Tolueno (C7H8)';";
                $resultado = pg_query($conexion, $consulta);
                if($Tolu_value >= $Tolu_MAX){
                    $consulta = "SELECT * FROM contadores_incidencias WHERE parametro='Tolueno (C7H8)';";
                    $resultado = pg_query($conexion, $consulta);
                    $fila = pg_fetch_row($resultado);
                    $id = $fila[1] + 1;
                    $fechayhora = date('d/m/Y h:i:s a', time());
                    $consulta = "INSERT INTO incidencias_tolueno (fecha_y_hora, valor) VALUES ('$fechayhora', '$Tolu_value');";
                    $resultado = pg_query($conexion, $consulta);
                    $consulta = "UPDATE contadores_incidencias SET valor = $id WHERE parametro = 'Tolueno (C7H8)';";
                    $resultado = pg_query($conexion, $consulta);
                }
            }if(!empty($Amon_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Amon_value WHERE parametro = 'Amoniaco (NH3)';";
                $resultado = pg_query($conexion, $consulta);
                if($Amon_value >= $Amon_MAX){
                    $consulta = "SELECT * FROM contadores_incidencias WHERE parametro='Amoniaco (NH3)';";
                    $resultado = pg_query($conexion, $consulta);
                    $fila = pg_fetch_row($resultado);
                    $id = $fila[1] + 1;
                    $fechayhora = date('d/m/Y h:i:s a', time());
                    $consulta = "INSERT INTO incidencias_amoniaco (fecha_y_hora, valor) VALUES ('$fechayhora', '$Amon_value');";
                    $resultado = pg_query($conexion, $consulta);
                    $consulta = "UPDATE contadores_incidencias SET valor = $id WHERE parametro = 'Amoniaco (NH3)';";
                    $resultado = pg_query($conexion, $consulta);
                }
            }
    }
?>