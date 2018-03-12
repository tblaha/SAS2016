<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>

<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>

<body>

<?php

if ($_GET['present']){
    $presence = 1;
    $search = NULL;
} else if ($_GET['not_present']) { 
    $presence = 0;
    $search = NULL;
} else if ($_GET['search']) {
    $search = 1;
    $presence = NULL;
}

include '../library.php';

$id = (int)$_GET['id'];
$result = presence($id, $presence, $search, NULL);

echo "Folgende Meldungen zu diesem Nutzer wurden bisher protokolliert:<br>";
echo "<table>";
echo "<tr>";
echo "<th>ID</th>";
echo "<th>Bürger ID</th>";
echo "<th>Vorname</th>";
echo "<th>Nachname</th>";
echo "<th>Anwesenheit</th>";
echo "<th>Zeitstempel</th>";
echo "<th>Sachbearbeiter</th>";
echo "</tr>";
foreach ($result as $row) {
  $i = 0;
  echo "<tr>";
  foreach ($row as $value) {
    if ($i > 6){ break; }
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

