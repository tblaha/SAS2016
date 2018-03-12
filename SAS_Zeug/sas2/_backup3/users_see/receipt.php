<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>

<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>

<body>

<?php

include '../library.php';


$id    = (int)$_GET['id'];
$limit = (int)$_GET['limit'];
$result = generate_receipt($id, $_GET['cat'], $limit);

echo "<br>Der aktuelle Kontostand liegt bei: <b>" . $result['account'] . "</b><br><br>";

echo "Dies sind die neusten Kontobewegungen mit diesem Nutzer, die bisher protokolliert wurden:<br><br>";
echo "<table>";
echo "<tr>";
echo "<th>TransaktionID</th>";
echo "<th>Sender Art</th>";
echo "<th>Sender ID</th>";
echo "<th>Sender Name</th>";
echo "<th>Empfänger Art</th>";
echo "<th>Empfänger ID</th>";
echo "<th>Empfänger Name</th>";
echo "<th>Betrag</th>";
echo "<th>Überschriebener Betrag</th>";
echo "<th>Steuerbetrag</th>";
echo "<th>Steuersatz</th>";
echo "<th>Zeitstempel</th>";
echo "<th>Zweck</th>";
echo "<th>Sachbearbeiter</th>";
echo "<th>Rückgängig gemacht?</th>";
echo "</tr>";
foreach ($result['transactions'] as $row) {
  $i = 0;
  echo "<tr>";
  foreach ($row as $value) {
    if ($i > 14){ break; }
    echo "<td>";
    echo $value . " ";
    echo "</td>";
    $i++;
  }
  echo "</tr>";
}
echo "</table><br><br>";

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
  foreach ($result['pay_in_out'] as $row) {
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

  echo "</table><br><br>";

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
  foreach ($result['changes'] as $row) {
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
  echo "</table><br><br>";


  
?>

</body>
<br><br>Das Bankensystem für Schule als Staat an der iDSB 2016. Geschrieben von Till Blaha
</html> 
