<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>

<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>
<b><h1>SUPERUSER</h1></b>
<body>

<?php

if ($_GET['missing']){
    $missing = 1;
}

include '../library.php';

$result = presence(NULL, NULL, NULL, $missing);

echo "<table>";
echo "<tr>";
echo "<th>Bürger ID</th>";
echo "<th>Vorname</th>";
echo "<th>Nachname</th>";
echo "<th>Anwesenheit</th>";
echo "</tr>";
foreach ($result as $row) {
  $i = 0;
  echo "<tr>";
  foreach ($row as $value) {
    if ($i > 3){ break; }
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

