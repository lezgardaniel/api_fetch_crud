<?php
require('../datos/ConexionBD.php');
require('../controladores/denuncias.php');
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>Sistema de Denuncias</title>

  <!-- Bootstrap core CSS -->
  <link href="dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="assets/sticky-footer-navbar.css" rel="stylesheet">
  <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>

  <script type="text/javascript">
    $(document).ready(function() {
      setTimeout(function() {
        $(".content").fadeOut(1500);
      }, 3000);

    });
  </script>
</head>

<body>
  <header>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark"> <a class="navbar-brand" href="#">SSP</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active"> <a class="nav-link" href="index.php">Inicio <span class="sr-only">(current)</span></a> </li>
        </ul>
        <ul class="navbar-nav mr-5">
          <li class="nav-item"> <a class="nav-link" href="http://localhost/api.victimas.com/v1/victimas">Victimas </a> </li>
        </ul>
        <ul class="navbar-nav mr-5">
          <li class="nav-item"> <a class="nav-link" href="http://localhost/api.victimas.com/v1/usuarios">Usuarios </a> </li>
        </ul>
        <form class="form-inline mt-2 mt-md-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Buscar" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Busqueda</button>
        </form>
      </div>
    </nav>
  </header>

  <!-- Begin page content -->

  <div class="container">
    <?php

    if (isset($_POST['eliminar'])) {

      ////////////// Actualizar la tabla /////////
      $consulta = "DELETE FROM " . denuncias::NOMBRE_TABLA .
        " WHERE " . denuncias::ID_DENUNCIA . "=:idDenuncia";
      $sql = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
      $sql->bindParam(':idDenuncia', $id, PDO::PARAM_INT);
      $id = trim($_POST['idDenuncia']);

      $sql->execute();

      if ($sql->rowCount() > 0) {
        $count = $sql->rowCount();
        echo "<div class='content alert alert-warning' > 
        $count registro ha sido eliminado  </div>";
      } else {
        echo "<div class='content alert alert-danger'> No se pudo eliminar el registro  </div>";

        print_r($sql->errorInfo());
      }
    } // Cierra envio de guardado
    ?>

    <?php

    if (isset($_POST['insertar'])) {
      ///////////// Informacion enviada por el formulario /////////////
      $hechos = $_POST['hechos'];
      $lugar = $_POST['lugar'];
      $fecha = $_POST['fecha'];
      $responsable = $_POST['responsable'];
      $idVictima = $_POST['idVictima'];
      ///////// Fin informacion enviada por el formulario /// 

      ////////////// Insertar a la tabla la informacion generada /////////
      $sql = "INSERT INTO " . denuncias::NOMBRE_TABLA . " ( " .
        denuncias::HECHOS . "," .
        denuncias::LUGAR . "," .
        denuncias::FECHA . "," .
        denuncias::RESPONSABLE . "," .
        denuncias::ID_VICTIMA . ")" .
        " VALUES(:hechos,:lugar,:fecha,:responsable,:idVictima)";

      $sql = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($sql);

      $sql->bindParam(':hechos', $hechos, PDO::PARAM_STR, 25);
      $sql->bindParam(':lugar', $lugar, PDO::PARAM_STR, 25);
      $sql->bindParam(':fecha', $fecha, PDO::PARAM_STR, 25);
      $sql->bindParam(':responsable', $responsable, PDO::PARAM_STR, 25);
      $sql->bindParam(':idVictima', $idVictima, PDO::PARAM_STR, 25);

      $sql->execute();

      $lastInsertId = ConexionBD::obtenerInstancia()->obtenerBD()->lastInsertId();
      if ($lastInsertId > 0) {

        echo "<div class='content alert alert-primary' > Tu denuncia ha sido enviada </div>";
      } else {
        echo "<div class='content alert alert-danger'> No se pueden agregar datos, comuníquese con el administrador  </div>";

        print_r($sql->errorInfo());
      }
    } // Cierra envio de guardado
    ?>

    <?php

    if (isset($_POST['actualizar'])) {
      ///////////// Informacion enviada por el formulario /////////////
      $idDenuncia = trim($_POST['idDenuncia']);
      $hechos = trim($_POST['hechos']);
      $lugar = trim($_POST['lugar']);
      $fecha = trim($_POST['fecha']);
      $responsable = trim($_POST['responsable']);
      $idVictima = trim($_POST['idVictima']);
      ///////// Fin informacion enviada por el formulario /// 

      ////////////// Actualizar la tabla /////////
      $consulta = "UPDATE " . denuncias::NOMBRE_TABLA .
        " SET " . denuncias::HECHOS . "=:hechos," .
        denuncias::LUGAR . "=:lugar," .
        denuncias::FECHA . "=:fecha," .
        denuncias::RESPONSABLE . "=:responsable " .
        " WHERE " . denuncias::ID_DENUNCIA . "=:idDenuncia AND " . denuncias::ID_VICTIMA . "=:idVictima";

      $sql = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
      $sql->bindParam(':hechos', $hechos, PDO::PARAM_STR, 25);
      $sql->bindParam(':lugar', $lugar, PDO::PARAM_STR, 25);
      $sql->bindParam(':fecha', $fecha, PDO::PARAM_STR, 25);
      $sql->bindParam(':responsable', $responsable, PDO::PARAM_STR, 25);
      $sql->bindParam(':idVictima', $idVictima, PDO::PARAM_STR, 25);
      $sql->bindParam(':idDenuncia', $idDenuncia, PDO::PARAM_INT);

      $sql->execute();

      if ($sql->rowCount() > 0) {
        $count = $sql->rowCount();
        echo "<div class='content alert alert-primary' > 

        $count registro ha sido actualizado  </div>";
      } else {
        echo "<div class='content alert alert-danger'> No se pudo actulizar el registro  </div>";

        print_r($sql->errorInfo());
      }
    } // Cierra envio de guardado
    ?>
    <h3 class="mt-5"><img src="img/patrulla.gif" width="100px" height="70px"> SISTEMA DE DENUNCIAS Y ATENCION A VICTIMAS</h3>
    <hr>
    <div class="row">

      <!-- Insertar Registros-->
      <?php
      if (isset($_POST['formInsertar'])) { ?>
        <div class="col-12 col-md-12">
          <form action="" method="POST">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="hechos">Hechos</label>
                <input name="hechos" type="text" class="form-control" placeholder="Describe los hechos" required>
              </div>
              <div class="form-group col-md-6">
                <label for="lugar">Lugar</label>
                <input name="lugar" type="text" class="form-control" id="lugar" placeholder="Lugar de los hechos" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="fecha">Fecha</label>
                <input name="fecha" type="date" class="form-control" id="fecha" placeholder="Fecha" required>
              </div>

              <div class="form-group col-md-6">
                <label for="responsable">Responsable</label>
                <input name="responsable" type="text" class="form-control" id="responsable" placeholder="Responsable" required>
              </div>

            </div>
            <div class="form-row d-flex justify-content-center">
              <div class="form-group col-md-6">
                <label for="idVictima">Victima</label>
                <!--<input name="idVictima" type="text" class="form-control" id="idVictima" placeholder="Victima" required>-->

                <select id="idVictima" name="idVictima" class="form-control" required>
                <option value="">Selecciona una victima</option>
                  <?php
                  $sql = "SELECT idVictima, primerNombre FROM victima";
                  $query = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($sql);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);

                  if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                      <option value="<?php echo $result->idVictima; ?>"><?php echo $result->idVictima; echo ". "; echo $result->primerNombre; ?></option>
                  <?php
                    }
                  }
                  ?>
                </select>

              </div>
            </div>
            <div class="form-group">
              <button name="insertar" type="submit" class="btn btn-success  btn-block">Guardar</button>
            </div>
          </form>
        </div>
      <?php }  ?>
      <!-- Fin Insertar Registros-->


      <?php
      if (isset($_POST['editar'])) {
        $idDenuncia = $_POST['idDenuncia'];
        $sql = "SELECT * FROM denuncia WHERE idDenuncia = :idDenuncia";
        $stmt = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($sql);
        $stmt->bindParam(':idDenuncia', $idDenuncia, PDO::PARAM_INT);
        $stmt->execute();
        $obj = $stmt->fetchObject();

      ?>

        <div class="col-12 col-md-12">

          <form role="form" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>">
            <input value="<?php echo $obj->idDenuncia; ?>" name="idDenuncia" type="hidden">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="hechos">Hechos</label>
                <input value="<?php echo $obj->hechos; ?>" name="hechos" type="text" class="form-control" placeholder="Hechos">
              </div>
              <div class="form-group col-md-6">
                <label for="lugar">Lugar</label>
                <input value="<?php echo $obj->lugar; ?>" name="lugar" type="text" class="form-control" id="lugar" placeholder="Lugar">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="fecha">Fecha</label>
                <input value="<?php echo $obj->fecha; ?>" name="fecha" type="date" class="form-control" id="fecha" placeholder="Fecha">
              </div>

              <div class="form-group col-md-6">
                <label for="responsable">Responsable</label>
                <input value="<?php echo $obj->responsable; ?>" name="responsable" type="text" class="form-control" id="responsable" placeholder="Responsable">
              </div>
            </div>

            <div class="form-row d-flex justify-content-center">
              <div class="form-group col-md-6">
                <label for="idVictima">Victima</label>
                <input value="<?php echo $obj->idVictima; ?>" name="idVictima" type="text" class="form-control" id="idVictima" placeholder="Victima">
              </div>
            </div>
            <div class="form-group">
              <button name="actualizar" type="submit" class="btn btn-success  btn-block">Actualizar Registro</button>
            </div>
          </form>
        </div>
      <?php } ?>
      <div class="col-12 col-md-12">
        <!-- Contenido -->

        <div style="float:left;">
          <!--GENERAR REPORTE PFF-->
          <a href="reporte.php" target="_blank" class="btn btn-warning">Generar reporte</a>
        </div>

        <div style="float:right; margin-bottom:5px;">
          <form action="" method="post"><button class="btn btn-primary" name="formInsertar">Nuevo registro</button> <a href="index.php"><button type="button" class="btn btn-primary">Cancelar</button></a></form>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="thead-dark">
              <th width="6%">N°</th>
              <th width="22%">Hechos</th>
              <th width="18%">Lugar</th>
              <th width="15%">Fecha</th>
              <th width="21%">Responsable</th>
              <th width="8%">Victima</th>
              <th width="13%" colspan="2"></th>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM denuncia";
              $query = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($sql);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);

              if ($query->rowCount() > 0) {
                foreach ($results as $result) {
                  echo "<tr>
                        <td>" . $result->idDenuncia . "</td>
                        <td>" . $result->hechos . "</td>
                        <td>" . $result->lugar . "</td>
                        <td>" . $result->fecha . "</td>
                        <td>" . $result->responsable . "</td>
                        <td>" . $result->idVictima . "</td>
                        <td>
                        <form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>
                        <input type='hidden' name='idDenuncia' value='" . $result->idDenuncia . "'>
                        <button name='editar' class='btn btn-info'>Editar</button>
                        </form>
                        </td>

                        <td>
                        <form  onsubmit=\"return confirm('¿Estás seguro de eliminar el registro?');\" method='POST' action='" . $_SERVER['PHP_SELF'] . "'>
                        <input type='hidden' name='idDenuncia' value='" . $result->idDenuncia . "'>
                        <button name='eliminar' class='btn btn-danger'>Eliminar</button>
                        </form>
                        </td>
                        </tr>";
                }
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>


    <!-- Fin Contenido -->
  </div>
  </div>
  <!-- Fin row -->

  </div>
  <!-- Fin container -->
  <footer class="footer">
    <div class="container"> <span class="text-muted">
        <p align="center">Daniel Garcia</a></p>
      </span> </div>
  </footer>

  <!-- Bootstrap core JavaScript
    ================================================== -->
  <script src="dist/js/bootstrap.min.js"></script>
  <!-- Placed at the end of the document so the pages load faster -->
</body>

</html>