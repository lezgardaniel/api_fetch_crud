<?php
require('fpdf/fpdf.php');
require('../datos/ConexionBD.php');

$sql = "SELECT idDenuncia, hechos, lugar, fecha, responsable, idVictima FROM denuncia";
$query = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($sql);
$query->execute();
$result = $query->fetchAll(PDO::FETCH_OBJ);

//zona horaria para fechas
date_default_timezone_set('America/Cancun');

class PDF extends FPDF
{
   // Cabecera de página
   function Header()
   {
      $this->Image('img/denuncia.jpg', 250, 5, 40); //logo de la empresa,moverDerecha,moverAbajo,tamañoIMG
      $this->SetFont('Arial', 'B', 19); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(95); // Movernos a la derecha
      $this->SetTextColor(0, 0, 0); //color
      //creamos una celda o fila
      $this->Cell(110, 15, utf8_decode('SIS DENUNCIAS'), 1, 1, 'C', 0); // AnchoCelda,AltoCelda,titulo,borde(1-0),saltoLinea(1-0),posicion(L-C-R),ColorFondo(1-0)
      $this->Ln(3); // Salto de línea
      $this->SetTextColor(103); //color

      //Fecha
      $this->Cell(130);
      $this->SetFont('Arial', 'B', 12);
      $this->Cell(25, 5, "Fecha: " . date("d/m/Y"), 0, 1, '', 0);

      /* UBICACION */
      //$this->Cell(130);  // mover a la derecha
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(96, 10, utf8_decode("Ubicación: Av. Insurgentes No. 330, David G. Gutiérrez, Chetumal"), 0, 0, '', 0);
      $this->Ln(5);

      /* TELEFONO */
      //$this->Cell(130);  // mover a la derecha
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(59, 10, utf8_decode("Teléfono: 9831814345"), 0, 0, '', 0);
      $this->Ln(5);

      /* COREEO */
      //$this->Cell(130);  // mover a la derecha
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(85, 10, utf8_decode("Correo: denuncias@sisdenuncias.com"), 0, 0, '', 0);
      $this->Ln(10);

      /* TITULO DE LA TABLA */
      //color
      $this->SetTextColor(228, 100, 0);
      $this->Cell(100); // mover a la derecha
      $this->SetFont('Arial', 'B', 15);
      $this->Cell(100, 10, utf8_decode("REPORTE DE DENUNCIAS "), 0, 1, 'C', 0);
      $this->Ln(7);

      /* CAMPOS DE LA TABLA */
      //color
      $this->SetFillColor(228, 100, 0); //colorFondo
      $this->SetTextColor(255, 255, 255); //colorTexto
      $this->SetDrawColor(163, 163, 163); //colorBorde
      $this->SetFont('Arial', 'B', 11);
      $this->Cell(15, 10, utf8_decode('N°'), 1, 0, 'C', 1);
      $this->Cell(85, 10, utf8_decode('HECHOS'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('LUGAR'), 1, 0, 'C', 1);
      $this->Cell(40, 10, utf8_decode('FECHA'), 1, 0, 'C', 1);
      $this->Cell(70, 10, utf8_decode('RESPONSABLE'), 1, 0, 'C', 1);
      $this->Cell(20, 10, utf8_decode('VICTIMA'), 1, 1, 'C', 1);
   }

   // Pie de página
   function Footer()
   {
      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, negrita(B-I-U-BIU), tamañoTexto
      $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); //pie de pagina(numero de pagina)

      $this->SetY(-15); // Posición: a 1,5 cm del final
      $this->SetFont('Arial', 'I', 8); //tipo fuente, cursiva, tamañoTexto
      $hoy = date('d/m/Y');
      $this->Cell(540, 10, utf8_decode($hoy), 0, 0, 'C'); // pie de pagina(fecha de pagina)
   }
}

$pdf = new PDF();
$pdf->AddPage("landscape"); /* aqui entran dos para parametros (horientazion,tamaño)V->portrait H->landscape tamaño (A3.A4.A5.letter.legal) */
$pdf->AliasNbPages(); //muestra la pagina / y total de paginas

$i = 0;
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(163, 163, 163); //colorBorde

$i = $i + 1;
/* TABLA */
if ($query->rowCount() > 0) {
   foreach ($result as $fila) {
      $pdf->Cell(15, 10, $fila->idDenuncia, 1, 0, "C");
      $pdf->Cell(85, 10, mb_convert_encoding($fila->hechos, 'ISO-8859-1', 'UTF-8'), 1, 0, "C");
      $pdf->Cell(40, 10, mb_convert_encoding($fila->lugar, 'ISO-8859-1', 'UTF-8'), 1, 0, "C");
      $pdf->Cell(40, 10, $fila->fecha, 1, 0, "C");
      $pdf->Cell(70, 10, mb_convert_encoding($fila->responsable, 'ISO-8859-1', 'UTF-8'), 1, 0, "C");
      $pdf->Cell(20, 10, $fila->idVictima, 1, 1, "C");
   }
}

$pdf->Output('Reporte.pdf', 'I');//nombreDescarga, Visor(I->visualizar - D->descargar)
