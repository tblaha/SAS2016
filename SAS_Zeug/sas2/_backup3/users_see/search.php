<!DOCTYPE html>

<meta charset="UTF-8"> 

<head>
<link rel="stylesheet" type="text/css" href="/style.css">
</head>

<html>
<body>

<?php

include '../library.php';

if ($_GET['get_details'] !== NULL) {

  $id = (int)$_GET['id'];
  $result = get_details($id, $_GET['first_name'], $_GET['last_name'], $_GET['c_id']);

  echo "<br>Ihr Suche ergab Folgendes:<br><br>";
  echo "<table>";
  echo "<tr>";
  echo "<th>Bürger ID</th>";
  echo "<th>Vorname</th>";
  echo "<th>Nachname</th>";
  echo "<th>Firmen-ID</th>";
  echo "<th>Firmenname</th>";
  echo "<th>Kontostand</th>";
  foreach ($result as $row) {
    echo "<tr>";
    foreach ($row as $value) {
      echo "<td>";
      echo $value . " ";
      echo "</td>";
    }
    echo "</tr>";
  }
    echo "</table>";
} elseif ($_GET['get_details_company'] !== NULL) {

  $id = (int)$_GET['id'];
  $result = get_details_company($id, $_GET['display_name']);
  
  echo "<br>Ihr Suche ergab Folgendes:<br><br>";
  echo "<table>";
  echo "<tr>";
  echo "<th>Firmen ID</th>";
  echo "<th>Name</th>";
  echo "<th>Geschäftsführer-ID</th>";
  echo "<th>Vorname des Geschäftsführers</th>";
  echo "<th>Nachname des Geschäftsführers</th>";
  echo "<th>Kontostand</th>";
  foreach ($result as $row) {
    echo "<tr>";
    foreach ($row as $value) {
      echo "<td>";
      echo $value . " ";
      echo "</td>";
    }
    echo "</tr>";
}
  echo "</table>";
}


?>

</body>
<br><br>Das Bankensystem für Schule als Staat an der iDSB 2016. Geschrieben von Till Blaha
</html> 

