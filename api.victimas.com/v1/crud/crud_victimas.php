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
      <h2 class="crud-title">Agregar Victima</h2>

      <form class="crud-form">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="primerNombre">Primer nombre</label>
            <input name="primerNombre" type="text" class="form-control" placeholder="Escribe el primer nombre" required>
          </div>
          <div class="form-group col-md-6">
            <label for="primerApellido">Primer apellido</label>
            <input name="primerApellido" type="text" class="form-control" placeholder="Escribe el primer apellido" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="edad">Edad</label>
            <input name="edad" type="number" class="form-control" placeholder="Escribe la edad" required>
          </div>
          <div class="form-group col-md-6">
            <label for="genero">Genero</label>
            <!-- <input name="genero" type="text" class="form-control" id="genero" placeholder="Escribe el genero" required> -->
            <select name="genero" required>
              <option value="masculino" selected>Masculino</option>
              <option value="femenino">Femenino</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="telefono">Telefono</label>
            <input name="telefono" type="text" class="form-control" placeholder="Número telefonico" required>
          </div>
          <div class="form-group col-md-6">
            <!-- <label for="idUsuario">Usuario</label>
            <input name="idUsuario" type="number" class="form-control" placeholder="Elige el ID del usuario" required> -->

            <label for="idUsuario">Usuario</label>
            <select id="idUsuario" name="idUsuario" class="form-control" required>
            <option value="">Selecciona el usuario</option>
              <?php
              $sql = "SELECT idUsuario, nombre FROM usuario";
              $query = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($sql);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              if ($query->rowCount() > 0) {
                foreach ($results as $result) { ?>
                  <option value="<?php echo $result->idUsuario; ?>"><?php echo $result->idUsuario; echo ". "; echo $result->nombre; ?></option>
              <?php
                }
              }
              ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="claveApi">Clave API</label>
            <input name="claveApi"  id="claveApi" type="text" class="form-control" placeholder="Clave Api de usuario">
          </div>
        </div>
        <div class="form-group">
          <input type="submit" value="Enviar">
          <input type="hidden" name="idVictima">
        </div>
      </form>
    </article>

    <article>
      <h2>Lista de Victimas</h2>

      <table class="crud-table">
        <thead>
          <tr>
            <td>ID</td>
            <td>Primer nombre</td>
            <td>Primer apellido</td>
            <td>Edad</td>
            <td>Genero</td>
            <td>Telefono</td>
            <td>Usuario</td>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </article>
  </section>
  <template id="crud-template">
    <tr>
      <td class="idVictima"></td>
      <td class="primerNombre"></td>
      <td class="primerApellido"></td>
      <td class="edad"></td>
      <td class="genero"></td>
      <td class="telefono"></td>
      <td class="idUsuario"></td>
      <td>
        <button class="edit">Editar</button>
        <button class="delete">Eliminar</button>
      </td>
    </tr>
  </template>

  <!-- CODIGO JavaScript API FETCH -->
  <script src="js/fetch_victimas.js"></script>

</body>

</html>