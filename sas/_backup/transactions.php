<!DOCTYPE html>

<meta charset="UTF-8"> 

<html>
<body>

<?php

include 'library.php';

if ($_GET['transaction'] !== NULL) {

  $f_id   = (int)$_GET['f_id'];
  $t_id   = (int)$_GET['t_id'];
  $amount = (int)$_GET['amount'];
  
  $result = transaction($f_id, $t_id, $amount, $_GET['description']);
  
  if (!$result) {
    echo "Transaktion Erfolgreich!";
  } else {
    echo $result;
  }
} elseif ($_GET['generate_reciept'] !== NULL) {

  $id = (int)$_GET['id'];
  $result = generate_reciept($id, $_GET['limit']);
  
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
