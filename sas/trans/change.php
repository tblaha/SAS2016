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
$amount   = numfmt_parse($german, $_GET['amount']);

if ($_GET['buy']) {
    $result = change("buy", $id, $_GET['cat'], $_GET['name'], $amount);
} else if ($_GET['sell']) {
    $result = change("sell", $id, $_GET['cat'], $_GET['name'], $amount);
} else if ($_GET['revoke']) {
    $result = revoke_change($_GET['change_id'], NULL);
}

  echo "Aktion Erfolgreich!<br>";
  echo "Folgende Geldwechslungen wurden von Nutzer wurden bisher protokolliert:<br><br>";
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
  echo "<th>Überschriebener Betrag</th>";
  echo "<th>Betrag an den Staat</th>";
  echo "<th>Zeitstempel</th>";
  echo "<th>Sachbearbeiter</th>";
  echo "<th>Zurückgezogen</th>";
  echo "</tr>";
  foreach ($result as $row) {
    $i = 0;
    echo "<tr>";
    foreach ($row as $value) {
      if ($i > 12){ break; }
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

