<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<body>

<?php

include 'library.php';

if ($_GET['get_details'] !== NULL) {

  $id = (int)$_GET['id'];
  $result = get_details($id, $_GET['first_name'], $_GET['last_name'], $_GET['display_name'], $_GET['category']);

  foreach ($result as $row) {
    foreach ($row as $value) {
      echo $value . " ";
    }
    echo "<br>";
  }
} elseif ($_GET['get_details_company'] !== NULL) {

  $id = (int)$_GET['id'];
  $result = get_details_company($id, $_GET['display_name']);
  
  foreach ($result as $row) {
    foreach ($row as $value) {
      echo $value . " ";
    }
    echo "<br>";
  }
}

?>
</body>
</html> 
