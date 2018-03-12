<!DOCTYPE html>

<meta charset="UTF-8"> 

<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>

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
}

?>

</body>
</html> 
