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
} else { 
    $presence = 0;
}

include '../library.php';

$id = (int)$_GET['id'];
$result = presence($id, $presence);

echo "Folgende Meldungen zu diesem Nutzer wurden bisher protokolliert:<br>";
echo "<table>";
echo "<tr>";
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
    if ($i > 5){ break; }
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
