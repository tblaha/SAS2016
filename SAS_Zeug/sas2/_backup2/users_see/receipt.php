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
$result = generate_reciept($id, $_GET['cat'], $limit);

echo "<br>Der aktuelle Kontostand liegt bei: " . $result['account'] . "<br><br>";

echo "Folgende Kontobewegungen mit diesem Nutzer wurden bisher protokolliert:<br><br>";
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
  
?>

</body>
</html> 
