<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<link rel="stylesheet" type="text/css" href="/style.css">
<body>

<?php

include '../library.php';

if ($_GET['add_user'] !== NULL) {

  $id     = (int)$_GET['id'];
  $result = add_user($id, $_GET['f_name'], $_GET['l_name'], $_GET['c_id']);

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
  
} elseif ($_GET['add_company'] !== NULL) {

  $id = (int)$_GET['id'];
  $result = add_company($id, $_GET['display_name'], $_GET['ceo_id']);
  
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
  echo "</table>";
}
  
}

?>

</body>
<br><br>Das Bankensystem für Schule als Staat an der iDSB 2016. Geschrieben von Till Blaha
</html> 

