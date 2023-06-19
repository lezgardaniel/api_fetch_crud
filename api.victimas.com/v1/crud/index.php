<?php
require('../datos/ConexionBD.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Sistema de Denuncias</title>
  <!-- Bootstrap core CSS -->
  <link href="dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom styles for this template -->
  <link href="assets/sticky-footer-navbar.css" rel="stylesheet">
  <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
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
          <li class="nav-item"> <a class="nav-link" href="crud_victimas.php">Victimas </a> </li>
        </ul>
        <ul class="navbar-nav mr-5">
          <li class="nav-item"> <a class="nav-link" href="crud_denuncias.php">Denuncias </a> </li>
        </ul>
        <form class="form-inline mt-2 mt-md-0">
          <input class="form-control mr-sm-2" type="text" placeholder="Buscar" aria-label="Search">
          <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Busqueda</button>
        </form>
      </div>
    </nav>
  </header>

  <div class="container">
    <h3 class="mt-2 text-center"><img src="img/patrulla.gif" width="100px" height="70px"> SISTEMA DE DENUNCIAS Y ATENCION A VICTIMAS</h3>
    <section class="crud">
      <article>
        <h4 class="crud-title text-center font-weight-bold" style="color: #2F4F4F;">Agregar Denuncia</h4>

        <!-- FORMULARIO -->
        <div class="col-12 col-md-12">
          <form class="crud-form">
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
              <div class="form-group col-md-4">
                <label for="fecha">Fecha</label>
                <input name="fecha" type="date" class="form-control" id="fecha" placeholder="Fecha" required>
              </div>
              <div class="form-group col-md-4">
                <label for="responsable">Responsable</label>
                <input name="responsable" type="text" class="form-control" id="responsable" placeholder="Responsable" required>
              </div>
              <div class="form-group col-md-4">
                <label for="idVictima">Victima</label>
                <select id="idVictima" name="idVictima" class="form-control" required>
                  <option value="">Selecciona una victima</option>
                  <?php
                  $sql = "SELECT idVictima, primerNombre FROM victima";
                  $query = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($sql);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  if ($query->rowCount() > 0) {
                    foreach ($results as $result) { ?>
                      <option value="<?php echo $result->idVictima; ?>">
                        <?php
                        echo $result->idVictima;
                        echo ". ";
                        echo $result->primerNombre;
                        ?>
                      </option>
                  <?php
                    }
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="form-group">
              <input class="btn btn-success btn-block" type="submit" value="Enviar">
              <input type="hidden" name="idDenuncia">
            </div>
          </form>
        </div>
      </article>

      <hr>
      <article>
        <h4 class="text-center font-weight-bold" style="color: #2F4F4F;">Lista de Denuncias</h4>
        <div style="float:left;">
          <!--GENERAR REPORTE PFF-->
          <a href="reporte.php" target="_blank" class="btn btn-warning mb-2">Generar reporte</a>
        </div>
        <div class="table-responsive">
          <table class="crud-table table table-bordered table-striped">
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
            </tbody>
          </table>
        </div>
      </article>
    </section>

    <template id="crud-template">
      <tr>
        <td class="idDenuncia"></td>
        <td class="hechos"></td>
        <td class="lugar"></td>
        <td class="fecha"></td>
        <td class="responsable"></td>
        <td class="idVictima"></td>
        <td><button class="edit btn btn-info">Editar</button></td>
        <td><button class="delete btn btn-danger">Eliminar</button></td>
      </tr>
    </template>
  </div>

  <footer class="footer">
    <div class="container"> <span class="text-muted">
        <p align="center">© 2023 Copyright: Sistema de Denuncias</a></p>
      </span> </div>
  </footer>

  <!-- CODIGO JavaScript API FETCH -->
  <script src="js/fetch_denuncias.js"></script>

</body>
</html>