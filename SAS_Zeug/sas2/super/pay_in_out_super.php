<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>

<?php

include '../library.php';

$t_id     = (int)$_GET['t_id'];
$amount   = numfmt_parse($german, $_GET['amount']);


$result = pay_in_out($_GET['mode'], $t_id, $_GET['t_cat'], $amount, 1);


  echo "Aktion Erfolgreich!<br>";
  echo "Folgende Einzahlungen/Abhebungen fanden bisher von diesem Bürger/Dieser Firma statt:<br>";
  echo "<table>";
  echo "<tr>";
  echo "<th>Ein/Auszahlungs ID</th>";
  echo "<th>Tätiger Art</th>";
  echo "<th>Tätiger ID</th>";
  echo "<th>Tätiger Name</th>";
  echo "<th>Modus</th>";
  echo "<th>Betrag</th>";
  echo "<th>Zeitstempel</th>";
  echo "<th>Sachbearbeiter</th>";
  echo "</tr>";
  foreach ($result as $row) {
    $i = 0;
    echo "<tr>";
    foreach ($row as $value) {
      if ($i > 7){ break; }
      echo "<td>";
      echo $value . " ";
      echo "</td>";
      $i++;
    }
    echo "</tr>";
  }

  echo "</table>";

?>

</body>
<br><br>Das Bankensystem für Schule als Staat an der iDSB 2016. Geschrieben von Till Blaha
</html> 

