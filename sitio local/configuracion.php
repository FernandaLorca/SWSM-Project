<?php
//session_start();

require_once "conexion.php";
include_once "header.php";
$conexion = connectdb();

$sql = "SELECT * FROM PARAMETROS ORDER BY NOMBRE";
$result = pg_query($conexion, $sql);

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


function imagen($id)
{
  if ($id == 1) {
    $url = '../static/img/temperatura.png';
    return $url;
  } else if ($id == 2) {
    $url = '../static/img/sonido.png';
    return $url;
  } else {
    $url = '../static/img/gas6.png';
    return $url;
  }
}

if (isset($_GET['msg'])) {
  if ($_GET['msg'] == 0) {
?>
    <div class="mb-3">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol></svg>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
      <?php echo 'Modificación exitosa.' ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    </div>
  <?php
  } else {
  ?>
  <div class="mb-3">
  <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
  <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>
    <div class="alert alert-danger alert-dismissible fade show">
    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
      <?php echo 'Modificación fallida.' ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    </div>
<?php
  }
}

?>
<div class="container-md">
  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap mb-3 border-bottom">
    <h3>
      Safe Worker Security Monitor <span style="color:gray">Configuración</span>
    </h3>
  </div>



  <div class="border bg-light round py-2 mb-5">
    <h4 class="px-4 py-2" align="left">Rangos actuales: <span style="color:teal"> Valores establecidos para cada parámetro.</h4>
    <p class=" px-4 " align="left">La tabla muestra los valores máximo y mínimo configurados para cada parámetro. Para modificar algún rango en específico,  seleccione el botón editar correspondiente.</p>
  </div>


  <div class="table table-responsive">
    <table class="table table-responsive table-bordered" id="tabla">
      <thead >
        <tr>
          <th scope="col" >Parámetro</th>
          <th scope="col">Mínimo</th>
          <th scope="col" >Máximo</th>
          <th scope="col" >Editar</th>
        </tr>
      </thead>
      <tbody>
        <?php

        if ($result) {

          while ($datos = pg_fetch_row($result, null, PGSQL_ASSOC)) {

        ?>

            <tr>

              <td >
                <?php
                echo $datos['nombre'];
                ?>
              </td>
              <td id="valorMinimo">
                <?php echo $datos['minimo'], " ", medidas($datos['nombre']); ?>
              </td>
              <td id="valorMaximo">
                <?php echo $datos['maximo'], " ", medidas($datos['nombre']); ?>

              </td>
              <td>
                <a href="editar.php?id=<?php echo $datos['id'] ?>" class="btn btn-info btn-sm">
                  <span> <img src="/static/svg/edit.svg" alt=""> </span>
                </a>
              </td>
          <?php
          }
        }
          ?>
            </tr>
      </tbody>
    </table>

  </div>
  <div class="container" style="margin:200px auto auto auto;">
  <?php
  include_once "footer.php";
  ?>
  </div>



</div>


</div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

<!--<script type="text/javascript">
  const minimo = $('#tabla tbody').children().first().children().eq(2).text();
  const val_min = parseInt(minimo);
  function actualizarDatos(nombre1) {
    $.ajax({
      data: {nombre: "nombre1"},
      dataType: "json",
      type: "POST",
      url: "/procesos/obtener.php",
      /*success: function (r) {
        datos = jQuery.parseJSON(r);
        
        ('#valorMin').val(datos['minimo']);
        ('#valorMax').val(datos['maximo']);
        
        console.log(r);
        //alert("choripan")


      }*/
      beforeSend: function(){
        console.log("valor minimo temperatura: " + val_min)
      }
      
    })
  }

  $("#btnActualizar").on('click', function(){  
    let minimo1 = document.getElementById("valorMin").value;
    let maximo = document.getElementById("valorMax").value;

    console.log(minimo1);
    console.log(maximo);

    let nuevoMinimo = document.getElementById("valorMinimo")
    let nuevoMaximo = document.getElementById("valorMaximo")

    //$("#tabla").attr("valorMinimo").;

  })
  

</script>-->


</html>