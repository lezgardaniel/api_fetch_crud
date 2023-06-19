<?php
require('../datos/ConexionBD.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Denuncias</title>
  <style>
    td{ text-align: center; }
  </style>
</head>

<body>
  <a href="index.php">Volver</a>
  <h2>SISTEMA DE DENUNCIAS Y ATENCION A VICTIMAS</h2>
  
  <section class="crud">
    <article>
      <h2 class="crud-title">Agregar Denuncia</h2>

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
          <input type="submit" value="Enviar">
          <input type="hidden" name="idDenuncia">
        </div>
      </form>
    </article>

    <article>
      <h2>Lista de Denuncias</h2>
      
      <table class="crud-table">
        <thead>
          <tr>
            <td>N°</td>
            <td>Hechos</td>
            <td>Lugar</td>
            <td>Fecha</td>
            <td>Responsable</td>
            <td>Victima</td>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
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
      <td>
        <button class="edit">Editar</button>
        <button class="delete">Eliminar</button>
      </td>
    </tr>
  </template>

  <!-- CODIGO JavaScript API FETCH -->
  <script src="js/fetch_denuncias.js"></script>
  
</body>
</html>
