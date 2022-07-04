<?php
    require_once "conexion.php";
    $conexion = connectdb();
    date_default_timezone_set('America/Santiago');

    if(isset($_POST)){
        $Temp_value = $_POST['Temp_value'];
        $Noise_value = $_POST['Noise_value'];
        $Diox_value = $_POST['Diox_value'];
        $Monox_value = $_POST['Monox_value'];
        $Tolu_value = $_POST['Tolu_value'];
        $Amon_value = $_POST['Amon_value']; 
       
            if(!empty($Temp_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Temp_value WHERE parametro = 'Temperatura';";
                $resultado = pg_query($conexion, $consulta);
            
            }if(!empty($Noise_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Noise_value WHERE parametro = 'Sonido';";
                $resultado = pg_query($conexion, $consulta);
              
            }if(!empty($Diox_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Diox_value WHERE parametro = 'Dioxido de carbono (CO2)';";
                $resultado = pg_query($conexion, $consulta);
             
            }if(!empty($Monox_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Monox_value WHERE parametro = 'Monoxido de carbono (CO)';";
                $resultado = pg_query($conexion, $consulta);
             
            }if(!empty($Tolu_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Tolu_value WHERE parametro = 'Tolueno (C7H8)';";
                $resultado = pg_query($conexion, $consulta);
              
            }if(!empty($Amon_value)){
                $consulta = "UPDATE tiempo_real SET valor = $Amon_value WHERE parametro = 'Amoniaco (NH3)';";
                $resultado = pg_query($conexion, $consulta);
            
            }
    }
?>