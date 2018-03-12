<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<body>

<?php

include '../library.php';

$id       = (int)$_GET['t_id'];
$amount   = (float)$_GET['amount'];

if ($_GET['buy']) {
    $result = pay_in_out("buy", $id, $_GET['cat'], $_GET['name'], $amount);
} else if ($_GET['sell']) {
    $result = pay_in_out("sell", $id, $_GET['cat'], $_GET['name'], $amount);
}

  echo "Aktion Erfolgreich!<br>";
  echo "Folgende Ein-/Auszahlungen fanden bisher auf dieses Konto statt:<br>";
  echo "<table>";
  echo "<tr>";
  echo "<th>Wechsel ID</th>";
  echo "<th>Tätiger Art</th>";
  echo "<th>Tätiger ID</th>";
  echo "<th>Tätiger Name</th>";
  echo "<th>Modus</th>";
  echo "<th>Gezahlter Betrag</th>";
  echo "<th>Nominale Wechselrate</th>";
  echo "<th>Gebühr-Abweichung</th>";
  echo "<th>Ausgezahlter Betrag</th>";
  echo "<th>Betrag an den Staat</th>";
  echo "<th>Zeitstempel</th>";
  echo "<th>Sachbearbeiter</th>";
  echo "</tr>";
  foreach ($result as $row) {
    $i = 0;
    echo "<tr>";
    foreach ($row as $value) {
      if ($i > 11){ break; }
      echo "<td>";
      echo $value . " ";
      echo "</td>";
      $i++;
    }
    echo "</tr>";
  }

  
?>

</body>
</html>
